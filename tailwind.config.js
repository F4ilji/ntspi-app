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
                'primaryBlue': '#2D4191',
                'primaryRed': '#E7544A',
                primary: {
                    DEFAULT: '#2D4191',
                    light: '#4A5FA3',
                    lighter: '#6F7FB7',
                    dark: '#233376',
                    hover: '#3A52B0',
                    active: '#1E2F6D',
                },
                secondary: {
                    DEFAULT: '#E7544A',
                    light: '#EF7A72',
                    lighter: '#F7A099',
                    dark: '#D43E34',
                    hover: '#E6635A',
                    active: '#CC3A30',
                },
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
