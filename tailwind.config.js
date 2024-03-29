/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  safelist: [
    {
      pattern: /alert-(info|success|warning|error)/,
    },
  ],
  theme: {
  },
  plugins: [
    require('@tailwindcss/forms')({ strategy: 'class' }),
    require('@tailwindcss/typography'),
    require('@tailwindcss/container-queries'),
    require("daisyui"),
  ],
  daisyui: {
    themes: [
      {
        maakmed: {
          primary: '#38b2ac',
          "primary-content": '#EDEDED',
          secondary: '#7b92b2',
          "secondary-content": '#EDEDED',
          neutral: "#212121",
          "neutral-content": "#EDEDED",
          "base-100": "oklch(100% 0 0)",
          "base-content": "#181a2a",
          accent: '#5c99d6',
          "accent-content": '#edf2f7',

          "error-content": 'oklch(100% 0 0)',
          "success-content": 'oklch(100% 0 0)',
          // "warning-content": '#EDEDED',
          // "info-content": '#EDEDED',
        },
      },
      "light"
    ]
  }
}
