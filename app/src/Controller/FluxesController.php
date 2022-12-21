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

    #[Route('/fluxes/{idOrSlug<\w+>}', name: 'app_fluxs_show')]
    public function show(FluxRepository $fluxRepository, string $idOrSlug): JsonResponse
    {
        $typeOfSearch = 'slug';
        if (ctype_digit($idOrSlug)) {
            $flux = $fluxRepository->find((int) $id);
            if ( type_of($flux) === 'Flux') {
                $typeOfSearch = 'id';
            }
        } 
        
        if($typeOfSearch === 'slug') {
            $flux = $fluxRepository->findOneBy(['slug' => $idOrSlug]);
        }

        if( $flux !== null ){
            return $this->json(
                [
                    'status' => 200,
                    'searchType' => $typeOfSearch,
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

        return $this->json(
            [
                'status' => 404,
                'message' => 'Flux not found',
            ],
            404
        );
    }
}