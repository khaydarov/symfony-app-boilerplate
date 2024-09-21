<?php

declare(strict_types=1);

namespace App\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/{version}/user')]
class UserController extends AbstractController
{
  #[Route('/', name: 'hello', methods: ['GET'])]
  public function hello(): Response {
      return new JsonResponse(['message' => 'Hello World!']);
  }
}