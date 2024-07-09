<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\MediaType;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response as ModelResponse;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\HttpFoundation\Response;

/**
 * @codeCoverageIgnore
 */
final readonly class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }

    /**
     * @param array<mixed> $context
     */
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $openApi->getInfo()
            ->withDescription('This is a simple API')
            ->withExtensionProperty('info-key', 'Info value')
            ->withExtensionProperty('key', 'Custom x-key value')
            ->withExtensionProperty('x-value', 'Custom x-value value');

        // Components Schemas
        $components = $openApi->getComponents();
        /** @var \ArrayObject $schemas */
        $schemas = $components->getSchemas();
        $schemas['Authentification'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'user' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string', 'format' => 'uuid'],
                        'email' => ['type' => 'string'],
                        'roles' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'active' => ['type' => 'boolean'],
                        'username' => ['type' => 'string'],
                        'firstname' => ['type' => 'string'],
                        'lastname' => ['type' => 'string'],
                    ],
                ],
                'token' => ['type' => 'string'],
                'tokenExpiresAt' => ['type' => 'integer'],
                'refreshTokenExpiresAt' => ['type' => 'integer'],
            ],
        ]);

        $schemas->ksort();

        // Token routes
        $tags = ['Auth'];
        $pathItem = $openApi->getPaths()->getPath('/api/authentication_token');
        $openApi->getPaths()->addPath('/api/authentication_token', $pathItem->withPost(
            $pathItem->getPost()
                ->withTags($tags)
                ->withResponses([
                    Response::HTTP_OK => (new ModelResponse())
                        ->withDescription('User token created')
                        ->withContent(new \ArrayObject([
                            'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                                '$ref' => '#/components/schemas/Authentification',
                            ]))),
                        ])),
                ])
        ));

        $openApi->getPaths()->addPath('/api/auth/logout', (new PathItem())->withPost((new Operation())
            ->withOperationId('auth_logout')
            ->withTags($tags)
            ->withResponses([
                Response::HTTP_OK => ['description' => 'Delete BEARER and refresh_token cookies'],
            ])
            ->withSummary('Delete a token user')
        ));

        $openApi->getPaths()->addPath('/api/auth/refresh', (new PathItem())->withPost((new Operation())
            ->withOperationId('auth_refresh')
            ->withTags($tags)
            ->withResponses([Response::HTTP_OK => ['description' => 'User token refreshed']])
            ->withSummary('Refresh a token user')
            ->withRequestBody((new RequestBody())
                ->withDescription('The refresh token')
                ->withRequired(false)
                ->withContent(new \ArrayObject([
                    'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                        'type' => 'object',
                        'properties' => [
                            'refresh_token' => ['type' => 'string', 'required' => false, 'nullable' => true],
                        ],
                    ]))),
                ]))
            )
            ->withResponses([
                Response::HTTP_OK => (new ModelResponse())
                    ->withDescription('User token refreshed')
                    ->withContent(new \ArrayObject([
                        'application/json' => new MediaType(new \ArrayObject(new \ArrayObject([
                            '$ref' => '#/components/schemas/Authentification',
                        ]))),
                    ])),
            ])
        ));

        // Global Response add for all routes
        /** @var PathItem $pathItem */
        foreach ($openApi->getPaths()->getPaths() as $pathItem) {
            /** @var Operation $operation */
            foreach ([$pathItem->getGet(), $pathItem->getPost(), $pathItem->getPatch(), $pathItem->getPut(), $pathItem->getDelete(), $pathItem->getHead()] as $operation) {
                if ($operation instanceof Operation) {
                    $operation->addResponse(new ModelResponse('Unauthorized'), Response::HTTP_UNAUTHORIZED);
                    $operation->addResponse(new ModelResponse('Invalid query'), Response::HTTP_BAD_REQUEST);
                    $operation->addResponse(new ModelResponse('Forbidden'), Response::HTTP_FORBIDDEN);
                }
            }
        }

        return $openApi;
    }
}
