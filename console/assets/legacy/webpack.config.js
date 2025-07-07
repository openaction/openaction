const Encore = require('@symfony/webpack-encore');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('../../public/build-legacy/')
    .setPublicPath('/build-legacy')
    .addStyleEntry('lib', './js-stimulus/styles/lib.scss')
    .addStyleEntry('app', './js-stimulus/styles/app.scss')
    .addEntry('bundle', './js-stimulus/kernel.jsx')
    .addEntry('editor', './js-stimulus/editor.jsx')
    .addEntry('new-app', './ts-react/app.ts')
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
