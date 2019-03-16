<?php
/**
 * Created by PhpStorm.
 * User: jtsmrdel
 * Date: 2019-02-03
 * Time: 15:15
 */

namespace App\Serializer;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        LoggerInterface $logger
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $context options that normalizers have access to
     */
    public function supportsNormalization(
        $data,
        $format = null,
        array $context = array()
    ) {
        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param mixed $object Object to normalize
     * @param string $format Format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|int|float|bool
     *
     * @throws InvalidArgumentException   Occurs when the object given is not an attempted type for the normalizer
     * @throws CircularReferenceException Occurs when the normalizer detects a circular reference when no circular
     *                                    reference handler can fix it
     * @throws LogicException             Occurs when the normalizer is not called in an expected context
     */
    public function normalize($object, $format = null, array $context = array())
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = 'get-owner';
        }
        else {
            $context['groups'][] = 'get';
        }

        // Continue serialization
        return $this->passOn($object, $format, $context);
    }

    private function isUserHimself(User $object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUsername();
    }

    private function passOn($object, ?string $format, array $context)
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException(
                sprintf(
                    'Cannot normalize object "%s" because the injected serializer is not a normalizer.',
                    $object
                )
            );
        }

        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->serializer->normalize($object, $format, $context);
    }
}