import '@fontsource/inter/400.css';
import '@fontsource/inter/500.css';
import '@fontsource/inter/600.css';
import '@fontsource/inter/700.css';
import '@fontsource/inter/800.css';

import Alpine from 'alpinejs';
import { factoryDashboard } from './factory-dashboard';

// Used by x-data="factoryDashboard(...)" in layouts/factory.blade.php.
window.factoryDashboard = factoryDashboard;
window.Alpine = Alpine;

Alpine.start();
