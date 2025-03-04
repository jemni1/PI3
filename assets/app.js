import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
// assets/app.js
import Chart from 'chart.js/auto'; 

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
import { Application } from '@hotwired/stimulus';
import StatistiquesController from './controllers/statistiques_controller';

// Initialiser Stimulus
const application = Application.start();
application.register('statistiques', StatistiquesController);

console.log('✅ Stimulus est chargé et le contrôleur "statistiques" est enregistré !');