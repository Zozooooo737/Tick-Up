# Tick'Up — Réservation de séances de cinéma

> Application web de réservation de places de cinéma, développée avec Symfony.
> Projet de développement web · ESILV · 2025-2026

![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.3-000000?logo=symfony&logoColor=white)
![Doctrine](https://img.shields.io/badge/Doctrine_ORM-3.5-FC6A31)
![EasyAdmin](https://img.shields.io/badge/EasyAdmin-4-4B5563)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-database-4479A1?logo=mysql&logoColor=white)

<!-- Ajoutez ici une capture de la page d'accueil : ![Accueil Tick'Up](docs/accueil.png) -->

---

## Sommaire

- [Le projet](#le-projet)
- [Fonctionnalités](#fonctionnalités)
- [Stack technique](#stack-technique)
- [Modèle de données](#modèle-de-données)
- [Parcours de réservation](#parcours-de-réservation)
- [Routes](#routes)
- [Structure du dépôt](#structure-du-dépôt)
- [Installation](#installation)
- [Données de test](#données-de-test)
- [Équipe](#équipe)

---

## Le projet

Le sujet demandait de créer un site complet de réservation de spectacles avec le framework Symfony. **Tick'Up** l'applique au cinéma : on y consulte les films à l'affiche et leurs séances, puis on réserve des places pour une séance précise.

Le site se compose de deux parties :

- **Front** : catalogue des films, fiche détaillée avec bande-annonce, création de compte, connexion et réservation avec paiement simulé.
- **Back-office** : interface d'administration (EasyAdmin) pour gérer toutes les données, et page de statistiques.

---

## Fonctionnalités

### Côté visiteur et client

- **Page d'accueil** avec les films à l'affiche.
- **Catalogue des films** et **fiche détaillée** : synopsis, durée, genre, réalisateur, acteurs, affiche, bande-annonce et liste des séances.
- **Création de compte** par email et mot de passe (mot de passe haché).
- **Connexion / déconnexion**, avec option « se souvenir de moi » (7 jours).
- **Réservation** (réservée aux utilisateurs connectés) :
  1. choix du film et de la séance ;
  2. saisie du nombre de personnes ;
  3. vérification des places restantes dans la salle ;
  4. **récapitulatif** : nombre de personnes, prix unitaire, prix total et email du client (paiement simulé) ;
  5. confirmation et enregistrement de la réservation.

### Côté administrateur

- **Tableau de bord EasyAdmin** sur `/admin`, avec un CRUD complet pour :
  - les utilisateurs ;
  - les films ;
  - les salles ;
  - les séances ;
  - les réservations.
- **Page de statistiques** : nombre total de places réservées, chiffre d'affaires, nombre de réservations, de séances et d'utilisateurs, et séance la plus réservée.

### Sécurité

| Zone | Accès |
|---|---|
| `/admin/*` | `ROLE_ADMIN` uniquement |
| `/booking/*` | Utilisateurs connectés (`ROLE_USER`) |
| Reste du site | Public |

L'authentification passe par un authentificateur de formulaire personnalisé (`LoginFormAuthenticator`), avec l'email comme identifiant.

---

## Stack technique

| Couche | Technologie |
|---|---|
| Langage | PHP 8.2+ |
| Framework | Symfony 7.3 |
| ORM | Doctrine ORM 3 + Doctrine Migrations |
| Back-office | EasyAdmin 4 |
| Templates | Twig |
| Front | Bootstrap 5.3, AssetMapper, Stimulus, Turbo |
| Base de données | MySQL |
| Données de test | FakerPHP + Doctrine Fixtures |

---

## Modèle de données

```
┌──────────┐                 ┌──────────────┐                ┌──────────┐
│   User   │ 1             * │   Booking    │ *            1 │Screening │
│──────────│─────────────────│──────────────│────────────────│──────────│
│ email    │                 │ date         │                │ dateTime │
│ password │                 │ places       │                │ price    │
│ roles    │                 │ pricePerPers.│                └────┬─┬───┘
│ firstName│                 │ totalPrice   │                   * │ │ *
│ lastName │                 └──────────────┘                     │ │
└──────────┘                                         ┌────────────┘ └──────────┐
                                                   1 │                       1 │
                                              ┌──────┴─────┐            ┌──────┴─────┐
                                              │   Movie    │            │    Room    │
                                              │────────────│            │────────────│
                                              │ title      │            │ name       │
                                              │ synopsis   │            │ capacity   │
                                              │ duration   │            └────────────┘
                                              │ genre      │
                                              │ director   │
                                              │ actors     │
                                              │ releaseDate│
                                              │ poster     │
                                              │ trailer    │
                                              └────────────┘
```

| Entité | Rôle |
|---|---|
| `User` | Compte client ou administrateur (email unique, rôles, mot de passe haché) |
| `Movie` | Film à l'affiche et ses informations |
| `Room` | Salle de cinéma et sa capacité |
| `Screening` | Séance : un film dans une salle, à une date et un prix donnés |
| `Booking` | Réservation d'un utilisateur pour une séance |

**Logique métier dans les entités :**
- `Screening::getRemainingPlaces()` calcule les places restantes : capacité de la salle moins les places déjà réservées.
- `Booking` recalcule automatiquement son `totalPrice` dès que le nombre de places ou le prix unitaire change.

---

## Parcours de réservation

```
/movies/{id}  ──▶  /booking  ──▶  /booking/recap  ──▶  /booking/confirm  ──▶  /booking/success
 fiche film        choix de la    récapitulatif       enregistrement         confirmation
                   séance et du   (paiement simulé)   en base
                   nombre de
                   places
```

À chaque étape, le serveur vérifie que la séance existe et que le nombre de places demandé est compris entre 1 et le nombre de places restantes. Sinon, l'utilisateur est renvoyé vers le formulaire avec un message d'erreur.

---

## Routes

| Route | URL | Description |
|---|---|---|
| `app_home` | `/` | Page d'accueil |
| `app_movies` | `/movies` | Liste des films |
| `app_show` | `/movies/{id}` | Fiche d'un film et ses séances |
| `app_register` | `/register` | Création de compte |
| `app_login` | `/login` | Connexion |
| `app_logout` | `/logout` | Déconnexion |
| `app_booking` | `/booking` | Formulaire de réservation |
| `app_booking_recap` | `/booking/recap` | Récapitulatif (POST) |
| `app_booking_confirm` | `/booking/confirm` | Enregistrement de la réservation (POST) |
| `app_booking_success` | `/booking/success` | Page de confirmation |
| `admin` | `/admin` | Tableau de bord EasyAdmin |
| `app_stats` | `/admin/stats` | Statistiques |

---

## Structure du dépôt

```
Tick-Up/
├── assets/                       # JS (Stimulus) et CSS
├── config/                       # Configuration Symfony (sécurité, Doctrine, routes...)
├── migrations/                   # Migrations Doctrine
├── public/                       # Point d'entrée web (index.php)
├── src/
│   ├── Controller/
│   │   ├── Admin/                # Tableau de bord et CRUD EasyAdmin
│   │   ├── AuthController.php    # Connexion / déconnexion
│   │   ├── MyController.php      # Pages du site, réservation, statistiques
│   │   └── RegistrationController.php
│   ├── DataFixtures/             # Données de test (Faker + movies.yaml)
│   ├── Entity/                   # User, Movie, Room, Screening, Booking
│   ├── Form/                     # Formulaire d'inscription
│   ├── Repository/               # Requêtes (places restantes, stats...)
│   └── Security/                 # Authentificateur de formulaire
├── templates/                    # Vues Twig
├── composer.json
└── .env                          # Configuration (base de données...)
```

---

## Installation

### Prérequis

- PHP 8.2 ou plus
- [Composer](https://getcomposer.org/)
- MySQL (par exemple via XAMPP, WAMP ou MAMP)
- [Symfony CLI](https://symfony.com/download) (recommandé)

### Étapes

1. **Cloner le dépôt**

   ```bash
   git clone https://github.com/Zozooooo737/Tick-Up.git
   cd Tick-Up
   ```

2. **Installer les dépendances**

   ```bash
   composer install
   ```

3. **Configurer la base de données**

   Créez un fichier `.env.local` et adaptez la connexion à votre MySQL :

   ```env
   DATABASE_URL="mysql://root:root@127.0.0.1:3306/tickup_db"
   ```

4. **Créer la base et les tables**

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Charger les données de test**

   ```bash
   php bin/console doctrine:fixtures:load
   ```

6. **Lancer le serveur**

   ```bash
   symfony serve
   ```

   Le site est accessible sur http://127.0.0.1:8000.

---

## Données de test

Les fixtures (`src/DataFixtures/AppFixtures.php`) génèrent un jeu de données réaliste avec [FakerPHP](https://fakerphp.org/) :

| Données | Quantité |
|---|---|
| Compte administrateur | 1 |
| Utilisateurs clients | 20 |
| Films (définis dans `movies.yaml`, avec affiche et bande-annonce) | 16 |
| Salles (50 à 200 places) | 5 |
| Séances sur le mois à venir | 50 |
| Réservations | 100 |

Le prix des séances varie selon l'heure : entre 8 et 10 € avant 18 h, entre 11 et 14 € ensuite.

**Comptes de démonstration :**

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | `root@gmail.com` | `root` |
| Client | un email généré (visible dans `/admin`) | `password` |

> **Attention :** ces identifiants servent uniquement à la démonstration en local.

---

## Équipe

Projet réalisé à l'**ESILV** (2025-2026) par :

- **Enzo BOTTURA**
- **Alexandre KOUAKOU**
- **Lenny MURRAY**
- **Mattia SCIACCA**

Encadrant : Florent DIEDLER
