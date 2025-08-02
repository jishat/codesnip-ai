import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'
import path from 'path'
import { fileURLToPath } from 'url'
import { humanId } from 'human-id'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

const folderName = path.basename(process.cwd())
const PLUGIN_SLUG = 'codesnip-ai'
const codeName = humanId({ capitalize: false, separator: '-' })

export default defineConfig({
  assetsDir: 'assets',
  base: `/wp-content/plugins/${folderName}/frontend/`,
  build: {
    emptyOutDir: true,
    outDir: `../assets`,
    rollupOptions: {
      input: path.resolve(import.meta.dirname, 'frontend/src/main.tsx'),
      output: {
        assetFileNames: fInfo => {
          const pathArr = fInfo?.name?.split('/')
          const fileName = pathArr?.at(-1)

          if (fileName === 'main.css') {
            return `main-${PLUGIN_SLUG}-ba-assets-${codeName}.css`
          }

          if (fileName === 'logo.svg') {
            return `logo.svg`
          }

          return `${PLUGIN_SLUG}-ba-assets-${hash()}.[ext]`
        },
        chunkFileNames: fInfo => {
          if (fInfo?.facadeModuleId?.includes('lucide-react')) {
            return `icons/[name]-icon-[hash].js`
          }
          return `[name]-[hash].js`
        },
        entryFileNames: `main-${codeName}.js`,
        generatedCode: {
          arrowFunctions: true,
          constBindings: true,
          objectShorthand: true,
          preset: 'es2015'
        }
      }
    }
  },

  root: 'frontend',
  plugins: [react({jsxRuntime: 'automatic'}), tailwindcss()],
  server: {
    cors: true, // required to load scripts from custom host
    hmr: { host: 'localhost' },
    port: 5173,
    strictPort: true // strict port to match on PHP side
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./frontend/src"),
    },
  },
})
