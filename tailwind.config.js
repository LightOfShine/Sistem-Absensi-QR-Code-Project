import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    safelist: [
        // Dynamic colors used in dashboard stats & timeline
        { pattern: /bg-(indigo|cyan|emerald|rose|orange|teal|amber|red|blue|purple|green|gray)-(50|100|200|400|500|600|700|800|900)/ },
        { pattern: /text-(indigo|cyan|emerald|rose|orange|teal|amber|red|blue|purple|green|gray)-(100|200|400|500|600|700|800)/ },
        { pattern: /border-(indigo|cyan|emerald|rose|orange|teal|amber|red|blue|purple|green|gray)-(100|200|400|500)/ },
        { pattern: /dark:bg-(indigo|cyan|emerald|rose|orange|teal|amber|red|blue|purple|green|gray)-(800|900|950)/ },
        { pattern: /dark:text-(indigo|cyan|emerald|rose|orange|teal|amber|red|blue|purple|green|gray)-(100|200|300|400)/ },
        'animate-spin', 'animate-pulse',
    ],
    theme: {
        extend: {
            colors: {
                gray: {
                    950: '#0a0a0f',
                }
            },
            fontFamily: {
                inter: ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [forms],
}

