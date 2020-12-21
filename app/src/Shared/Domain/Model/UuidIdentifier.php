<?php

declare (strict_types=1);

namespace Gtd\Shared\Domain\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Rfc4122\NilUuid;

class UuidIdentifier implements Identifier
{
    private bool $isNilUuid = false;
    private UuidInterface $value;

    /**
     * {@inheritdoc}
     */
    public function __construct($value)
    {
        if ($value instanceof UuidInterface) {
            $this->value = $value;
        } else {
            try {
                $this->value = Uuid::fromString($value);
            } catch (\Throwable $e) {
                throw new InvalidIdentifierError("L'identifiant n'est pas un UUID valide.", $e->getCode(), $e);
            }
        }
        $this->isNilUuid = $this->value instanceof NilUuid;
    }

    public static function random() /* static */
    {
        // Little bit of magic, on contourne le constructeur.
        $ret = (new \ReflectionClass(\get_called_class()))->newInstanceWithoutConstructor();
        $ret->value = Uuid::uuid4();

        return $ret;
    }

    /**
     * Get internal raw value.
     */
    public function raw(): UuidInterface
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Comparable $other): bool
    {
        return !$this->isNilUuid && $other instanceof static && $this->value->equals($other->value);
    }

    /**
     * {@inheritdoc}
     */
    public function toString(): string
    {
        return $this->value->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->value->toString();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->isNilUuid;
    }

    /**
     * {@inheritdoc}
     */
    public static function empty() /* : static */
    {
        $ret = new static(Uuid::fromString(Uuid::NIL));
        $ret->isNilUuid = true;

        return $ret;
    }
}
