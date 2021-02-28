const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
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
    // customForms: (theme) => ({
    //   default: {
    //     input: {
    //       borderColor: theme('colors.cool-gray[300]'),
    //     },
    //     textarea: {
    //       borderColor: theme('colors.cool-gray[300]'),
    //     },
    //     checkbox: {
    //       borderColor: theme('colors.cool-gray[400]'),
    //     },
    //     radio: {
    //       borderColor: theme('colors.cool-gray[400]'),
    //     },
    //   },
    // }),
  },
  variants: {
    fill: ['responsive', 'hover', 'focus', 'group-hover'],
    opacity: ['responsive', 'hover', 'group-hover'],
    textColor: ['responsive', 'hover', 'focus', 'group-hover'],
    zIndex: ['responsive', 'focus'],
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
};
