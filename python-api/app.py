import sys
import json
import joblib
import numpy as np

def load_model():
    return joblib.load('C:\\Users\\anisj\\PI3\\python-api\\random_forest_crop_model.pkl')

def predict(features):
    try:
        # Charger le modèle
        model = load_model()
        
        # Convertir les features en array numpy
        features_array = np.array(features).reshape(1, -1)
        
        # Faire la prédiction
        prediction = model.predict(features_array)
        
        # Retourner la première prédiction (comme string)
        return str(prediction[0])
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    try:
        # Récupérer les features depuis les arguments
        features_json = sys.argv[1]
        features = json.loads(features_json)
        
        # Faire la prédiction
        result = predict(features)
        
        # Afficher le résultat
        print(result)
        
    except Exception as e:
        print(f"Error: {str(e)}", file=sys.stderr)
        sys.exit(1)