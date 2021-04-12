module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        extend: {
            colors: {
                primary: {
                    'light': '#ed5ba4',
                    'DEFAULT': '#b72375',
                    'dark': '#830049',
                }
            },
        },
    },
    variants: {
        backgroundColor: ['responsive', 'hover', 'focus', 'active'],
        extend: {
            backgroundColor: ['group-focus'],
            textColor: ['group-focus']
        },
    },
    plugins: [],
}
