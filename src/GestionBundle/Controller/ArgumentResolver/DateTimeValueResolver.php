<?php

declare(strict_types=1);

namespace GestionBundle\Controller\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class DateTimeValueResolver implements ArgumentValueResolverInterface
{
    const DATE_SIMPLE = 'd/m/Y';

    private $formats = [
        self::DATE_SIMPLE,
        \DateTime::ATOM,
        \DateTime::RSS,
        \DateTime::W3C,
        \DateTime::COOKIE,
        \DateTime::ISO8601,
        \DateTime::RFC3339_EXTENDED,
        \DateTime::RFC822,
        \DateTime::RFC850,
        \DateTime::RFC1036,
        \DateTime::RFC1123,
        \DateTime::RFC2822,
        \DateTime::RFC3339,
    ];

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $value = $request->get($argument->getName());

        // Deal with timestamps gracefully
        if (is_numeric($value)) {
            $value = '@' . $value;
        }

        $ret = null;

        switch ($argument->getType()) {

            case \DateTimeImmutable::class:
            case \DateTimeInterface::class:
                foreach ($this->formats as $format) {
                    if ($date = \DateTimeImmutable::createFromFormat($format, $value)) {
                        $ret = $date;
                        break 2;
                    }
                }
                break;

            // User explicitely want a mutable object
            case \DateTime::class:
                foreach ($this->formats as $format) {
                    if ($date = \DateTime::createFromFormat($format, $value)) {
                        $ret = $date;
                        break 2;
                    }
                }
                break;

            default:
                throw new \InvalidArgumentException(sprintf("argument, parameter or request parameter '%s' is missing from the query", $argument->getName()));
        }

        if (!$ret) {
            throw new \InvalidArgumentException(sprintf("could not parse date '%s' for parameter '%s'", $value, $argument->getName()));
        }

        yield $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return $request->get($argument->getName()) && (
            \DateTime::class === $argument->getType() ||
            \DateTimeInterface::class === $argument->getType() ||
            \DateTimeImmutable::class === $argument->getType()
        );
    }
}
