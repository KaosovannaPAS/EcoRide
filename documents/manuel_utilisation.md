# Manuel d'Utilisation - EcoRide

Ce guide explique comment utiliser la plateforme EcoRide et fournit les identifiants de test pour évaluer toutes les fonctionnalités.

## Rôles et Identifiants de Test

La plateforme intègre 3 niveaux de rôles. Vous pouvez vous connecter depuis la page `/interface_frontend/pages/connexion.html` avec les identifiants suivants (mot de passe identique pour tous : `password123`) :

### 1. Administrateur
*   **Email :** `admin@ecoride.fr`
*   **Fonctionnalités :** Accès au tableau de bord (Dashboard), création de comptes "Employé", consultation des statistiques globales (Trajets par jour, crédits générés), blocage de comptes utilisateurs.

### 2. Employé
*   **Email :** `employe@ecoride.fr`
*   **Fonctionnalités :** Accès au panneau d'administration restreint pour vérifier et approuver/refuser les avis (Reviews) laissés par les utilisateurs, gestion des incidents.

### 3. Utilisateurs (Chauffeurs & Passagers)
*   **Email 1 (Chauffeur EV) :** `test.driver@ecoride.fr` (100 crédits)
*   **Email 2 (Passager) :** `test.passenger@ecoride.fr` (100 crédits)
*   **Fonctionnalités :** Les comptes peuvent agir en tant que Passager (recherche, réservation) ou Chauffeur (ajout de véhicule, publication de trajet).

*(Si ces comptes n'existent pas lors du premier lancement, créez simplement un compte depuis la page `inscription.html` : 20 crédits vous seront offerts).*

## Scénario de Test Recommandé

### Étape 1 : Le Chauffeur
1. Connectez-vous avec `test.driver@ecoride.fr`.
2. Allez dans **Mon Espace** et naviguez vers **Mes Véhicules**.
3. Ajoutez un véhicule (cochez "Véhicule Électrique" pour gagner le badge écologique sur l'annonce).
4. Naviguez vers **Publier un trajet**. Saisissez une ville de départ, une ville d'arrivée (ex: Paris -> Lyon), fixez un nombre de crédits (le prix) et validez. 
*Note : La publication déduit une commission de 2 crédits de votre solde.*

### Étape 2 : Le Passager
1. Dans un autre navigateur (ou après déconnexion), connectez-vous avec `test.passenger@ecoride.fr`.
2. Allez sur **Rechercher un trajet**.
3. Cherchez "Paris" vers "Lyon". Vous devriez voir l'annonce du Chauffeur. Le badge "🍃 Écologique" sera visible si le véhicule est électrique.
4. Cliquez sur "Détails" puis "Réserver". Les crédits seront déduits de votre compte.

### Étape 3 : L'Employé (Modération)
1. Si le passager laisse un avis via la base MongoDB (simulable via l'API MongoDB ou en base), il apparaît "en attente".
2. Connectez-vous avec `employe@ecoride.fr`.
3. Allez dans **Mon Espace** -> **Plateau d'Administration** (ou **Gestion Avis**).
4. Acceptez l'avis pour qu'il soit visible sur la page d'accueil ou la fiche du trajet.

### Étape 4 : L'Administrateur
1. Connectez-vous avec `admin@ecoride.fr`.
2. Accédez au **Dashboard Admin**.
3. Vous y verrez les statistiques des jours précédents (Nombre de trajets planifiés, démarrés, terminés).

## Fonctionnement des Crédits
EcoRide n'utilise pas d'argent réel. Les trajets s'échangent contre des Crédits.
*   Inscription : +20 crédits offerts
*   Publication trajet : -2 crédits (Commission plateforme)
*   Réservation : -[Prix du trajet] crédits pour le passager, réservés sur un portefeuille temporaire (non simulé actuellement dans la V1, paiement instantané).
