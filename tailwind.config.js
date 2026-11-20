import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                    main: 'rgb(var(--color-brand-main) / <alpha-value>)',
                    secondary: 'rgb(var(--color-brand-secondary) / <alpha-value>)',
                    third: 'rgb(var(--color-brand-third) / <alpha-value>)',
                    dark: 'rgb(var(--color-brand-dark) / <alpha-value>)',
                    light: 'rgb(var(--color-brand-light) / <alpha-value>)',
                    sidebar: 'rgb(var(--color-brand-sidebar, var(--color-brand-main)) / <alpha-value>)',
                    active: 'rgb(var(--color-brand-active, var(--color-brand-main)) / <alpha-value>)',
                    accent: 'rgb(var(--color-brand-accent, var(--color-brand-third)) / <alpha-value>)',
                }
            }
        },
    },

    plugins: [forms],
};
