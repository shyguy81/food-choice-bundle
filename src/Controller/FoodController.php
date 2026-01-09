<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\Controller;

use Shyguy\FoodChoiceCore\Service\SuggestionEngine;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class FoodController
{
  private SuggestionEngine $engine;

  public function __construct(SuggestionEngine $engine)
  {
    $this->engine = $engine;
  }

  #[Route('/food/suggest', name: 'food_choice.suggest', methods: ['GET'])]
  public function suggest(Request $request): JsonResponse
  {
    $days = (int) $request->query->get('days', 7);
    $suggest = $this->engine->suggestOne($days);

    if (null === $suggest) {
      return new JsonResponse(['message' => 'No suggestion available'], 204);
    }

    return new JsonResponse([
      'id' => $suggest->id,
      'name' => $suggest->name,
      'category' => $suggest->category,
    ]);
  }
}
