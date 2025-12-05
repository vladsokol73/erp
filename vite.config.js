import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";

export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        laravel({
            input: [
                "resources/css/globals.css",
                //"resources/css/app.css",
                //"resources/css/fancybox.css",
                //"resources/css/swiper-bundle.min.css",

                "resources/js/App.tsx",
            ],
            refresh: true,
        }),
        react(),
    ],
});
