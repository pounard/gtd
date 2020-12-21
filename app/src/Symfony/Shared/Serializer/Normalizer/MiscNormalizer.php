<?php

declare(strict_types=1);

namespace Gtd\Symfony\Shared\Serializer\Normalizer;

use Gtd\Shared\Domain\Model\Identifier;
use Gtd\Shared\Domain\Model\UuidIdentifier;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MiscNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return \is_object($data) && $this->supports(\get_class($data));
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        if ($object instanceof Identifier) {
            return $object->toString();
        }

        throw new \LogicException(\sprintf("Unsupported object type: %s", \is_object($object) ? \get_class($object) : \gettype($object)));
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $this->supports($type);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        switch ($type) {
            case Identifier::class:
                // @todo Find which type of identifier, we do need something better
                //   here, such as a factory of some kind.
                try {
                    return new UuidIdentifier($data);
                } catch (\Throwable $e) {
                    throw new \InvalidArgumentException(\sprintf("Unsupported identifier format: '%s'", $data));
                }
                break;

            case UuidIdentifier::class:
                return new UuidIdentifier($data);
        }

        throw new \LogicException(\sprintf("Unsupported object type: %s", $type));
    }

    /**
     * Supports*() implementation.
     */
    private function supports(string $type): bool
    {
        return UuidIdentifier::class === $type || Identifier::class === $type;
    }
}
