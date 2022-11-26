<?php

namespace App\Controller;

use App\entity\Flux;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserFluxesController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/user/created_fluxes', name: 'app_user_created_fluxes', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();
        $fluxes = $user->getCreatedFluxes();

        // return the flux in json format
        return $this->json(
            [
                'user' => $user,
                'fluxes' => $fluxes,
            ],
            Response::HTTP_OK,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['user:light', 'flux:light']
            ]
        );;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/create_flux', name: 'app_user_create_flux', methods: ['POST'], format: 'json')]
    public function createFlux(
        Request $request,
        ValidatorInterface $validator,
        ManagerRegistry $doctrine,
    ): JsonResponse {
        $req = json_decode($request->getContent(), true);
        $user = $this->getUser();

        $flux = new Flux();
        if (isset($req['title'])) {
            $flux->setTitle($req['title'] ?: '');
            $flux->setSlug((new AsciiSlugger())->slug($req['title']));
        }
        $flux->setDescription($req['description'] ?? null);
        $flux->setCreatedAt(new \DateTimeImmutable());
        $flux->setCreatedBy($user);


        $validation_message = $validator->validate($flux);

        if (count($validation_message) > 0) {
            foreach ($validation_message as $error) {
                $error_message[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json([
                'status' => 400,
                'errors' => $error_message
            ], 400);
        }

        $entityManager = $doctrine->getManager();
        $entityManager->persist($flux);
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

        $flux_id = $flux->getId();

        return $this->json(
            [
                'status' => 201,
                'flux_id' => $flux_id
            ],
            Response::HTTP_CREATED
        );
    }
}