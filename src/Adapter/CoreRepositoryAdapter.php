<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;
use Shyguy\FoodChoiceCore\Domain\Food as CoreFood;
use Shyguy\FoodChoiceCore\Domain\CookedFood as CoreCookedFood;
use Shyguy\FoodChoiceCore\Domain\Cuisine as CoreCuisine;

final class CoreRepositoryAdapter implements RepositoryInterface
{
  private EntityManagerInterface $em;
  private string $foodClass;
  private string $cookedFoodClass;
  private string $cuisineClass;

  public function __construct(EntityManagerInterface $em, string $foodClass = 'App\\Entity\\Food', string $cookedFoodClass = 'App\\Entity\\CookedFood', string $cuisineClass = 'App\\Entity\\Cuisine')
  {
    $this->em = $em;
    $this->foodClass = $foodClass;
    $this->cookedFoodClass = $cookedFoodClass;
    $this->cuisineClass = $cuisineClass;
  }

  /** @return iterable<CoreFood> */
  public function findAllFoods(): iterable
  {
    $repo = $this->em->getRepository($this->foodClass);
    $entities = $repo->findAll();

    foreach ($entities as $e) {
      $id = (string) ($this->safeCall($e, 'getId') ?? '');
      $name = (string) ($this->safeCall($e, 'getName') ?? '');

      $cuisineEntity = $this->safeCall($e, 'getCuisine');
      $cuisine = null;
      if (null !== $cuisineEntity) {
        $cuisineId = (string) ($this->safeCall($cuisineEntity, 'getId') ?? '');
        $cuisineName = (string) ($this->safeCall($cuisineEntity, 'getName') ?? '');
        $cuisine = new CoreCuisine($cuisineId, $cuisineName);
      }

      yield new CoreFood($id, $name, $cuisine, []);
    }
  }

  /** @return iterable<CoreCookedFood> */
  public function findRecentCookedFoods(\DateTimeImmutable $since): iterable
  {
    $repo = $this->em->getRepository($this->cookedFoodClass);

    if (method_exists($repo, 'findRecentCookedSince')) {
      $results = $repo->findRecentCookedSince($since);
    } elseif (method_exists($repo, 'createQueryBuilder')) {
      $qb = $repo->createQueryBuilder('c');
      $qb->where('c.cookedAt >= :since')->setParameter('since', $since);
      $results = $qb->getQuery()->getResult();
    } else {
      $results = $repo->findAll();
    }

    foreach ($results as $r) {
      $foodRef = $this->safeCall($r, 'getFood');
      $foodId = '';
      if (null !== $foodRef) {
        $foodId = (string) ($this->safeCall($foodRef, 'getId') ?? '');
      } else {
        $foodId = (string) ($this->safeCall($r, 'getFoodId') ?? '');
      }

      $cookedAt = $this->safeCall($r, 'getCookedAt') ?? new \DateTimeImmutable();

      yield new CoreCookedFood((string) $foodId, $cookedAt);
    }
  }

  private function safeCall(object $obj, string $method): mixed
  {
    if (method_exists($obj, $method)) {
      return $obj->{$method}();
    }

    return null;
  }
}
