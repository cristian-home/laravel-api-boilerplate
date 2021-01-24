module.exports = {
    env: {
        browser: true,
        es2021: true,
        node: true
    },
    extends: ['eslint:recommended', 'plugin:vue/essential'],
    parserOptions: {
        ecmaVersion: 12,
        sourceType: 'module'
    },
    plugins: ['vue'],
    rules: {
        // allow async-await
        'generator-star-spacing': 'off',
        // allow debugger during development
        'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        'vue/singleline-html-element-content-newline': 'off',
        'vue/multiline-html-element-content-newline': 'off',
        'vue/max-attributes-per-line': [
            'error',
            {
                singleline: 6
            }
        ],
        'vue/html-self-closing': [
            'error',
            {
                html: {
                    void: 'any',
                    normal: 'always',
                    component: 'any'
                },
                svg: 'always',
                math: 'always'
            }
        ],
        'no-console': 'off',
        'vue/html-indent': ['error', 4]
    }
};
