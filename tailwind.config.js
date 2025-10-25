/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/**/*.php",
    "./storage/framework/views/*.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      opacity: {
        '15': '0.15',
        '25': '0.25',
        '35': '0.35',
        '45': '0.45',
        '55': '0.55',
        '65': '0.65',
        '85': '0.85',
      },
      backgroundColor: {
        'black/30': 'rgb(0 0 0 / 0.3)',
        'black/50': 'rgb(0 0 0 / 0.5)',
        'black/60': 'rgb(0 0 0 / 0.6)',
        'black/70': 'rgb(0 0 0 / 0.7)',
        'gray-900/50': 'rgb(17 24 39 / 0.5)',
        'gray-900/60': 'rgb(17 24 39 / 0.6)',
      }
    },
  },
  plugins: [],
}
