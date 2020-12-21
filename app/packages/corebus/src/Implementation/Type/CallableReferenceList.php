<?php

declare(strict_types=1);

namespace MakinaCorpus\CoreBus\Implementation\Type;

/**
 * Idéalement, il faudrait que cette classe soit utilisée dans une passe de
 * compilation, afin d'éviter que l'introspection soit faite au runtime.
 *
 * Une fois tous les handlers référencés, il faut dumper la liste de
 * HandlerReference et trouver un moyen pour le container les restitue
 * telles quelles.
 */
final class CallableReferenceList
{
    private string $parameterClassName;
    private bool $allowMultiple;
    /** @var array<string,CallableReference> */
    private array $references = [];

    public function __construct(string $parameterClassName, bool $allowMultiple)
    {
        $this->parameterClassName = $parameterClassName;
        $this->allowMultiple = $allowMultiple;
    }

    public function appendFromClass(string $handlerClassName, ?string $id = null): void
    {
        foreach ($this->findHandlerMethods($handlerClassName, $id ?? $handlerClassName) as $reference) {
            \assert($reference instanceof CallableReference);

            $this->append($reference);
        }
    }

    /**
     * @return null|CallableReference
     */
    public function first(string $className): ?CallableReference
    {
        return $this->references[$className][0] ?? null;
    }

    /**
     * @return CallableReference[]
     */
    public function all(string $className): iterable
    {
        return $this->references[$className] ?? [];
    }

    private function append(CallableReference $reference): void
    {
        $existing = $this->references[$reference->className][0] ?? null;

        if ($existing && !$this->allowMultiple) {
            \assert($existing instanceof CallableReference);

            throw new \LogicException(\sprintf(
                "Handler for command class %s is already defined using %s::%s, found %s::%s",
                $reference->className,
                $existing->serviceId,
                $existing->methodName,
                $reference->serviceId,
                $reference->methodName
            ));
        }

        $this->references[$reference->className][] = $reference;
    }

    /**
     * Here lies magic, beware.
     */
    private function findHandlerMethods(string $handlerClassName, string $id): iterable
    {
        $class = new \ReflectionClass($handlerClassName);

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            \assert($method instanceof \ReflectionMethod);

            if ($method->isStatic()) {
                continue;
            }

            $parameters = $method->getParameters();
            if (1 !== \count($parameters)) {
                continue;
            }

            $parameter = \reset($parameters);
            \assert($parameter instanceof \ReflectionParameter);

            if (!$parameter->hasType()) {
                continue;
            }

            $type = $parameter->getType();
            \assert($type instanceof \ReflectionType);

            if ($type->isBuiltin()) {
                continue;
            }

            $parameterClassName = $type->getName();

            if (\class_exists($parameterClassName) &&
                \in_array($this->parameterClassName, \class_implements($parameterClassName))
            ) {
                yield new CallableReference($parameterClassName, $method->getName(), $id);
            }
        }
    }
}
