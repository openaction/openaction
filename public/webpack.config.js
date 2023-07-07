const Encore = require('@symfony/webpack-encore');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .addEntry('aos', './assets/aos.js')
    .addEntry('turbo', './assets/turbo.js')
    .addEntry('ejs', './assets/ejs.js')
    .addStyleEntry('aoh', './assets/styles/aoh.css')
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableReactPreset()
    .enableStimulusBridge('./assets/controllers.json')
;

if (process.env.PROFILE) {
    Encore.addPlugin(new BundleAnalyzerPlugin());
}

module.exports = Encore.getWebpackConfig();
