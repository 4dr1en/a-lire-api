<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AppLoginController extends AbstractController
{
    #[Route('/app/login', name: 'app_login')]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if ( $user === null ) {
            return $this->json(
                [
                    'message' => 'Missing credentials',
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $token = 'token';

        return $this->json(
            [
                'user' => $user->getUserIdentifier(),
                'token' => $token,
            ],
            Response::HTTP_OK
        );



    }
}
