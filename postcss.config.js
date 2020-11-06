module.exports = {
    plugins: {
        'postcss-import': {},
        'postcss-cssnext': {
            browsers: ['last 2 versions'],
        },
        'postcss-clean': {
            level: 2,
        },
    },
};
