<?php

namespace App\Controller;

use App\Repository\FluxRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FluxesController extends AbstractController
{
    #[Route('/fluxes', name: 'app_fluxs')]
    public function index(FluxRepository $fluxRepository): JsonResponse
    {
        $fluxes = $fluxRepository->findAll();

        return $this->json(
            [
                'fluxes' => $fluxes,
            ],
            200,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['flux:light']
            ]
        );
    }

    #[Route('/fluxes/{id}', name: 'app_fluxs_show')]
    public function show(FluxRepository $fluxRepository, int $id): JsonResponse
    {
        $flux = $fluxRepository->find($id);

        return $this->json(
            [
                'status' => 200,
                'flux' => $flux,
            ],
            200,
            [],
            [
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object->getId();
                },
                ObjectNormalizer::GROUPS => ['flux:light', 'flux:full', 'user:light', 'article:light'],
            ]
        );
    }
}