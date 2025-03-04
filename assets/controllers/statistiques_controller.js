import { Controller } from "stimulus";
import Chart from "chart.js/auto";

export default class extends Controller {
    connect() {
        console.log("📌 Contrôleur Stimulus connecté.");

        // 🔥 Vérifie si l'élément contient bien les données
        console.log("Dataset brut :", this.element.dataset);

        try {
            // 🔄 Parsage des données depuis les attributs HTML (convertir JSON string en objet JS)
            const labels = JSON.parse(this.element.dataset.statistiquesLabels);
            const quantites = JSON.parse(this.element.dataset.statistiquesQuantites);
            const energies = JSON.parse(this.element.dataset.statistiquesEnergies);
            const usages = JSON.parse(this.element.dataset.statistiquesUsages);

            console.log("📊 Données parsées :", { labels, quantites, energies, usages });

            const ctx1 = this.element.querySelector("#statistiquesChart")?.getContext("2d");
            const ctx2 = this.element.querySelector("#consommationChart")?.getContext("2d");

            if (ctx1 && ctx2) {
                // Regrouper l'énergie par type d'utilisation
                const consommationParUsage = {};
                usages.forEach((usage, index) => {
                    if (!consommationParUsage[usage]) {
                        consommationParUsage[usage] = 0;
                    }
                    consommationParUsage[usage] += energies[index]; 
                });

                const usageLabels = Object.keys(consommationParUsage);
                const consommationData = Object.values(consommationParUsage);

                // 🟢 Graphique 1 : Quantité recyclée & Énergie produite
                new Chart(ctx1, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: "Quantité recyclée (kg)",
                                data: quantites,
                                backgroundColor: "rgba(54, 162, 235, 0.5)",
                                borderColor: "rgba(54, 162, 235, 1)",
                                borderWidth: 1
                            },
                            {
                                label: "Énergie produite (kWh)",
                                data: energies,
                                backgroundColor: "rgba(255, 99, 132, 0.5)",
                                borderColor: "rgba(255, 99, 132, 1)",
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // 🟠 Graphique 2 : Consommation d'énergie par utilisation
                new Chart(ctx2, {
                    type: "pie",
                    data: {
                        labels: usageLabels,
                        datasets: [
                            {
                                label: "Consommation d'énergie par utilisation (kWh)",
                                data: consommationData,
                                backgroundColor: [
                                    "rgba(255, 99, 132, 0.5)",
                                    "rgba(54, 162, 235, 0.5)",
                                    "rgba(255, 206, 86, 0.5)",
                                    "rgba(75, 192, 192, 0.5)",
                                    "rgba(153, 102, 255, 0.5)"
                                ],
                                borderColor: [
                                    "rgba(255, 99, 132, 1)",
                                    "rgba(54, 162, 235, 1)",
                                    "rgba(255, 206, 86, 1)",
                                    "rgba(75, 192, 192, 1)",
                                    "rgba(153, 102, 255, 1)"
                                ],
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

            } else {
                console.error("❌ Le canvas n'a pas été trouvé !");
            }
        } catch (error) {
            console.error("❌ Erreur lors du parsing JSON :", error);
        }
    }
}
