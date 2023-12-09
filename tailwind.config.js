/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/resources/views/**/*.twig',
    './app/resources/scripts/**/*.ts',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui')
  ],
  daisyui: {
    themes: ["cupcake"],
  }
}

