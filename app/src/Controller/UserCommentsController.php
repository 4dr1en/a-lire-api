<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserCommentsController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/user/comments', name: 'app_user_comments')]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $comments = $user->getWrittenComments();

        return $this->json(
            [
                'user' => $user,
                'comments' => $comments,
            ],
            Response::HTTP_OK,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['user:light', 'comment:light', 'article:light', 'flux:light']
            ]
        );
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/create_comment', name: 'app_user_create_comment', methods: ['POST'], format: 'json')]
    public function createComment(
        Request $request,
        ValidatorInterface $validator,
        ManagerRegistry $doctrine
    ): JsonResponse {
        $req = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $comment = new Comment();
        $comment->setWrittenBy($user);
        $comment->setCreatedAt(new \DateTimeImmutable());

        $entityManager = $doctrine->getManager();

        if (!empty($req['article'])) {
            $comment->setBelongToArticle(
                $entityManager->getReference(Article::class, $req['article'])
            );
        }

        if (isset($req['text'])) {
            $comment->setText($req['text'] ?: '');
        }

        if (isset($req['parentComment'])) {
            // todo: check if parentComment is a comment of the article

            $comment->setParentComment(
                $entityManager->getReference(Comment::class, $req['parentComment'])
            );
        }

        $errors = $validator->validate($comment);
        if (count($errors) > 0) {
            return $this->json(
                [
                    'errors' => (string) $errors,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $entityManager->persist($comment);
        try {
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json(
                [
                    'errors' => $e->getMessage(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->json(
            [
                'status' => Response::HTTP_CREATED,
                'comment' => $comment,
            ],
            Response::HTTP_CREATED,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['user:light', 'comment:light', 'comment:full', 'article:light', 'flux:light']
            ]
        );
    }
}