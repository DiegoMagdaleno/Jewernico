/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/resources/views/**/*.twig',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui')
  ],
}

