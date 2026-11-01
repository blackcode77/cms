import { defineConfig } from 'steiger'
import fsd from '@feature-sliced/steiger-plugin'

export default defineConfig([
  ...fsd.configs.recommended,
  {
    // Early in the project most slices are single-use, which is fine.
    files: ['./app/layers/**'],
    rules: {
      'fsd/insignificant-slice': 'off',
    },
  },
])
