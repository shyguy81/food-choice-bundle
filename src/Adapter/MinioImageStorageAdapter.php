<?php

declare(strict_types=1);

namespace Shyguy\FoodChoiceBundle\Adapter;

use Shyguy\FoodChoiceCore\Port\ImageStorageInterface;

/**
 * Minimal adapter for a MinIO-like client.
 *
 * The adapter expects a client provided by the application that can produce
 * presigned URLs. The method name is flexible (try `getPresignedUrl` or
 * `presignGetObject`). If none found, a fallback URL is returned.
 */
final class MinioImageStorageAdapter implements ImageStorageInterface
{
  private object $client;
  private string $bucket;

  public function __construct(object $client, string $bucket = 'default')
  {
    $this->client = $client;
    $this->bucket = $bucket;
  }

  public function generatePresignedUrl(string $key, int $ttl = 3600): string
  {
    if (method_exists($this->client, 'getPresignedUrl')) {
      return $this->client->getPresignedUrl($this->bucket, $key, $ttl);
    }

    if (method_exists($this->client, 'presignGetObject')) {
      return $this->client->presignGetObject($this->bucket, $key, $ttl);
    }

    if (method_exists($this->client, 'getObjectUrl')) {
      return $this->client->getObjectUrl($this->bucket, $key);
    }

    // Last resort: return a relative path that the app can serve.
    return sprintf('/images/%s', ltrim($key, '/'));
  }
}
