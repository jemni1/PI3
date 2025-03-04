import { Application } from "stimulus";
import { definitionsFromContext } from "stimulus/webpack-helpers";

// Créez une application Stimulus
const application = Application.start();

// Chargez automatiquement les contrôleurs Stimulus
const context = require.context("./controllers", true, /\.js$/);
application.load(definitionsFromContext(context));
