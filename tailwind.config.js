/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.vue',
    './resources/**/*.js',
    './resources/**/*.ts',
  ],
          // ✅ prevent class conflicts with Vuetify
  corePlugins: {
    preflight: false,      // ✅ don’t override Vuetify’s reset
  },
  theme: {
    extend: {},
  },
  plugins: [],
}
