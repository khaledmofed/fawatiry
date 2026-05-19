import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Support/InvoicePreviewThemes.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
                display: ['Inter', 'ui-sans-serif', 'system-ui', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                clay: {
                    primary: '#0a0a0a',
                    'primary-active': '#1f1f1f',
                    'primary-disabled': '#e5e5e5',
                    ink: '#0a0a0a',
                    body: '#3a3a3a',
                    'body-strong': '#1a1a1a',
                    muted: '#6a6a6a',
                    'muted-soft': '#9a9a9a',
                    hairline: '#e5e5e5',
                    'hairline-soft': '#f0f0f0',
                    canvas: '#fffaf0',
                    'surface-soft': '#faf5e8',
                    'surface-card': '#f5f0e0',
                    'surface-strong': '#ebe6d6',
                    'surface-dark': '#0a1a1a',
                    'surface-dark-elevated': '#1a2a2a',
                    'on-primary': '#ffffff',
                    'on-dark': '#ffffff',
                    'on-dark-soft': '#a0a0a0',
                    pink: '#ff4d8b',
                    teal: '#1a3a3a',
                    lavender: '#b8a4ed',
                    peach: '#ffb084',
                    ochre: '#e8b94a',
                    mint: '#a4d4c5',
                    coral: '#ff6b5a',
                    success: '#22c55e',
                    warning: '#f59e0b',
                    error: '#ef4444',
                },
            },
            borderRadius: {
                clay: '12px',
                'clay-lg': '16px',
                'clay-xl': '24px',
            },
            boxShadow: {
                'clay-soft': '0 4px 24px rgba(10, 10, 10, 0.06)',
                'clay-card': '0 8px 40px rgba(10, 10, 10, 0.08)',
                'clay-glass': '0 8px 32px rgba(10, 26, 26, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.06)',
            },
        },
    },

    plugins: [forms],
};
