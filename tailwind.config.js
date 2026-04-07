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
                    50: '#EEF0F8',
                    100: '#DDE0F1',
                    200: '#BBC1E3',
                    500: '#2D4191',
                    700: '#233376',
                },
                secondary: {
                    DEFAULT: '#E7544A',
                    light: '#EF7A72',
                    lighter: '#F7A099',
                    dark: '#D43E34',
                    hover: '#E6635A',
                    active: '#CC3A30',
                },
                foreground: {
                    DEFAULT: '#0F172A',
                },
                // Семантические цвета для Dashboard
                layer: {
                    DEFAULT: '#FFFFFF',
                    foreground: '#0F172A',
                },
                surface: {
                    DEFAULT: '#F8FAFC',
                    muted: '#F1F5F9',
                },
                'layer-line': '#E2E8F0',
                'line-2': '#CBD5E1',
                'muted-foreground': {
                    1: '#64748B',
                    2: '#94A3B8',
                },
                'muted-hover': '#F1F5F9',
                'background-2': '#F8FAFC',
                success: {
                    DEFAULT: '#059669',
                    light: '#10B981',
                    dark: '#047857',
                },
                danger: {
                    DEFAULT: '#DC2626',
                    light: '#EF4444',
                    dark: '#B91C1C',
                },
                warning: {
                    DEFAULT: '#D97706',
                    light: '#F59E0B',
                    dark: '#B45309',
                },
                info: {
                    DEFAULT: '#0284C7',
                    light: '#0EA5E9',
                    dark: '#0369A1',
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
