import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    server: {
        host: '127.0.0.1',
    },
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/fancybox.css",
                "resources/css/swiper-bundle.min.css",
            ],
            refresh: true,
        }),
    ],
});
