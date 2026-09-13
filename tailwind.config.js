import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
        },
    },

    corePlugins: {
        // Tailwind's `.collapse` utility (visibility: collapse, for table rows)
        // shares its class name with Bootstrap's `.collapse` component (used by
        // accordions/modals on the public pages that load both frameworks).
        // Since neither app currently uses Tailwind's visible/invisible/collapse
        // utilities, disabling them avoids Tailwind's rule winning the cascade
        // and leaving Bootstrap-collapsed content permanently invisible.
        visibility: false,
    },

    plugins: [forms],
};
