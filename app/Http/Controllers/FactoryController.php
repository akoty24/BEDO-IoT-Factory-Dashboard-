<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FactoryController extends Controller
{
    public function dashboard(): View
    {
        return view('factory.dashboard');
    }

    public function manual(): View
    {
        return view('factory.manual', [
            'steps' => config('factory.steps'),
            'outputs' => config('factory.outputs'),
            'sensors' => $this->sensors(),
        ]);
    }

    public function automatic(): View
    {
        return view('factory.automatic', [
            'sensors' => $this->sensors(),
        ]);
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    private function sensors(): array
    {
        return collect(config('factory.inputs'))
            ->map(fn (string $label, string $id) => ['id' => $id, 'name' => $label])
            ->values()
            ->all();
    }
}
