<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class FoodChoiceExtension extends Extension
{
  public function load(array $configs, ContainerBuilder $container): void
  {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config'));
    $loader->load('services.yaml');

    if (!empty($config['repository_service'])) {
      $container->setAlias(
        \Shyguy\FoodChoiceCore\Port\RepositoryInterface::class,
        $config['repository_service']
      )->setPublic(true);
    }

    if (!empty($config['image_storage_service'])) {
      $container->setAlias(
        \Shyguy\FoodChoiceCore\Port\ImageStorageInterface::class,
        $config['image_storage_service']
      )->setPublic(true);
    }
  }
}
