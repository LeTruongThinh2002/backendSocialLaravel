<?php

namespace App\Services;

use GuzzleHttp\Client;

class PixabayService
{
  public function searchVideos($query, $perPage = 10)
  {
    $client = new Client([
      'base_uri' => 'https://pixabay.com/api/',
      'verify' => false,
      // 'verify' => storage_path('certs/cacert.pem'), // Đường dẫn đến cacert.pem
    ]);

    $response = $client->get('videos/', [
      'query' => [
        'key' => '43366086-3b4d9da0870eea122279122f0',
        'q' => $query,
        'per_page' => $perPage,
      ],
    ]);

    return json_decode($response->getBody()->getContents(), true);
  }
  public function searchImages($query, $perPage = 10)
  {
    $client = new Client([
      'base_uri' => 'https://pixabay.com/api/',
      'verify' => false,
      // 'verify' => storage_path('certs/cacert.pem'), // Đường dẫn đến cacert.pem
    ]);

    $response = $client->get('', [
      'query' => [
        'key' => '43366086-3b4d9da0870eea122279122f0',
        'q' => $query,
        'per_page' => $perPage,
      ],
    ]);

    return json_decode($response->getBody()->getContents(), true);
  }
  public function getRandomMedia(string $query = 'nature', int $perPage = 200)
  {
    // Randomly decide if it should be a video or an image
    $isVideo = rand(0, 1) === 1;
    $mediaUrl = null;

    if ($isVideo) {
      $videoData = $this->searchVideos($query, $perPage);
      if (!empty($videoData['hits'])) {
        $randomVideo = $videoData['hits'][array_rand($videoData['hits'])];
        $mediaUrl = $randomVideo['videos']['medium']['url'];
      }
    } else {
      $imageData = $this->searchImages($query, $perPage);
      if (!empty($imageData['hits'])) {
        $randomImage = $imageData['hits'][array_rand($imageData['hits'])];
        $mediaUrl = $randomImage['webformatURL'];
      }
    }

    return $mediaUrl;
  }
}
