const Encore = require('@symfony/webpack-encore');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addStyleEntry('lib', './assets-legacy/styles/lib.scss')
    .addStyleEntry('app', './assets-legacy/styles/app.scss')
    .addEntry('bundle', './assets-legacy/kernel.jsx')
    .addEntry('editor', './assets-legacy/editor.jsx')
    .addEntry('new-app', './assets/app.ts')
    .splitEntryChunks()
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps()
    .enableVersioning(Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enableTypeScriptLoader()
    .enableReactPreset()
;

if (process.env.PROFILE) {
    Encore.addPlugin(new BundleAnalyzerPlugin());
}

module.exports = Encore.getWebpackConfig();
