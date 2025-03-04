const Encore = require('@symfony/webpack-encore');

Encore
    // Le répertoire où tous les fichiers compilés seront stockés
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Spécifier le répertoire des fichiers d'entrée
    .addEntry('app', './assets/app.js')

  
    

    // Ajouter la prise en charge de Stimulus
    .enableStimulusBridge('./assets/controllers.json')

    // Activation du HMR (pour le développement)
    .enableSourceMaps(!Encore.isProduction())

    // Versionnement des fichiers (pour les caches en production)
    .enableVersioning(Encore.isProduction())

    // Activation du support de Babel pour les fichiers JavaScript
    .enableVueLoader()
    .enableSingleRuntimeChunk();

    
;

module.exports = Encore.getWebpackConfig();
