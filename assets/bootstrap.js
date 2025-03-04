import { Application } from "stimulus";
import StatistiquesController from "./controllers/statistiques_controller";

const application = Application.start();
application.register("statistiques", StatistiquesController);
