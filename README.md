# Forge de Héros
Forge de Héros est une application complète de création et gestion de personnages de jeu de rôle inspirée de Donjons & Dragons.
Le projet est composé de deux applications distinctes :

- Backend Symfony (Fullstack + API REST)
- Frontend React (consommation de l’API)
- Chaque application doit être installée, configurée et lancée séparément.

# Structure du repository
/forge-de-heros-symfony   → Application Symfony (backend + fullstack)
/forge-de-heros-react     → Application React (frontend)

# 1 - Application Symfony (Backend + Fullstack)
Cette application gère :

- Authentification (inscription / connexion)
- Attribution automatique du rôle ROLE_ADMIN au premier utilisateur
- Gestion des races, classes et compétences (admin)
- Gestion des personnages (CRUD)
- Upload d’un avatar
- Système de Point Buy (27 points)
- Calcul automatique des points de vie
- Gestion des groupes d’aventure (Party)
- Recherche et filtres
- API REST publique sous /api/v1/*
- Base de données SQLite obligatoire

# Installation — Symfony

## Cloner le projet
```bash
git clone https://github.com/<votre-repo>/forge-de-heros-symfony.git
cd forge-de-heros-symfony
```
## Installer les dépendances
composer install

## Configurer l’environnement
Créer un fichier .env.local :
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

## Créer la base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

## Charger les fixtures
php bin/console doctrine:fixtures:load
Les fixtures ajoutent automatiquement :

- Races
- Classes
- Compétences
- Associations classes ↔ compétences

## Lancer le serveur Symfony
symfony server:start
http://localhost:8000

# API REST - Documentation
L’API est publique et accessible sans authentification sous :

/api/v1/*
Endpoints disponibles

## Races
GET /api/v1/races — Liste des races

GET /api/v1/races/{id} — Détail d’une race

## Classes
GET /api/v1/classes — Liste des classes

GET /api/v1/classes/{id} — Détail d’une classe + compétences

## Compétences
GET /api/v1/skills — Liste des compétences

## Personnages
GET /api/v1/characters — Liste des personnages (filtres : nom, classe, race)

GET /api/v1/characters/{id} — Détail complet d’un personnage

## Groupes (Party)
GET /api/v1/parties — Liste des groupes (filtres : complets / disponibles)

GET /api/v1/parties/{id} — Détail d’un groupe + membres

# CORS (pour React)

# Installer NelmioCorsBundle :
composer require nelmio/cors-bundle
Configurer config/packages/nelmio_cors.yaml :

nelmio_cors:
    defaults:
        allow_origin: ['http://localhost:5173']
        allow_methods: ['GET', 'OPTIONS']
        allow_headers: ['Content-Type', 'Authorization']
    paths:
        '^/api/':
            allow_origin: ['*']

## Fonctionnalités Symfony
- Authentification complète
- ROLE_ADMIN attribué automatiquement au premier utilisateur
- CRUD races / classes / compétences (admin)
- CRUD personnages
- Upload avatar
- Point Buy (27 points)
- Calcul automatique des PV
- Gestion des groupes d’aventure
- Recherche + filtres
- API REST complète

# 2 - Application React (Frontend)
Cette application consomme l’API Symfony et affiche :

- Liste des personnages
- Détail d’un personnage
- Statistiques visuelles
- Compétences
- Groupes d’aventure
- Membres d’un groupe

# Installation — React

## Cloner le projet
```bash
git clone https://github.com/<votre-repo>/forge-de-heros-react.git
cd forge-de-heros-react
```
## Installer les dépendances
npm install

## Configurer l’API
Créer un fichier .env :

VITE_API_URL=http://localhost:8000/api/v1

## Lancer l’application
npm run dev
http://localhost:5173

# Fonctionnalités React
- Liste des personnages (cartes)
- Filtres : nom, classe, race
- Tri : nom, niveau
- Détail d’un personnage
- Visualisation graphique des stats
- Liste des groupes
- Filtre groupes disponibles
- Navigation fluide
- Gestion du loading et des erreurs

# Structure générale

## Symfony
src/
 ├── Controller/
 ├── Entity/
 ├── Repository/
 ├── Security/
 ├── Service/
 └── DataFixtures/
public/
templates/
migrations/

## React
src/
 ├── components/
 ├── pages/
 ├── services/
 ├── hooks/
 └── assets/

# Contribution
- Code en anglais
- Commentaires en français
- Respect des standards PSR-12
- Commits clairs et réguliers

# Licence
Projet réalisé dans un cadre pédagogique.
Libre d’utilisation et d’adaptation.