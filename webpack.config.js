const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        main: './app/resources/scripts/main.ts',
        login: './app/resources/scripts/login.ts',
        admin_products: './app/resources/scripts/admin_products.ts',
        footer: './app/resources/scripts/footer.ts',
        register: './app/resources/scripts/register.ts',
        recover: './app/resources/scripts/recover.ts',
        admin_handle_product: './app/resources/scripts/admin_handle_product.ts',
        products: "./app/resources/scripts/products.ts",
        contact: "./app/resources/scripts/contact.ts",
    },
    output: {
        path: path.resolve(__dirname, 'public', 'dist'),
        filename: '[name].js',
    },
    resolve: {
        extensions: ['.ts', '.js'],
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                use: 'ts-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.scss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'postcss-loader',
                    'sass-loader',
                ],
            },
            {
                test: /\.css$/,
                use: [
                    'css-loader',
                    'postcss-loader',
                ],
            }
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'main.css',
        }),
    ],
};
