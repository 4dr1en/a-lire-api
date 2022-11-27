<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Flux;
use App\Service\FetchPageMetas;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserArticlesController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/user/articles', name: 'app_user_articles', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $articles = $user->getCreatedArticles();

        // return the flux in json format
        return $this->json(
            [
                'user' => $user,
                'articles' => $articles,
            ],
            Response::HTTP_OK,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['user:light', 'article:light', 'flux:light']
            ]
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/create_article', name: 'app_user_create_article', methods: ['POST'], format: 'json')]
    public function createArticle(
        Request $request,
        ValidatorInterface $validator,
        ManagerRegistry $doctrine,
        FetchPageMetas $fetchPageMetas
    ): JsonResponse {
        $req = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $article = new Article();
        if (isset($req['title'])) {
            $article->setTitle($req['title'] ?: '');
        }
        if (isset($req['description'])) {
            $article->setDescription($req['description'] ?: '');
        }
        if (isset($req['url'])) {
            $article->setUrl($req['url'] ?: '');
            $metas = $fetchPageMetas->get($req['url']);

            if (isset($metas['title'])) {
                $article->setTitle($metas['title']);
            }
            if (isset($metas['description'])) {
                $article->setDescription($metas['description']);
            }
            if (isset($metas['thumbnail'])) {
                $article->setThumbnail($metas['thumbnail']);
            }
        }
        if (isset($req['flux'])) {
            $flux = $doctrine->getRepository(Flux::class)->find($req['flux']);
            $article->setBelongTo($flux);
        }

        $article->setCreatedAt(new \DateTimeImmutable());
        $article->setCreatedBy($user);


        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return $this->json(
                [
                    'errors' => (string) $errors,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        $entityManager = $doctrine->getManager();
        $entityManager->persist($article);

        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'status' => 400,
                'errors' => [
                    'database' => 'An error occured'
                ]
            ], 400);
        }

        return $this->json(
            [
                'status' => 201,
                'article' => $article,
            ],
            Response::HTTP_CREATED,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['user:light', 'article:light', 'article:full', 'flux:light']
            ]
        );
    }
}