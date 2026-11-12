// @ts-check
import withNuxt from './.nuxt/eslint.config.mjs'

export default withNuxt(
    // Prettier decides self-closing tags (`<img />`), so the stylistic rule would only conflict with it.
    {
        rules: {
            'vue/html-self-closing': 'off',
        },
    },
)
