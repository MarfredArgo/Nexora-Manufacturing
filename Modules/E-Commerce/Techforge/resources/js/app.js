import './bootstrap';

// Process all images in resources/img for Vite manifest
import.meta.glob([
    '../img/**',
], { eager: true });
