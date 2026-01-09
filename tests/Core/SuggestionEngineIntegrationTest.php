<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\Tests\Core;

use PHPUnit\Framework\TestCase;
use Shyguy\FoodChoiceCore\Service\SuggestionEngine;
use Shyguy\FoodChoiceCore\Domain\Food as CoreFood;
use Shyguy\FoodChoiceCore\Domain\CookedFood as CoreCookedFood;
use Shyguy\FoodChoiceCore\Port\RepositoryInterface;

final class SuggestionEngineIntegrationTest extends TestCase
{
  public function testSuggestOneReturnsFirstNotRecentlyCooked(): void
  {
    $food1 = new CoreFood('f1', 'Salade');
    $food2 = new CoreFood('f2', 'Soupe');

    $cooked = [new CoreCookedFood('f1', new \DateTimeImmutable())];

    $repo = $this->createMock(RepositoryInterface::class);
    $repo->method('findAllFoods')->willReturn([$food1, $food2]);
    $repo->method('findRecentCookedFoods')->willReturn($cooked);

    $engine = new SuggestionEngine($repo);
    $suggest = $engine->suggestOne(7);

    $this->assertNotNull($suggest);
    $this->assertSame('f2', $suggest->id);
    $this->assertSame('Soupe', $suggest->name);
  }
}
