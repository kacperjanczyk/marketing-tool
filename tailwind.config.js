/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
      "./templates/**/*.twig",
      "./assets/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [
      require('flowbite/plugin')
  ],
  darkMode: 'class'
}
