<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder(): TreeBuilder
  {
    $tb = new TreeBuilder('food_choice');
    $root = $tb->getRootNode();

    $root
      ->children()
      ->scalarNode('repository_service')
      ->defaultNull()
      ->end()
      ->scalarNode('image_storage_service')
      ->defaultNull()
      ->end()
      ->end();

    return $tb;
  }
}
