import sys
from ultralytics import YOLO

# Récupérer le chemin du modèle et de l'image depuis les arguments
model_path = sys.argv[1]
image_path = sys.argv[2]

try:
    model = YOLO(model_path)
except Exception as e:
    print(f"Erreur lors du chargement du modèle: {e}")
    sys.exit(1)

# Effectuer la prédiction sur l'image
results = model.predict(image_path)

# On suppose qu'il y a une seule image, donc on récupère le premier résultat
result = results[0]

# On vérifie que des boîtes ont été détectées
if result.boxes is not None and len(result.boxes) > 0:
    # Récupérer les classes détectées (les indices, ici 0: crop et 1: weed)
    classes = result.boxes.cls.tolist()
    # Si une détection "weed" (mauvaise plante) est présente, on considère l'image comme "Mauvaise"
    if 1 in classes:
        print("Mauvaise")
    else:
        print("Bonne")
else:
    print("Aucune détection")
