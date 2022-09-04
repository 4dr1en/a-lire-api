<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{

	#[Route('/registration', name: 'app_registration', methods: ['POST'], format: 'json')]
	public function index(UserPasswordHasherInterface $passwordHasher, Request $request): Response
	{
		$user = new User();
		$user->setPseudo('test');
		$user->setEmail('test@test.com');
		$user->setPassword($passwordHasher->hashPassword($user, 'test'));
		$user->setRoles(['ROLE_USER']);


		return $this->json(
			[
				'user' => $user->getUserIdentifier(),
				'api_token' => 'token',
			],
			Response::HTTP_CREATED
		);
	}
}
