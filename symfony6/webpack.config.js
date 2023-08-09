const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/login', ['./assets/css/login.css'])
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();