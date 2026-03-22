# Forge de Heros

## 1. Explication breve du sujet

Forge de Heros est un projet pedagogique inspire de Donjons et Dragons.

Le but est de creer et gerer des personnages de jeu de role, avec:

- une application Symfony (fullstack + API REST publique)
- une application React (front public qui consomme l'API)

Fonctionnalites principales attendues:

- gestion des personnages (stats, classe, race, avatar)
- systeme Point Buy (27 points)
- calcul automatique des points de vie
- gestion des groupes d'aventure
- navigation/liste/detail cote React

## 2. Methode d'installation du projet

### Prerequis

- Git
- PHP 8.3+
- Composer
- Node.js 20+ et npm

### Cloner le repository

```bash
git clone https://github.com/fluteABec/Forge-Heros.git
cd Forge-Heros
```

Le monorepo contient:

- `fullstack-symfony` (backend + API)
- `front-react` (frontend)

### Installation et lancement de l'application Symfony

```bash
cd fullstack-symfony
composer install
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
symfony server:start
```

Si la commande `symfony` n'est pas installee, alternative:

```bash
php -S 127.0.0.1:8000 -t public
```

Adresse backend/API: http://localhost:8000

Remarque:

- la base est configuree en SQLite dans le projet
- les routes API sont exposees sous `/api/v1/*`

### Installation et lancement de l'application React

Dans un second terminal:

```bash
cd front-react
npm install
```

Puis lancer le front:

```bash
npm run dev
```

Adresse frontend: http://localhost:5173

### Ordre de demarrage conseille

1. Lancer Symfony (port 8000)
2. Lancer React (port 5173)
3. Ouvrir le front React dans le navigateur