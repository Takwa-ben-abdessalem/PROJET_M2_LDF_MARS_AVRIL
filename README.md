# EventFlow

[![EventFlow CI/CD](https://github.com/Takwa-ben-abdessalem/PROJET_M2_LDF_MARS_AVRIL/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/Takwa-ben-abdessalem/PROJET_M2_LDF_MARS_AVRIL/actions/workflows/ci-cd.yml)

EventFlow est une plateforme MVP de gestion d'evenements professionnels avec une API Symfony, un frontend Vue 3 et une partie RGPD visible dans l'application.

## Prerequis

- PHP 8.2+
- Composer 2+
- Node.js 20+
- MySQL 8+ ou PostgreSQL 14+
- OpenSSL pour generer les cles JWT

## Architecture

```text
backend/   API REST Symfony 6.4, Doctrine, JWT, RGPD
frontend/  Application Vue 3, Vite, Pinia, Vue Router, Axios
docs/      Livrables RGPD et collection de requetes HTTP
```

## Installation backend

```bash
cd backend
composer install
cp .env.example .env
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console lexik:jwt:generate-keypair
symfony server:start
```

Sans Symfony CLI :

```bash
php -S 127.0.0.1:8000 -t public
```

Variables importantes :

- `DATABASE_URL` : connexion MySQL ou PostgreSQL.
- `APP_SECRET` : secret Symfony utilise aussi pour hasher les IP RGPD.
- `JWT_SECRET_KEY`, `JWT_PUBLIC_KEY`, `JWT_PASSPHRASE` : configuration Lexik JWT.
- `CORS_ALLOW_ORIGIN` : origine du frontend.
- `ORGANIZER_INVITE_CODE` : code exige pour creer un compte organisateur. Valeur par defaut : `eventflow-organizer`.

## Installation frontend

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Le frontend attend l'API sur `VITE_API_BASE_URL`, par defaut `http://localhost:8000/api`.

## Lancement avec Docker Compose

Docker Compose lance toute la stack de demo : MariaDB, PHP-FPM Symfony, Nginx et Vue/Vite.

```bash
docker-compose up --build
```

Selon l'installation Docker, la commande moderne peut aussi fonctionner :

```bash
docker compose up --build
```

URLs :

- Frontend Vue : `http://localhost:5173`
- API Symfony via Nginx : `http://localhost:8080/api`
- MariaDB Docker : `localhost:3307`

Au demarrage, le conteneur PHP installe les dependances Composer, genere les cles JWT si elles n'existent pas, puis applique les migrations Doctrine. La base Docker utilise :

- database : `eventflow`
- user : `eventflow`
- password : `eventflow`
- root password : `root`

Pour arreter :

```bash
docker-compose down
```

Pour repartir avec une base vide :

```bash
docker-compose down -v
docker-compose up --build
```

## Comptes de test

Le MVP ne livre pas de fixtures obligatoires. Cree deux comptes via `POST /api/auth/register` ou via la page frontend `/register` :

- utilisateur : `user.eventflow@example.com`
- organisateur : `organizer.eventflow@example.com` avec `roles: ["ROLE_ORGANIZER"]` et `organizerInviteCode: "eventflow-organizer"`

## Endpoints principaux

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/events`
- `GET /api/events/{id}`
- `POST /api/events`
- `PUT /api/events/{id}`
- `DELETE /api/events/{id}`
- `POST /api/events/{id}/register`
- `GET /api/me/registrations`
- `DELETE /api/registrations/{id}`
- `GET /api/me`
- `PUT /api/me`
- `DELETE /api/me`
- `GET /api/me/consent-logs`
- `GET /api/me/cookie-preferences`
- `PUT /api/me/cookie-preferences`
- `POST /api/consent`
- `GET /api/me/export`

## Scenario de demo

1. Ouvrir `/register`, accepter le consentement RGPD et creer un compte organisateur.
2. Se connecter sur `/login`.
3. Creer un evenement depuis `/events/create`.
4. Ouvrir `/events`, verifier la liste publique, puis s'inscrire a un evenement.
5. Aller sur `/profile`, modifier le profil, consulter les logs RGPD, exporter les donnees.
6. Tester l'anonymisation avec confirmation.

## Tests

```bash
cd backend
php bin/phpunit
```

Les tests couvrent l'authentification, le CRUD evenement, l'inscription, l'anonymisation et les logs RGPD.

## CI/CD GitHub Actions

Le workflow `.github/workflows/ci-cd.yml` se lance a chaque push ou pull request sur `main`.

Il verifie :

- backend Symfony : installation Composer, generation des cles JWT de test, lint du container, validation Doctrine et PHPUnit ;
- frontend Vue : installation npm et build Vite ;
- delivery Docker : validation `docker compose config` et build des images Docker.

Sur la branche `main`, le build frontend est aussi publie comme artifact GitHub Actions.

## Commande RGPD

```bash
cd backend
php bin/console app:anonymize-old-users
```

La commande anonymise les comptes inactifs depuis plus de 2 ans : prenom et nom remplaces par `Utilisateur supprimé`, email remplace par son hash SHA-256, telephone mis a `null`, `isAnonymized = true`. Elle peut etre planifiee via un cron en production.

## Securite

- Ne jamais commiter de vrai `.env`.
- Les mots de passe sont hashes avec Symfony PasswordHasher.
- Les adresses IP des logs RGPD sont hashees avec SHA-256 et `APP_SECRET`.
- Le frontend communique uniquement avec l'API REST.
