# Documentation — Fonctionnalités implémentées

Ce document décrit les fonctionnalités que nous avons implémentées dans ce projet Laravel (compte-api). Il explique où trouver les fichiers, comment les tester et quelles sont les conventions suivies.

Date : 21 octobre 2025

## Sommaire

- Introduction
- Modèles, migrations et factories
- Seeders et données de test
- Routes API (v1)
- Contrôleur et resources
- Format de réponse centralisé (trait RestResponse)
- Validation et règles personnalisées
- Middleware LoadModelById
- Exceptions personnalisées
- Authentification (Passport) et sécurité
- CORS
- Rate limiting
- Commandes utiles
- Tests rapides


## Introduction

Ce dépôt fournit une API REST (versionnée `v1`) pour gérer des clients et leurs comptes bancaires. Les principales entités : `Client` et `Compte`.

Les principales exigences abordées :
- UUID pour clés primaires
- génération automatique du numéro de compte
- validations via FormRequests et règles personnalisées
- format de réponse centralisé
- middleware pour précharger les modèles par ID
- seeders + factories pour tests
- routes sécurisées via OAuth2 (Laravel Passport)
- index DB pour les colonnes souvent recherchées


## Modèles, migrations et factories

- `app/Models/Client.php` : modèle `Client` (UUID primary key). Fichier ajouté/modifié.
- `app/Models/Compte.php` : modèle `Compte` (UUID primary key). Génére automatiquement `numero_compte` si absent (hook `creating`).

Migrations importantes :
- `database/migrations/2025_10_21_000001_create_clients_table.php` (table `clients`)
- `database/migrations/2025_10_21_000002_create_comptes_table.php` (table `comptes`)
- `database/migrations/2025_10_21_000003_add_indexes_to_clients_and_comptes.php` (ajoute indexs idempotents compatibles PostgreSQL)

Factories :
- `database/factories/ClientFactory.php`
- `database/factories/CompteFactory.php` (génère `client_id` automatiquement via `Client::factory()` si non fourni)

Notes : Les factories produisent des données cohérentes pour les tests et les seeders.


## Seeders et données de test

Seeders principaux :
- `database/seeders/ClientSeeder.php` — crée des clients tests (aliou, fallou, saliou, laye, tedene) et `khoussn@gmail.com`.
- `database/seeders/CompteSeeder.php` — crée 20 comptes génériques puis garantit que chaque client a au moins 2 comptes.
- `database/seeders/UserSeeder.php` — crée un utilisateur de test `khoussn@gmail.com`.

Commandes :

```bash
php artisan migrate --seed
# ou seeders spécifiques
php artisan db:seed --class=ClientSeeder
php artisan db:seed --class=CompteSeeder
php artisan db:seed --class=UserSeeder
```


## Routes API (v1)

Fichier : `routes/api.php`

Routes ajoutées :

- GET /api/v1/comptes — liste paginée des comptes (filtres : type, statut, search, sort, order, limit)
- GET /api/v1/comptes/{id} — récupérer un compte (middleware `load.model`)
- POST /api/v1/comptes — créer un compte (utilise `StoreCompteRequest`)

Ces routes sont groupées sous le préfixe `v1` et protégées par `auth:api` (Passport). Vous verrez les routes via :

```bash
php artisan route:list
```


## Contrôleur et resources

- `app/Http/Controllers/API/V1/CompteController.php` : fournit `index`, `show`, `store`. Utilise `RestResponse` pour uniformiser la réponse.
- `app/Http/Resources/CompteResource.php` : formate les champs retournés pour un compte.

Le controller implémente : pagination (paginate), filtres (type, statut), recherche (numero/titulaire) et tri.


## Format de réponse centralisé (trait RestResponse)

Fichier : `app/Traits/RestResponse.php`

Deux méthodes exposées : `success($data, $message='OK', $status=200)` et `error($message, $status=400, $errors=[])`.
Les controllers doivent utiliser ce trait pour retourner les réponses standardisées.


## Validation et règles personnalisées

- `app/Http/Requests/StoreCompteRequest.php` — centralise la validation pour la création d'un compte.
- Règles personnalisées :
  - `app/Rules/ValidNCI.php` — vérifie un NCI (exemple : 13 chiffres)
  - `app/Rules/ValidPhone.php` — vérifie un téléphone au format international

Remarque : Les règles sont simples et peuvent être adaptées au format réel attendu.


## Middleware LoadModelById

- `app/Http/Middleware/LoadModelById.php` — middleware générique qui, lorsqu'un `id` est présent dans la route ou la request, charge le modèle et l'ajoute aux attributs de la requête.
- Alias enregistré : `load.model` (dans `app/Http/Kernel.php`).

Usage dans la route :

```php
Route::get('/comptes/{id}', [CompteController::class, 'show'])->middleware('load.model:App\\Models\\Compte,id');
```


## Exceptions personnalisées

- `app/Exceptions/ApiException.php` — exception spécifique qui renvoie une structure JSON `success:false` et un message.


## Authentification (Passport) et sécurité

- Les routes sont protégées par `auth:api` (Passport).
- Assurez‑vous d'avoir exécuté :

```bash
php artisan passport:install
```

- Vous pouvez obtenir un token OAuth2 via le flux Password Grant ou via Personal Access Tokens.

Remarque : la distinction Admin vs Client est implémentée de façon minimale dans `CompteController@index` (vérification de `user->role`). Adaptez selon votre schéma utilisateur.


## CORS

- Fichier : `config/cors.php` — actuellement permissif (`'*'`) pour développement.
- En production, restreindre `allowed_origins` à vos domaines.


## Rate limiting

- Le middleware `ThrottleRequests` est actif (groupe `api`). Pour définir explicitement 60 requêtes/minute :

```php
// routes/api.php
Route::prefix('v1')->middleware(['auth:api', 'throttle:60,1'])->group(function () { ... });
```

Ou configurer un RateLimiter dans `App\Providers\RouteServiceProvider`.


## Commandes utiles

- Lister routes : `php artisan route:list`
- Exécuter migrations + seed : `php artisan migrate --seed`
- Lancer les seeders spécifiques : `php artisan db:seed --class=ClientSeeder`
- Créer une clé passport : `php artisan passport:install`


## Tests rapides

- Vérifier que les clients existent :

```bash
php artisan tinker --execute="print_r(\App\Models\Client::all()->toArray());"
```

- Lister clients avec nombre comptes (via psql) :

```bash
PGPASSWORD=Marakhib psql -h 127.0.0.1 -U postgres -d compte_api -c "SELECT cl.email, COUNT(co.*) AS comptes_count FROM clients cl LEFT JOIN comptes co ON co.client_id = cl.id GROUP BY cl.email ORDER BY cl.email;"
```


## Sujets à améliorer / prochaines étapes

- Ajouter tests PHPUnit (controller + factories + rules).
- Générer la documentation OpenAPI (Swagger) pour les endpoints.
- Ajouter Policies/Gates pour autorisations fines (Admin vs Client).
- Restreindre CORS en production.
- Ajouter un endpoint pour exporter les données (CSV/JSON).


---

Si vous voulez, je peux :
- ajouter la documentation OpenAPI automatiquement, ou
- générer les tests PHPUnit (et les exécuter), ou
- ajouter les endpoints CRUD restants (update/delete) et les policies.

Dites-moi ce que vous voulez que je fasse ensuite et je le fais maintenant.
