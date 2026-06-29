import { ConfigEnv, defineConfig, UserConfig } from 'vite';
import symfony from 'vite-plugin-symfony';

export default defineConfig(( configEnv: ConfigEnv): UserConfig => {
    const config = {
        plugins: [
            symfony({
                stimulus: false,
                refresh: true,
            }),
        ],

        base: configEnv.command === 'build' ? '/assets/' : '/',

        build: {
            outDir: 'public/build',
            emptyOutDir: true,
            assetsDir: 'assets',
            rollupOptions: {
                input: {
                    app: './assets/app.ts',
                }
            },
        },

        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: true,
            hmr: {
                host: 'localhost',
            },
        },
    };

    return config as any;
});
