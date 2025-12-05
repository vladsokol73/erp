module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
        {
            pattern: /(bg|text|border)-(red|crimson|rose|pink|magenta|fuchsia|purple|violet|indigo|blue|azure|sky|cyan|teal|mint|emerald|green|lime|chartreuse|yellow|amber|orange|tangerine|salmon|lightpink|lavender|lightblue|lightcyan|lightgreen|lightyellow|peach|coral|gray)-(100|200|300|400|500|600)/,
        }
    ],
    darkMode: "class",
    theme: {
        container: {
            center: true,
        },
        extend: {
            fontFamily: {
                inter: ["Inter", "sans-serif"],
            },
            colors: {
                transperent: "transperent",
                current: "currentColor",
                black: "#1c1c1c",
                lightred: "#FF4747",
                lightyellow: "#FFE999",
                lightwhite: "#F7F9FB",
                lightblue: {
                    100: "#E3F5FF",
                    200: "#B1E3FF",
                    300: "#A8C5DA",
                },
                lightpurple: {
                    100: "#E5ECF6",
                    200: "#C6C7F8",
                    300: "#95A4FC",
                },
                lightgreen: {
                    100: "#BAEDBD",
                    200: "#A1E3CB",
                },
            },
            boxShadow: {
                "3xl": "0 0 16px rgb(0 0 0 / 10%)",
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms")({
            strategy: "base", // only generate global styles
        }),
        require("tailwind-scrollbar"),
    ],
};
