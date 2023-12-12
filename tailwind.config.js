/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin')
module.exports = {
  content: [
    './app/resources/views/**/*.twig',
    './app/resources/scripts/**/*.ts',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui'),
    plugin(function ({ addUtilities }) {
      addUtilities({
        '.arrow-hide': {
          '-moz-appearance': 'textfield!important',
          '&::-webkit-inner-spin-button': {
            '-webkit-appearance': 'none',
            'margin': 0,
          },
          '&::-webkit-outer-spin-button': {
            '-webkit-appearance': 'none',
            'margin': 0
          },
        }
      }
      )
    })
  ],
  daisyui: {
    themes: ["cupcake"],
  }
}

