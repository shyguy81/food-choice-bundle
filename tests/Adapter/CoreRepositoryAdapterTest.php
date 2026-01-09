<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\Tests\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Shyguy\FoodChoiceBundle\Adapter\CoreRepositoryAdapter;
use Shyguy\FoodChoiceCore\Domain\Food as CoreFood;

final class CoreRepositoryAdapterTest extends TestCase
{
  public function testMapsFoodEntityToDomain(): void
  {
    $foodEntity = new class {
      public function getId()
      {
        return 123;
      }

      public function getName()
      {
        return 'Pizza';
      }

      public function getCuisine()
      {
        return new class {
          public function getId()
          {
            return 'c1';
          }

          public function getName()
          {
            return 'Italienne';
          }
        };
      }
    };

    $foodRepo = new class($foodEntity) {
      private $e;
      public function __construct($e)
      {
        $this->e = $e;
      }
      public function findAll()
      {
        return [$this->e];
      }
    };

    $cookedRepo = new class {
      public function findRecentCookedSince(\DateTimeImmutable $since)
      {
        return [];
      }
    };

    $em = $this->createMock(EntityManagerInterface::class);
    $em->method('getRepository')->willReturnMap([
      ['App\\Entity\\Food', $foodRepo],
      ['App\\Entity\\CookedFood', $cookedRepo],
    ]);

    $adapter = new CoreRepositoryAdapter($em);

    $foods = iterator_to_array($adapter->findAllFoods());

    $this->assertCount(1, $foods);
    $this->assertInstanceOf(CoreFood::class, $foods[0]);
    $this->assertSame('123', $foods[0]->getId());
    $this->assertSame('Pizza', $foods[0]->getName());
    $this->assertSame('Italienne', $foods[0]->getCuisine()->getName());
  }
}
