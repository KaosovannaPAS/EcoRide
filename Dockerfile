FROM php:8.2-apache

# ==============================================================================
# CONFIGURATION DE L'ENVIRONNEMENT DOCKER - ECORIDE
# ==============================================================================
# Ce Dockerfile définit l'environnement d'exécution de l'application web EcoRide.
# Il est basé sur l'image officielle PHP 8.2 avec Apache préconfiguré.
# Il installe toutes les dépendances systèmes et extensions PHP nécessaires
# pour communiquer avec la base de données relationnelle (MySQL/TiDB) et
# la base de données orientée document (MongoDB).
# ==============================================================================

# ------------------------------------------------------------------------------
# 1. Mise à jour du système et installation des dépendances OS
# ------------------------------------------------------------------------------
# - libssl-dev : Requis pour les connexions sécurisées (MongoDB, cURL).
# - libcurl4-openssl-dev : Utile si des requêtes externes (API) sont nécessaires.
# - pkg-config : Outil d'aide à la compilation pour les extensions PECL.
# ------------------------------------------------------------------------------
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config

# ------------------------------------------------------------------------------
# 2. Installation de l'extension PDO MySQL (Base Relationnelle principale)
# ------------------------------------------------------------------------------
# Nécessaire pour les interactions sécurisées avec MySQL (comptes, trajets, etc.)
RUN docker-php-ext-install pdo pdo_mysql

# ------------------------------------------------------------------------------
# 3. Installation de l'extension MongoDB (Base NoSQL secondaire)
# ------------------------------------------------------------------------------
# Utilisé via PECL pour stocker des données non structurées ou volumineuses
# (ex: historique étendu, logs d'audit ou rapports complexes).
RUN pecl install mongodb && docker-php-ext-enable mongodb

# ------------------------------------------------------------------------------
# 4. Configuration du serveur web Apache
# ------------------------------------------------------------------------------
# Activation du module "mod_rewrite" permettant de gérer la réécriture d'URL.
# C'est indispensable pour le bon fonctionnement du routing de l'API (ex: /api/v1/users)
# si des règles spécifiques sont définies dans des fichiers .htaccess.
# ------------------------------------------------------------------------------
RUN a2enmod rewrite

# (Fin de configuration de l'image)
# Le code source sera généralement monté via un volume dans docker-compose
# ou copié ici pour une image de production.
