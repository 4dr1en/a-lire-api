<?php

namespace App\Service;

use DOMDocument;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class FetchPageMetas
{
	public function __construct(HttpClientInterface $client)
	{
		$this->client = $client;
	}

	public function get(string $url): array
	{
		$urlResponse = $this->fetch($url);
		return $this->extractMetas($urlResponse);
	}

	protected function fetch(string $url): string
	{
		$response = $this->client->request('GET', $url);
		$statusCode = $response->getStatusCode();

		if ($statusCode === 200) {
			return $response->getContent();
		} else throw new Exception('Error while fetching page metas');
	}

	protected function extractMetas(string $response): array
	{
		$htmlDocument = new DOMDocument();
		@$htmlDocument->loadHTML($response);
		$title = $htmlDocument->getElementsByTagName('title')->item(0)->nodeValue ?? null;
		$metas = $htmlDocument->getElementsByTagName('meta');


		$metaArray = [];
		$metaImages = [];
		foreach ($metas as $meta) {
			$metaProperty = $meta->getAttribute('property') ?: $meta->getAttribute('name');
			if (preg_match('/^og:/', $metaProperty)) {

				$metaName = str_replace('og:', '', $metaProperty);

				if (preg_match('/^image/', $metaName)) {
					if ($metaName === 'image') {
						$metaImages[]['image'] = $meta->getAttribute('content');
					} elseif (
						count($metaImages) > 0
						&& ($metaName === 'image:width'
							|| $metaName === 'image:height'
						)
					) {
						$metaName = str_replace('image:', '', $metaName);
						$metaImages[count($metaImages) - 1][$metaName] = $meta->getAttribute('content');
					}
				} else {
					$metaArray[$metaName] = $meta->getAttribute('content');
				}
			}
		}

		$metaArray['title'] = $metaArray['title'] ?? $title ?? null;
		$metaArray['thumbnail'] = $this->getSmallestImage($metaImages);

		return $metaArray;
	}

	protected function getSmallestImage(array $images): string
	{
		$smallestImage = null;
		foreach ($images as $image) {
			if ($smallestImage === null) {
				$smallestImage = $image;
			} else {
				if (
					isset($smallestImage['width'], $image['width'], $smallestImage['height'], $image['height'])
					&& $smallestImage['width'] > $image['width']
					&& $smallestImage['height'] > $image['height']
				) {
					$smallestImage = $image;
				}
			}
		}
		return $smallestImage['image'] ?: '';
	}
}