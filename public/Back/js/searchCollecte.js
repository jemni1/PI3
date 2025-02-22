document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchCollecte");
    const collectesContainer = document.getElementById("collectesContainer");
    const collectes = collectesContainer.querySelectorAll(".form-check");

    // Cache toutes les collectes au chargement
    collectes.forEach(collecte => collecte.style.display = "none");

    searchInput.addEventListener("input", function () {
        let searchTerm = searchInput.value.toLowerCase().trim();

        if (searchTerm.length > 0) {
            collectes.forEach(collecte => {
                let label = collecte.querySelector("label").innerText.toLowerCase();
                if (label.startsWith(searchTerm)) { // Vérifie si le texte commence par le terme recherché
                    collecte.style.display = "block"; // Affiche uniquement les collectes correspondantes
                } else {
                    collecte.style.display = "none"; // Cache celles qui ne correspondent pas
                }
            });
        } else {
            collectes.forEach(collecte => collecte.style.display = "none"); // Cache tout si vide
        }
    });
});
