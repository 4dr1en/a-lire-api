<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class RegistrationController extends AbstractController
{

	#[Route('/registration', name: 'app_registration', methods: ['POST'], format: 'json')]
	public function index(
		UserPasswordHasherInterface $passwordHasher,
		ManagerRegistry $doctrine,
		JWTTokenManagerInterface $JWTManager,
		Request $request
	): Response {
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

		$existingEmail = $doctrine->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
		$existingPseudo = $doctrine->getRepository(User::class)->findOneBy(['pseudo' => $user->getPseudo()]);
		$error_message = [];
		if ($existingEmail) {
			$error_message['email'] = 'Email already exist';
		}
		if ($existingPseudo) {
			$error_message['pseudo'] = 'Pseudo already exist';
		}
		if (count($error_message) > 0) {
			$error['status'] = 400;
			$error['errors'] = $error_message;
			return $this->json($error, 400);
		}

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

		$token = $JWTManager->create($user);

		return $this->json(
			[
				'status' => 201,
				'token' => $token
			],
			Response::HTTP_CREATED
		);
	}
}