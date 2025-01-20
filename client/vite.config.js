import {defineConfig} from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path';

export default defineConfig({
    plugins: [vue()],
    server: {
        port: 3000,
        proxy: {
            '/api': {
                target: 'http://localhost:8000',
                changeOrigin: true,
                secure: false,
                ws: true
            },
            '/sanctum': {
                target: 'http://localhost:8000/sanctum',
                changeOrigin: true,
                secure: false,
                ws: true
            }
        }
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./"),
        },
    },
})
