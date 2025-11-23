/** @type {import('tailwindcss').Config} */
export default {
  content: ['./src/**/*.{html,js,svelte,ts}'],
  theme: {
    extend: {
      colors: {
        // DijitalMentor Brand Colors
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          300: '#93c5fd',
          400: '#60a5fa',
          500: '#3b82f6',
          600: '#2563eb', // DijitalMentor Mavisi (Main)
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
          DEFAULT: '#2563eb',
        },
        secondary: {
          50: '#fffbeb',
          100: '#fef3c7',
          200: '#fde68a',
          300: '#fcd34d',
          400: '#fbbf24',
          500: '#f59e0b', // Enerji Turuncusu
          600: '#d97706',
          700: '#b45309',
          DEFAULT: '#f59e0b',
        },
        success: {
          DEFAULT: '#10b981',
          50: '#ecfdf5',
          600: '#10b981',
        },
        error: {
          DEFAULT: '#ef4444',
          50: '#fef2f2',
          600: '#ef4444',
        },
        // Neutral/Gray palette
        gray: {
          50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937', // Karbon Siyah
          900: '#111827',
        }
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
      },
      fontSize: {
        // Typography Scale per Branding Kit
        'hero': ['3rem', { lineHeight: '1.1', fontWeight: '800' }],      // 48px
        'page': ['2.25rem', { lineHeight: '1.2', fontWeight: '700' }],   // 36px
        'section': ['1.875rem', { lineHeight: '1.3', fontWeight: '600' }], // 30px
        'card': ['1.5rem', { lineHeight: '1.4', fontWeight: '600' }],    // 24px
        'small-heading': ['1.25rem', { lineHeight: '1.5', fontWeight: '600' }], // 20px
      },
      boxShadow: {
        'card': '0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06)',
        'card-hover': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
      },
      borderRadius: {
        'card': '0.75rem', // 12px
      }
    },
  },
  plugins: [],
}
