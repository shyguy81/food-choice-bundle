## Instructions pour les agents AI — FoodChoiceBundle

Bref: Ce bundle Symfony expose des alias pour intégrer `shyguy81/food-choice-core` dans une application Symfony. L'objectif principal est de permettre à l'application d'injecter ses propres implémentations (repository, stockage d'images) via la configuration.

- **Point d'entrée du bundle**: [src/FoodChoiceBundle.php](src/FoodChoiceBundle.php#L1-L20)
- **Extension DI**: [src/DependencyInjection/FoodChoiceExtension.php](src/DependencyInjection/FoodChoiceExtension.php#L1-L200) — charge `Resources/config/services.yaml` et crée des alias vers les interfaces du core si les clés de config sont fournies.
- **Configuration et valeurs par défaut**: [src/DependencyInjection/Configuration.php](src/DependencyInjection/Configuration.php#L1-L200) — clés configurables: `repository_service` et `image_storage_service` (valeurs par défaut pointant vers `App\\Adapter\\...`).
- **Services fournis par le bundle**: voir [Resources/config/services.yaml](Resources/config/services.yaml#L1-L40) (actuellement vide; le bundle crée essentiellement des alias vers les services de l'application).

Ce qu'un agent doit savoir pour être productif

- Lorsque l'extension est chargée, elle appelle `ContainerBuilder::setAlias()` pour:
  - `Shyguy\\FoodChoiceCore\\Port\\RepositoryInterface` → valeur de `food_choice.repository_service`
  - `Shyguy\\FoodChoiceCore\\Port\\ImageStorageInterface` → valeur de `food_choice.image_storage_service`
  (voir [FoodChoiceExtension.php](src/DependencyInjection/FoodChoiceExtension.php#L1-L200)).
- Pour remplacer une implémentation, l'application doit définir un service correspondant et configurer `config/packages/food_choice.yaml` (exemple dans [README.md](README.md#L1-L40)).

Exemples concrets

- Config d'exemple (à ajouter dans `config/packages/food_choice.yaml`):

```yaml
food_choice:
  repository_service: 'App\\Adapter\\Repository\\DoctrineRepository'
  image_storage_service: 'App\\Adapter\\Storage\\MinioImageAdapter'
```

- Résultat attendu: les interfaces du core seront résolues vers ces services via des alias publics, utilisables par autowiring.

Commandes et flux de développement

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

Si vous voulez, j'ajuste le ton (plus verbeux ou plus bref) ou j'ajoute des exemples de tests/CI. Quel niveau de détail souhaitez-vous ensuite ?
