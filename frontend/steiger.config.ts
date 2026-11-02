import { defineConfig } from 'steiger'
import fsd from '@feature-sliced/steiger-plugin'

export default defineConfig([
  ...fsd.configs.recommended,
  {
    // Early in the project most slices are single-use, which is fine.
    files: ['./app/layers/**'],
    rules: {
      'fsd/insignificant-slice': 'off',
      // `health` is uncountable and `session` is the agreed entity name, so a plural rule only adds noise.
      'fsd/inconsistent-naming': 'off',
      // Feature slices are verb-user by agreed naming (create-user, edit-user, delete-user).
      'fsd/repetitive-naming': 'off',
    },
  },
])
