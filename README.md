# API Compte — Documentation

Ce dépôt contient une API Laravel pour la gestion basique des clients et de leurs comptes bancaires.

## Contenu principal

- Modèles:
	- `App\Models\Client` — représente un client. Utilise UUID comme clé primaire. Relation: `comptes()` (hasMany `Compte`).
	- `App\Models\Compte` — représente un compte bancaire. Utilise UUID comme clé primaire. Relation: `client()` (belongsTo `Client`).
	- `App\Models\User` — modèle utilisateur par défaut (authentification si nécessaire).

- Migrations:
	- `database/migrations/2025_10_21_000001_create_clients_table.php` — table `clients`.
	- `database/migrations/2025_10_21_000002_create_comptes_table.php` — table `comptes`.

- Factories:
	- `database/factories/ClientFactory.php`
	- `database/factories/CompteFactory.php`

- Seeders:
	- `database/seeders/ClientSeeder.php` — crée clients + comptes associés.
	- `database/seeders/CompteSeeder.php` — (optionnel).

- Validation:
	- `app/Http/Requests/StoreClientRequest.php` — règles de validation pour création/modification de client.

## Installation rapide

1. Installer les dépendances:

```bash
composer install
```

2. Copier le fichier d'environnement et configurer la base de données:

```bash
cp .env.example .env
php artisan key:generate
# Editez .env pour DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

3. Migrer et seed la base:

```bash
php artisan migrate --seed
```

## Commandes utiles

- `php artisan migrate`
- `php artisan migrate:rollback`
- `php artisan migrate:fresh --seed`
- `php artisan db:seed --class=ClientSeeder`

## Tests

Un exemple simple pour vérifier la relation client -> comptes:

```php
/** @test */
public function a_client_has_comptes()
{
		$client = \App\Models\Client::factory()->create();
		\App\Models\Compte::factory()->count(2)->create(['client_id' => $client->id]);

		$this->assertCount(2, $client->comptes);
}
```

Lancer les tests:

```bash
vendor/bin/phpunit
```

## Notes et améliorations possibles

- Décider si `Client` doit être authentifiable (actuellement `User` est le modèle d'auth par défaut).
- Ajouter endpoints API (contrôleur Ressource + routes API) et tests d'intégration.
- Ajouter la table `transactions` et ses relations si nécessaire.
- Affiner les index et contraintes pour la production.

---

Dites-moi si vous voulez que je crée automatiquement :
- un contrôleur API Resource pour `Client` (+ routes),
- des tests PHPUnit supplémentaires,
- ou que je rende `Client` authentifiable (Passport/Sanctum).

