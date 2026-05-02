import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // server: {
    //     host: '0.0.0.0', // Listen on all available IPs
    //     hmr: {
    //         host: '192.168.1.17', // Replace with your computer's IP
    //         protocol: 'ws', // Use WebSocket for HMR
    //     },
    // },
});
