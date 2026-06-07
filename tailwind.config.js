/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/views/**/*.twig',
    './public/assets/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        mono: ['JetBrains Mono', 'Fira Code', 'Cascadia Code', 'Consolas', 'monospace'],
        sans: ['Inter', 'system-ui', 'sans-serif'],
      }
    }
  },
  plugins: [],
}
