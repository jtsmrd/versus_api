<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-03
 * Time: 14:32
 */

namespace App\Serializer;

use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    /**
     * @var SerializerContextBuilderInterface
     */
    private $serializerContextBuilder;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * UserContextBuilder constructor.
     * @param SerializerContextBuilderInterface $serializerContextBuilder
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        SerializerContextBuilderInterface $serializerContextBuilder,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->serializerContextBuilder = $serializerContextBuilder;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Creates a serialization context from a Request.
     *
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     */
    public function createFromRequest(
        Request $request,
        bool $normalization,
        array $extractedAttributes = null
    ): array
    {
        $context = $this->serializerContextBuilder->createFromRequest(
            $request, $normalization, $extractedAttributes
        );

        // Class being serialized/ deserialized
        $resourceClass = $context['resource_class'] ?? null;

        if (
            User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        ) {
            $context['groups'][] = 'get-admin';
        }

        return $context;
    }
}