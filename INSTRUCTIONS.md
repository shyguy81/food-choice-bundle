# food-choice-bundle — Instructions de publication

Objectif

- Préparer le bundle Symfony `shyguy81/food-choice-bundle` pour GitHub/Packagist et consommation par `food_choice_app`.

Fichiers clés

- `composer.json`: nom `shyguy81/food-choice-bundle`, `type: symfony-bundle`, `autoload` PSR-4 (ex: `Shyguy\\FoodChoiceBundle\\` → `src/`), `extra.symfony` si nécessaire, `license` (MIT).
- `src/`: code du bundle; respecter la casse exacte des namespaces et noms de classes.

Bundling & intégration Symfony

- Le bundle doit déclarer une classe `FoodChoiceBundle` sous le namespace `Shyguy\\FoodChoiceBundle` (ou le namespace choisi) et être enregistrable dans `config/bundles.php`.
- Si le namespace change de casse, corriger pour correspondre exactement au PSR-4 et aux fichiers.

Versioning & publication

- Branch principale: `main` → alias `dev-main`.
- Taguer avec `git tag v1.0.0` puis `git push --tags`.
- Publier sur GitHub: `git@github.com:shyguy81/food-choice-bundle.git`.
- Ajouter le dépôt sur Packagist (ou activer intégration GitHub App).

Composer specifics

- Exemple de `composer.json` minimal:
  ```json
  {
    "name": "shyguy81/food-choice-bundle",
    "type": "symfony-bundle",
    "autoload": {
      "psr-4": {
        "Shyguy\\FoodChoiceBundle\\": "src/"
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

CI / Tests

- Valider `composer.json`.
- Tests unitaires via `phpunit`.
- Vérifier `bin/console` si le bundle ajoute des commandes.

Consommation dans `food_choice_app`

- Pour dev local: utiliser `path` repository avec `symlink: true` dans `composer.json` du projet.
- En production / CI: basculer sur la version Packagist ou sur le repo VCS `git@github.com:shyguy81/food-choice-bundle.git` dans `composer.json` du projet.

Check-list avant publication

- [ ] Namespace et noms de classes corrects (casse exacte).
- [ ] `composer validate` passe.
- [ ] Tests unitaires verts.
- [ ] README et CHANGELOG fournis.
- [ ] Tag créé et poussé.
- [ ] Packagist configuré (ou GitHub integration activée).

Dépannage rapide

- Erreur `Case mismatch between loaded and declared class names`: vérifier la casse de la déclaration de la classe et du chemin fichier.
- Package non trouvé sur Packagist: vérifier `name` dans `composer.json` et URL du repo.

Contact

- Mainteneur: `shyguy81` (GitHub).
