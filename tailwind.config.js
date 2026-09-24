/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{vue,js,ts}',
    './public/**/*.html',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          navy: "#08143a",
          dark: "#0a1744",
          blue: "#0062ff",
          hover: "#0051d4",
          light: "#edf4ff",
          subtle: "#f3f7fd"
        }
      },
      fontFamily: {
        body: ["Plus Jakarta Sans", "sans-serif"],
        display: ["Plus Jakarta Sans", "sans-serif"],
        mono: ["JetBrains Mono", "monospace"]
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
