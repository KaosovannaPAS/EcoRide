# EcoRide - Plateforme de Covoiturage Écologique 🌱

EcoRide est une application web conçue pour réduire l'impact environnemental des déplacements en encourageant le covoiturage, avec une mise en avant exclusive des véhicules électriques (EV).

## 🚀 Fonctionnalités Clés

*   **Recherche de trajets :** Filtrage avancé par ville, date, durée, prix, note du chauffeur et critère écologique.
*   **Création de trajets :** Les chauffeurs publient des annonces (commission de 2 crédits).
*   **Réservation par crédits :** Système d'économie partagée où les trajets s'échangent contre des crédits virtuels.
*   **Gestion de flotte :** Les utilisateurs peuvent ajouter leurs véhicules (et indiquer s'ils sont électriques).
*   **Avis & Notes :** Système de notation modéré par les employés avant publication.
*   **Tableau de bord Administrateur :** Statistiques quotidiennes sur les covoiturages et génération de crédits.

## 🛠️ Stack Technique

*   **Frontend :** HTML5, CSS3, JavaScript (Vanilla ES6+), Bootstrap 5.3.0
*   **Backend :** PHP 8.2 (Architecture API-Centric, Programmation Orientée Objet, PDO)
*   **Base de Données :** MySQL (Données relationnelles : Utilisateurs, Trajets, Véhicules) & MongoDB (Logs, Avis clients)
*   **Déploiement :** Docker / Docker Compose prêt pour l'hébergement (ex: sur Render ou Vercel pour le front, VPS/Heroku pour le back).

## 📁 Architecture du Projet

```text
EcoRide/
├── interface_frontend/       # Code source de l'interface utilisateur
│   ├── composants/           # Éléments réutilisables (header, footer, annonces)
│   ├── pages/                # Pages HTML (index, connexion, espace-perso...)
│   └── ressources/           # CSS, JS, Images (Logos, Illustrations, Assets)
├── noyau_backend/            # Logique métier et API
│   ├── api/                  # Points finaux d'API (v1/)
│   ├── configuration/        # Fichiers de connexion BDD (db.php, mongo.php)
│   └── models/               # Classes PHP (User, Trip, Vehicle, Review...)
├── documents/                # Livrables ECF (Charte graphique, Diagrams, MCD)
├── Dockerfile                # Configuration de l'environnement PHP/Apache
└── docker-compose.yml        # Orchestration (PHP + MySQL)
```

## ⚙️ Installation Locale (Docker)

1. **Cloner le répertoire :**
   ```bash
   git clone https://github.com/KaosovannaPAS/EcoRide.git
   cd EcoRide
   ```

2. **Démarrer les conteneurs :**
   ```bash
   docker-compose up -d
   ```

3. **Accéder à l'application :**
   * Interface Web : `http://localhost:8080/interface_frontend/pages/index.html`
   * PhpMyAdmin (Base de données) : `http://localhost:8081`

4. **Variables d'environnement :**
   (Le fichier docker-compose.yml configure automatiquement les informations d'identification MySQL). Pour MongoDB (hébergé sur MongoDB Atlas), reportez-vous au fichier `noyau_backend/configuration/mongo.php`.

## 📚 Documentation Technique

Tous les livrables d'architecture sont disponibles dans le dossier `/documents` :
*   [Schéma MCD (Entité/Relation)](documents/schema_mcd.md)
*   [Diagramme de Classes (Backend)](documents/diagramme_classes.md)
*   [Manuel d'Utilisation (Avec comptes de test)](documents/manuel_utilisation.md)

## 🎨 Charte Graphique et Design

Le projet utilise un thème "Eco" avec des nuances de vert et un design moderne (coins arrondis, ombres douces, style aéré) inspiré par les plateformes de voyage de nouvelle génération. La typographie principale utilise les polices système sans serif.
