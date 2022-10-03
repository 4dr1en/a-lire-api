<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
		Request $request,
		ValidatorInterface $validator
	): Response {
		$req = json_decode($request->getContent(), true);

		$user = new User();
		$user->setPseudo($req['pseudo']);
		$user->setEmail($req['email']);
		$user->setPassword($passwordHasher->hashPassword(
			$user,
			$req['password']
		));
		$user->setRoles(['ROLE_USER']);

		$error_message = [];
		$validation_message = $validator->validate($user);

		if (strlen($req['password']) < 8) {
			$error_message['password'] = 'Your password must be at least 8 characters long';
		}

		if (count($validation_message) > 0 || count($error_message) > 0) {
			foreach ($validation_message as $error) {
				$error_message[$error->getPropertyPath()] = $error->getMessage();
			}

			return $this->json([
				'status' => 'error',
				'errors' => $error_message
			], 400);
		}

		$entityManager = $doctrine->getManager();
		$entityManager->persist($user);
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