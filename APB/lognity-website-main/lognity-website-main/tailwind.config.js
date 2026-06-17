import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Penting untuk fitur Day/Night
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Kita akan pakai font yang lebih bulat & friendly nanti
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palet Warna LOGNITY (Fun & Modern)
                lognity: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8', // Sky Blue
                    500: '#0ea5e9',
                    600: '#0284c7', // Primary Brand Color
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                },
                fun: {
                    pink: '#f472b6',
                    purple: '#a78bfa',
                    yellow: '#fde047',
                    lime: '#bef264',
                },
                dark: {
                    bg: '#0f172a', // Deep Slate (Lebih estetik dari hitam pekat)
                    card: '#1e293b',
                    text: '#e2e8f0',
                }
            },
            animation: {
                'blob': 'blob 7s infinite',
            },
            keyframes: {
                blob: {
                    '0%': { transform: 'translate(0px, 0px) scale(1)' },
                    '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                    '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                    '100%': { transform: 'translate(0px, 0px) scale(1)' },
                }
            }
        },
    },

    plugins: [forms],
};