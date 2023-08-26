const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addEntry('js/likes', './assets/js/likes.js')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/login', ['./assets/css/login.css'])
    .addStyleEntry('css/likes', ['./assets/css/likes.css'])
    .disableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();