<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FactoryPagesTest extends TestCase
{
    public static function pages(): array
    {
        return [
            'dashboard' => ['/', 'Line Status'],
            'manual' => ['/manual', 'Solid Plastic Valve'],
            'automatic' => ['/automatic', 'Automatic Sequence Control'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_renders_with_shared_layout(string $uri, string $expectedText): void
    {
        $this->get($uri)
            ->assertOk()
            ->assertSee($expectedText)
            ->assertSee('BEDO IOT Factory System')
            ->assertSee('VERSION 3.0.0');
    }

    public function test_sidebar_marks_current_page_as_active(): void
    {
        $html = $this->get('/manual')->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#href="'.preg_quote(route('factory.manual'), '#').'"\s+aria-current="page"#',
            $html
        );
        $this->assertSame(1, substr_count($html, 'aria-current="page"'));
    }

    public function test_sidebar_start_and_stop_send_machine_commands(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee("@click=\"sendMachineCommand('start'); sidebarOpen = false\"", false)
            ->assertSee("@click=\"sendMachineCommand('stop'); sidebarOpen = false\"", false)
            ->assertSee('Start Homing')
            ->assertSee('Stop Process');
    }

    public function test_stop_button_is_never_disabled(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // STOP: "false && …" → never disabled. START: needs REMOTE / no pending command.
        $this->assertStringContainsString(':disabled="false && (pendingCommand !== null || !canControl)"', $html);
        $this->assertStringContainsString(':disabled="true && (pendingCommand !== null || !canControl)"', $html);
    }

    public function test_trainer_mqtt_settings_are_passed_to_the_browser(): void
    {
        config([
            'factory.mqtt.ws_url' => 'ws://10.0.0.5:9001',
            'factory.mqtt.username' => 'web',
            'factory.mqtt.root' => 'factory',
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        // @js() escapes quotes and slashes inside the page, so match loosely.
        $this->assertStringContainsString('10.0.0.5:9001', $html);
        $this->assertMatchesRegularExpression('/root.{1,16}factory/', $html);
        $this->assertMatchesRegularExpression('/username.{1,16}web/', $html);
    }

    public function test_manual_page_uses_trainer_output_and_step_names(): void
    {
        $response = $this->get('/manual')->assertOk();

        foreach (array_keys(config('factory.outputs')) as $name) {
            $response->assertSee("toggleOutput(&#039;{$name}&#039;)", false);
        }
        foreach (array_keys(config('factory.steps')) as $step) {
            $response->assertSee("runStep(&#039;{$step}&#039;)", false);
        }
    }

    public function test_manual_page_renders_all_sensors_by_trainer_input_name(): void
    {
        $response = $this->get('/manual')->assertOk();

        foreach (config('factory.inputs') as $id => $label) {
            $response->assertSee("isActive('{$id}')", false);
            $response->assertSee($label);
        }
    }

    public function test_automatic_page_sends_auto_commands(): void
    {
        $this->get('/automatic')
            ->assertOk()
            ->assertSee("autoCommand('start')", false)
            ->assertSee("autoCommand('feed')", false);
    }

    public function test_removed_pages_are_gone(): void
    {
        $this->get('/homing')->assertNotFound();
        $this->get('/stop')->assertNotFound();
        $this->get('/remote')->assertNotFound();
    }

    public function test_layout_has_dark_mode_toggle(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee("document.documentElement.classList.toggle('dark', dark)", false)
            ->assertSee('@click="toggleDarkMode()"', false)
            ->assertSee('aria-label="Toggle dark mode"', false);
    }

    public function test_page_loads_no_assets_from_the_internet(): void
    {
        // The factory network has no reliable internet: everything is bundled by Vite.
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertDoesNotMatchRegularExpression('#(src|href)="https?://(?!localhost)#', $html);
        $this->assertStringNotContainsString('cdn.', $html);
        $this->assertStringNotContainsString('fonts.googleapis', $html);
    }
}
