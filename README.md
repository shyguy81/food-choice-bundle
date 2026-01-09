# FoodChoiceBundle

Bundle d'intégration pour `shyguy81/food-choice-core`.

Installation (local):

```bash
composer install
```

Configurer le bundle dans `config/bundles.php` (si nécessaire):

```php
return [
    // ...
    Shyguy\FoodChoiceBundle\FoodChoiceBundle::class => ['all' => true],
];
```

Configuration (exemple `config/packages/food_choice.yaml`):

```yaml
food_choice:
  repository_service: 'App\\Adapter\\Repository\\DoctrineRepository'
  image_storage_service: 'App\\Adapter\\Storage\\MinioImageAdapter'
```

Le bundle crée des alias de services pour :

- `Shyguy\\FoodChoiceCore\\Port\\RepositoryInterface`
- `Shyguy\\FoodChoiceCore\\Port\\ImageStorageInterface`

Ainsi les applications peuvent remplacer les implémentations par leurs propres services.
