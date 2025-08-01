import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

export default defineConfig({
  root: path.resolve(__dirname, 'src'),
  plugins: [react(), tailwindcss()],
  build: {
    emptyOutDir: true,
    outDir: path.resolve(__dirname, 'build'),
    manifest: 'manifest.json',
    rollupOptions: {
      input: path.resolve(__dirname, 'src', 'main.jsx'),
    },
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
})
