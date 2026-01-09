## Instructions pour les agents AI — FoodChoiceBundle

Bref: Ce bundle Symfony expose des alias pour intégrer `shyguy81/food-choice-core` dans une application Symfony. L'objectif principal est de permettre à l'application d'injecter ses propres implémentations (repository, stockage d'images) via la configuration. Ce document rassemble à la fois les consignes pour les agents et les instructions de publication/packaging utiles pour maintenir et publier le bundle.

Principaux points techniques

- **Point d'entrée du bundle**: [src/FoodChoiceBundle.php](src/FoodChoiceBundle.php#L1-L20)
- **Extension DI**: [src/DependencyInjection/FoodChoiceExtension.php](src/DependencyInjection/FoodChoiceExtension.php#L1-L200) — charge `Resources/config/services.yaml` et crée des alias vers les interfaces du core si les clés de config sont fournies.
- **Configuration et valeurs par défaut**: [src/DependencyInjection/Configuration.php](src/DependencyInjection/Configuration.php#L1-L200) — clés configurables: `repository_service` et `image_storage_service` (valeurs par défaut pointant vers `App\\Adapter\\...`).
- **Services fournis par le bundle**: voir [Resources/config/services.yaml](Resources/config/services.yaml#L1-L40) (actuellement vide; le bundle crée essentiellement des alias vers les services de l'application).

Ce qu'un agent doit savoir pour être productif

- Lorsque l'extension est chargée, elle appelle `ContainerBuilder::setAlias()` pour:
  - `Shyguy\\FoodChoiceCore\\Port\\RepositoryInterface` → valeur de `food_choice.repository_service`
  - `Shyguy\\FoodChoiceCore\\Port\\ImageStorageInterface` → valeur de `food_choice.image_storage_service`
    (voir [src/DependencyInjection/FoodChoiceExtension.php](src/DependencyInjection/FoodChoiceExtension.php#L1-L200)).
- Pour remplacer une implémentation, l'application doit définir un service correspondant et configurer `config/packages/food_choice.yaml` (exemple dans [README.md](README.md#L1-L40)).

Exemples concrets d'utilisation

- Config d'exemple (à ajouter dans `config/packages/food_choice.yaml`):

```yaml
food_choice:
  repository_service: 'App\\Adapter\\Repository\\DoctrineRepository'
  image_storage_service: 'App\\Adapter\\Storage\\MinioImageAdapter'
```

- Résultat attendu: les interfaces du core seront résolues vers ces services via des alias publics, utilisables par autowiring.

Commandes & flux de développement

- Installer les dépendances et exécuter les tests (si présents):

```bash
composer install
vendor/bin/phpunit
```

- PHP/Symfony ciblés: voir [composer.json](composer.json#L1-L40) — `php:^8.1`, `symfony/framework-bundle:^6.0 || ^7.0`.

Patterns et conventions spécifiques au projet

- Le bundle n'expose pas d'API HTTP ni de contrôleurs — il fournit uniquement des alias DI vers le core. Quand vous modifiez l'extension DI, vérifiez les alias et leur visibilité (`setPublic(true)`).
- Les valeurs par défaut sont définies dans `Configuration.php`. Pour ajouter une nouvelle option de configuration, ajouter la clé dans `getConfigTreeBuilder()` et consommer la valeur dans `FoodChoiceExtension::load()`.

Points d'intégration externes

- Dépend du package `shyguy81/food-choice-core` (voir `composer.json`). Les interfaces à connaître sont dans ce core — l'extension suppose leur présence.

Notes pratiques pour les agents

- Lors de modifications de DI, relancer `composer dump-autoload` et, dans un projet Symfony, vider le cache (`bin/console cache:clear`) pour voir les changements.
- Préférez modifier `Configuration.php` et `FoodChoiceExtension.php` plutôt que de dupliquer la logique ailleurs.

Fichiers de référence rapides

- [README.md](README.md#L1-L40)
- [composer.json](composer.json#L1-L40)
- [src/DependencyInjection/Configuration.php](src/DependencyInjection/Configuration.php#L1-L200)
- [src/DependencyInjection/FoodChoiceExtension.php](src/DependencyInjection/FoodChoiceExtension.php#L1-L200)

Instructions de publication et packaging (extrait de INSTRUCTIONS.md)

- Objectif: Préparer le bundle Symfony `shyguy81/food-choice-bundle` pour GitHub/Packagist et consommation par `food_choice_app`.

- Fichiers clés:

  - `composer.json`: nom `shyguy81/food-choice-bundle`, `type: symfony-bundle`, `autoload` PSR-4 (ex: `Shyguy\\FoodChoiceBundle\\` → `src/`), `extra.symfony` si nécessaire, `license` (MIT).
  - `src/`: code du bundle; respecter la casse exacte des namespaces et noms de classes.

- Bundling & intégration Symfony:

  - Le bundle doit déclarer une classe `FoodChoiceBundle` sous le namespace `Shyguy\\FoodChoiceBundle` et être enregistrable dans `config/bundles.php`.
  - Si le namespace change de casse, corriger pour correspondre exactement au PSR-4 et aux fichiers.

- Versioning & publication:

  - Branch principale: `main` → alias `dev-main`.
  - Taguer avec `git tag v1.0.0` puis `git push --tags`.
  - Publier sur GitHub: `git@github.com:shyguy81/food-choice-bundle.git`.
  - Ajouter le dépôt sur Packagist (ou activer intégration GitHub App).

- Composer specifics (exemple minimal recommandé):

```json
{
  "name": "shyguy81/food-choice-bundle",
  "type": "symfony-bundle",
  "autoload": {
    "psr-4": {
      "Shyguy\\\\FoodChoiceBundle\\\\": "src/"
    }
  },
  "require": {
    "php": ">=8.4",
    "symfony/framework-bundle": "^6.0 || ^7.0"
  },
  "extra": {
    "branch-alias": { "dev-main": "1.0-dev" }
  }
}
```

- CI / Tests:

  - Valider `composer.json`.
  - Tests unitaires via `phpunit`.
  - Vérifier `bin/console` si le bundle ajoute des commandes.

- Consommation dans `food_choice_app`:

  - Pour dev local: utiliser `path` repository avec `symlink: true` dans `composer.json` du projet.
  - En production / CI: basculer sur la version Packagist ou sur le repo VCS `git@github.com:shyguy81/food-choice-bundle.git` dans `composer.json` du projet.

- Check-list avant publication:

  - Namespace et noms de classes corrects (casse exacte).
  - `composer validate` passe.
  - Tests unitaires verts.
  - README et CHANGELOG fournis.
  - Tag créé et poussé.
  - Packagist configuré (ou GitHub integration activée).

- Dépannage rapide:
  - Erreur `Case mismatch between loaded and declared class names`: vérifier la casse de la déclaration de la classe et du chemin fichier.
  - Package non trouvé sur Packagist: vérifier `name` dans `composer.json` et URL du repo.

Notes finales pour les agents

- Le bundle n'expose pas d'API HTTP — il fournit des alias DI vers le core. Quand vous modifiez l'extension DI, relancez l'autoload et videz le cache Symfony pour voir les changements.
- Si vous voulez, j'adapte le niveau de détail ou j'ajoute des exemples de tests/CI.

---

Dernière mise à jour: consolidation des instructions de maintenance, publication et usage pour les agents.
