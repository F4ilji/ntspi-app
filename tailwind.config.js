import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import daisyui from "daisyui";

/** @type {import('tailwindcss').Config} */

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/preline/dist/*.js',
        "./node_modules/flowbite/**/*.js"

    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'secondAzure': '#26ACB8',
                'secondDarkBlue': '#2C6288',
                'primaryBlue': '#1E57A3',
                'primaryRed': '#C52227',

            },
        },
    },

    plugins: [
        forms,
        require('preline/plugin'),
        require('flowbite/plugin')
    ],
    darkMode: 'class',

};
