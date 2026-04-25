/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php",
    "./apis/**/*.php",
    "./datos/**/*.php",
    "./negocio/**/*.php",
    "./presentacion/**/*.php",
    "./recursos/**/*.php",
    "./recursos/**/*.js",
  ],
  darkMode: "class",
  theme: {
    extend: {
      "colors": {
          "primary": "#7C3AED",
          "primary-container": "#6D28D9",
          "on-primary": "#ffffff",
          "secondary": "#1E40AF",
          "accent": "#F59E0B",
          "background": "#F9FAFB",
          "surface": "#FFFFFF",
          "surface-bright": "#F9FAFB",
          "surface-container-lowest": "#FFFFFF",
          "surface-container-low": "#F9FAFB",
          "surface-container": "#F3F4F6",
          "surface-container-high": "#E5E7EB",
          "surface-container-highest": "#D1D5DB",
          "surface-variant": "#F3F4F6",
          "on-surface": "#111827",
          "on-background": "#111827",
          "on-surface-variant": "#6B7280",
          "outline": "#9CA3AF",
          "outline-variant": "#E5E7EB",
          "error": "#DC2626",
          "error-container": "#FEE2E2",
          "tertiary": "#F59E0B",
          "tertiary-container": "#D97706",
          "tertiary-fixed": "#FEF3C7",
          "on-tertiary": "#FFFFFF",
          "on-tertiary-fixed": "#78350F",
          "on-tertiary-container": "#92400E",
          "inverse-surface": "#111827",
          "inverse-on-surface": "#F9FAFB",
          "inverse-primary": "#C4B5FD",
          "surface-tint": "#7C3AED",
          "surface-dim": "#E5E7EB"
      },
      "borderRadius": {
          "DEFAULT": "0.75rem",
          "lg": "0.75rem",
          "xl": "1.5rem",
          "full": "9999px"
      },
      "fontFamily": {
          "headline": ["Inter", "sans-serif"],
          "body": ["Inter", "sans-serif"],
          "label": ["Inter", "sans-serif"]
      }
    },
  },
  plugins: [],
}
