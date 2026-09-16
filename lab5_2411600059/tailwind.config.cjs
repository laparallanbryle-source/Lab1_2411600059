const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe',
                    300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6',
                    600: '#6D57D6', 700: '#5b46b8', 800: '#4a3a96',
                    900: '#3b2d78',
                },
                accent: {
                    blue: '#586BD6', light: '#A372D6',
                    sky: '#5893D6', magenta: '#C161D6',
                },
            },
        },
    },
    plugins: [forms],
};