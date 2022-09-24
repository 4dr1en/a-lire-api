<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class RegistrationController extends AbstractController
{

	#[Route('/registration', name: 'app_registration', methods: ['POST'], format: 'json')]
	public function index(UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine, Request $request): Response
	{
		$user = new User();
		$req = json_decode($request->getContent(), true);

		$user->setPseudo($req['pseudo']);
		$user->setEmail($req['email']);
		$user->setPassword($passwordHasher->hashPassword(
			$user,
			$req['password']
		));
		$user->setRoles(['ROLE_USER']);

		$entityManager = $doctrine->getManager();
		$entityManager->persist($user);
		$entityManager->flush();

		return $this->json(
			[
				'user' => $user->getUserIdentifier(),
				'api_token' => 'token',
			],
			Response::HTTP_CREATED
		);
	}
}
