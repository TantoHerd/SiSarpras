// tailwind.config.js
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
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Meta Design System Tokens
                primary: {
                    DEFAULT: '#0064e0',    // Cobalt - buy CTA
                    deep: '#0457cb',        // Pressed
                    soft: '#0091ff',        // Tint
                },
                ink: {
                    DEFAULT: '#1c1e21',     // Standard body
                    deep: '#0a1317',        // Headline
                    button: '#000000',      // Marketing CTA
                },
                charcoal: '#444950',
                slate: '#4b4c4f',
                steel: '#5d6c7b',
                stone: '#8595a4',
                canvas: '#ffffff',
                surface: {
                    soft: '#f1f4f7',
                },
                hairline: {
                    DEFAULT: '#ced0d4',
                    soft: '#dee3e9',
                },
                fb: '#1876f2',
                meta: {
                    link: '#385898',
                },
                oculus: '#a121ce',

                // Semantic
                success: '#31a24c',
                attention: '#f2a918',
                warning: '#f7b928',
                critical: {
                    DEFAULT: '#e41e3f',
                    strong: '#f0284a',
                },
                disabled: '#bcc0c4',
            },

            borderRadius: {
                'xs': '2px',
                'sm': '4px',
                'md': '6px',
                'lg': '8px',
                'xl': '16px',
                '2xl': '24px',
                '3xl': '32px',
                'feature': '40px',
                'pill': '100px',
                'circle': '9999px',
            },

            spacing: {
                'xxs': '4px',
                'xs': '8px',
                'sm': '10px',
                'md': '12px',
                'base': '16px',
                'lg': '20px',
                'xl': '24px',
                'xxl': '32px',
                'xxxl': '40px',
                'section-sm': '48px',
                'section': '64px',
                'section-lg': '80px',
            },

            fontSize: {
                'hero': ['64px', { lineHeight: '1.16', fontWeight: '500' }],
                'display-lg': ['48px', { lineHeight: '1.17', fontWeight: '500' }],
                'heading-lg': ['36px', { lineHeight: '1.28', fontWeight: '500' }],
                'heading-md': ['28px', { lineHeight: '1.21', fontWeight: '300' }],
                'heading-sm': ['24px', { lineHeight: '1.25', fontWeight: '500' }],
                'subtitle-lg': ['18px', { lineHeight: '1.44', fontWeight: '700' }],
                'subtitle-md': ['18px', { lineHeight: '1.44', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '1.50', letterSpacing: '-0.16px', fontWeight: '400' }],
                'body-md-bold': ['16px', { lineHeight: '1.50', letterSpacing: '-0.16px', fontWeight: '700' }],
                'body-sm': ['14px', { lineHeight: '1.43', letterSpacing: '-0.14px', fontWeight: '400' }],
                'body-sm-bold': ['14px', { lineHeight: '1.43', letterSpacing: '-0.14px', fontWeight: '700' }],
                'caption': ['12px', { lineHeight: '1.33', fontWeight: '400' }],
                'caption-bold': ['12px', { lineHeight: '1.33', fontWeight: '700' }],
            },

            boxShadow: {
                'subtle': 'rgba(0, 0, 0, 0.2) 1px 1px 0px 0px',
                'sticky': 'rgba(20, 22, 26, 0.3) 0px 1px 4px 0px',
            },
        },
    },

    plugins: [forms],
};