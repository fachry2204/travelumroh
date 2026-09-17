import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8080',
        changeOrigin: true,
      },
      '/storage': {
        target: 'http://127.0.0.1:8080',
        changeOrigin: true,
      },
    },
  },
  plugins: [
    laravel({
      input: ['resources/js/main.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    VitePWA({
      registerType: 'autoUpdate',
      devOptions: {
        enabled: false,
      },
      manifest: {
        name: 'Sistem Travel Umroh',
        short_name: 'Travel Umroh',
        description: 'Aplikasi Manajemen Travel Umroh Terpadu',
        theme_color: '#38BDF8',
        icons: [
          {
            src: 'pwa-icon.svg',
            sizes: '192x192 512x512',
            type: 'image/svg+xml',
          },
        ],
      },
    }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
    },
  },
});
