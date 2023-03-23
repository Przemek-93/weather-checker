<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api')]
final class SecurityController extends AbstractController
{
    #[Route(
        path: '/login',
        methods: ['POST']
    )]
    public function login(): Response
    {
        // The method is "empty" because symfony handles the request according
        // to the configuration in the security.yaml
        // if Content-Type is not application/json, this method will be
        // called and therefore return the following response
        // this method could be used for other authentication methods
        // implementation
        return new Response('Bad request', Response::HTTP_BAD_REQUEST);
    }
}
