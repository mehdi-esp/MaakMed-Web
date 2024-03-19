/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1DD3B0',
        secondary: '#086375',
        accent: '#AFFC41',
        light: '#B2FF9E',
        background: '#EDEDED',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/container-queries'),
    require("daisyui"),
  ],
  daisyui: {
    themes: [
      "light",
      {
        // XXX: Subject to change
        maakmed: {
          primary: '#1DD3B0',
          secondary: '#086375',
          accent: '#AFFC41',
          neutral: "#061b22",
          background: '#EDEDED',
          info: "#00b9fe",
          success: "#00ffce",
          warning: "#a98a00",
          error: "#ff005e",
        },
      }
    ]
  }
}


/* 
 * Our colors
 *
 * #1DD3B0
 * #086375
 * #AFFC41
 * #B2FF9E
 * #EDEDED
 *
 * */
