<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\Type;

final class NullCallableReferenceList implements CallableReferenceList
{
    /**
     * {@inheritdoc}
     */
    public function first(string $className): ?CallableReference
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function all(string $className): iterable
    {
        return [];
    }
}
