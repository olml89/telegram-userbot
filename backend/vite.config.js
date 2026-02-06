import { defineConfig } from 'vite';
import symfony from 'vite-plugin-symfony';

export default defineConfig(({ command }) => {
    const isDev = command === 'serve';

    return {
        plugins: [
            symfony({
                input: {
                    app: './assets/app.js',
                },
                buildDirectory: 'build/.vite',
            }),
        ],
        base: isDev ? '/' : '/build/',
        build: {
            outDir: 'public/build',
            emptyOutDir: true,
            rollupOptions: {
                input: {
                    app: './assets/app.js',
                },
            },
        },
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            origin: 'http://localhost:5173',
            hmr: {
                host: 'localhost',
                port: 5173,
            },
        },
    };
});
