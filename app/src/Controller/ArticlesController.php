<?php

namespace App\Controller;

use App\Entity\Flux;
use App\Repository\FluxRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Loader\ObjectLoader;

class ArticlesController extends AbstractController
{
    #[Route('/articles/{id}', name: 'app_articles_show')]
    public function show(ArticleRepository $articleRepository, int $id): JsonResponse
    {
        $article = $articleRepository->find($id);

        return $this->json(
            [
                'status' => 200,
                'article' => $article,
            ],
            200,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => [
                    'article:light',
                    'article:full',
                    'user:light',
                    'flux:light',
                    'comment:light',
                    'comment:full',
                ],
                ObjectNormalizer::CALLBACKS => [
                    'parent_comment' => function ($innerObject, $outerObject, string $attributeName) {
                        if ($attributeName === 'parent_comment') {
                            $parentComment = $outerObject->getParentComment();
                            if ($parentComment) {
                                return $parentComment->getId();
                            }
                        }
                    },
                ],
            ]
        );
    }
}