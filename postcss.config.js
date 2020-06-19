module.exports = {
    plugins: {
        'postcss-import': {},
        'postcss-cssnext': {
            browsers: ['last 2 versions', '> 5% in BE'],
        },
        'postcss-clean': {
            level: 2,
        },
    },
};
