<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HealthcheckController extends AbstractController
{
    public function __construct(private readonly ContainerBagInterface $params)
    {
    }

    #[Route(
        path: '/_health',
        name: '_healthcheck',
        methods: ['GET']
    )]
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse($request->query->has('dumpparams') ? $this->params->all() : 'OK');
    }
}
