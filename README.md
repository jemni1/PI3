# Data Farm - Smart & Sustainable Agriculture Platform with AI & ODD

## Overview
**Data Farm** est une application web intelligente développée dans le cadre du projet universitaire **Esprit School of Engineering**.  
Elle vise à révolutionner la gestion agricole grâce aux technologies modernes, en intégrant :
- **Gestion digitale des exploitations agricoles**
- **Automatisation intelligente des cultures, terrains et ressources**
- **Intelligence Artificielle (IA) via YOLOv8 pour détection d'herbes malades et mauvaises herbes**
- **Suivi des Objectifs de Développement Durable (ODD)**

## Objectifs de Développement Durable (ODD)
Le projet **Data Farm** contribue activement aux Objectifs de Développement Durable (ODD) définis par l'ONU :

- **Objectif 7 : Énergie propre et d'un coût abordable**
  - Promotion des énergies renouvelables issues du recyclage des déchets agricoles (biogaz).
  
- **Objectif 12 : Consommation et production responsables**
  - Gestion durable des ressources agricoles.
  - Optimisation des ressources et réduction du gaspillage.
  - Gestion efficace des déchets agricoles avec un processus de recyclage intelligent.

- **Objectif 13 : Lutte contre les changements climatiques**
  - Promotion des énergies renouvelables et solutions basées sur l'économie circulaire.
  - Réduction des impacts environnementaux de l'agriculture grâce à la gestion intelligente et durable.

## Features

- Gestion des utilisateurs : inscription, connexion, réinitialisation du mot de passe.
- Gestion des terrains : création de parcelles, géolocalisation précise, température en temps réel via API météo.
- Gestion des cultures : choix et suivi des cultures adaptées au terrain.
- Gestion des produits agricoles : mise en vente, achat, gestion des commandes.
- Gestion des déchets agricoles : déclaration des déchets, recyclage intelligent vers d'autres usages.
- Gestion des équipements agricoles : mise à disposition, réservation par les travailleurs.
- Gestion des réclamations : dépôt et suivi des réclamations par les utilisateurs, gestion par l'admin.
- **Modules IA YOLOv8** :
  - Détection d'herbes malades.
  - Détection des mauvaises herbes nuisibles ou favorables.
- Statistiques et reporting : ventes, déchets recyclés, suivi global.

## Technologies utilisées

### Backend
- Symfony 6.4 (PHP)
- MySQL

### IA et traitement d'images
- Python 3.8+
- Ultralytics YOLOv8

### Autres outils
- API météo (géolocalisation et température en temps réel)
- Stripe API (pour le paiement)
- Composer
- GitHub Actions 

## Table des matières
- [Installation](#installation)
  - [Installation de l'application Symfony (Backend)](#installation-de-lapplication-symfony-backend)
  - [Installation de Python, Ultralytics et YOLOv8](#installation-de-python-ultralytics-et-yolov8)
- [Utilisation](#utilisation)
  - [Lancement de l'application Symfony](#lancement-de-lapplication-symfony)
  - [Exécution des modules YOLOv8](#exécution-des-modules-yolov8)
- [Contribution](#contribution)
- [Licence](#licence)
- [Acknowledgments](#acknowledgments)

## Installation

### Installation de l'application Symfony (Backend)

1. Clonez le repository :
    ```bash
    git clone https://github.com/votre-utilisateur/data-farm.git
    cd data-farm
    ```

2. Installez les dépendances PHP avec Composer :
    ```bash
    composer install
    ```

3. Configurez le fichier `.env` pour connecter à MySQL :
    ```bash
    DATABASE_URL="mysql://user:password@127.0.0.1:3306/datafarm_db"
    ```

4. Exécutez les migrations :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

5. Lancez le serveur Symfony :
    ```bash
    symfony server:start
    ```

### Installation de Python, Ultralytics et YOLOv8

1. Installez Python (version recommandée : **3.8 ou supérieure**)  
   👉 [Télécharger Python](https://www.python.org/downloads/)

2. Créez et activez un environnement virtuel :
    ```bash
    python -m venv venv
    # Sur Windows
    venv\Scripts\activate
    # Sur macOS/Linux
    source venv/bin/activate
    ```

3. Mettez à jour pip :
    ```bash
    pip install --upgrade pip
    ```

4. Installez Ultralytics (YOLOv8) :
    ```bash
    pip install ultralytics
    ```

5. Vérifiez l'installation :
    ```bash
    yolo
    ```

## Utilisation

### Lancement de l'application Symfony
```bash
symfony server:start
