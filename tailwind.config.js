import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

export default {
  content: [
    "./vendor/usernotnull/tall-toasts/config/**/*.php",
    "./vendor/usernotnull/tall-toasts/resources/views/**/*.blade.php",
    "./vendor/wire-elements/modal/resources/views/*.blade.php",
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  safelist: [
    'sm:max-w-sm',
    'sm:max-w-md',
    'sm:max-w-2xl',
    'md:max-w-lg',
    'md:max-w-xl',
    'lg:max-w-2xl',
    'lg:max-w-3xl',
    'xl:max-w-4xl',
    'xl:max-w-5xl',
    '2xl:max-w-6xl',
    '2xl:max-w-7xl',
  ],
  theme: {
    extend: {
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
        '108': '27rem',
        '120': '30rem',
        '132': '33rem',
        '144': '36rem',
      },
      fill: (theme) => theme('colors'),
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  variants: {
    fill: ['responsive', 'hover', 'focus', 'group-hover'],
    opacity: ['responsive', 'hover', 'group-hover'],
    textColor: ['responsive', 'hover', 'focus', 'group-hover'],
    zIndex: ['responsive', 'focus'],
  },
  plugins: [forms],
}
