<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Doctrine\Common\PropertyHelperTrait;
use ApiPlatform\Exception\InvalidArgumentException;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Uid\Uuid;

trait TypedSearchFilterTrait
{
    use PropertyHelperTrait;

    public const DOCTRINE_INTEGER_TYPE = Types::INTEGER;
    public const DOCTRINE_SMALLINT_TYPE = Types::SMALLINT;
    public const DOCTRINE_UUID_TYPE = 'uuid';

    /**
     * @param array<mixed> $values
     *
     * @return array<mixed>
     */
    protected function normalizeTypedValues(array $values, string $property): ?array
    {
        foreach ($values as $key => $value) {
            if (!\is_int($key) || !\is_string($value) && !\is_int($value)) {
                unset($values[$key]);
            }
        }

        if ([] === $values) {
            throw new InvalidArgumentException(sprintf('At least one value is required, multiple values should be in "%1$s[]=firstvalue&%1$s[]=secondvalue" format', $property));
        }

        return array_values($values);
    }

    protected function getClassFieldType(string $property, string $resourceClass): ?string
    {
        $propertyParts = $this->splitPropertyParts($property, $resourceClass);
        $metadata = $this->getNestedMetadata($resourceClass, $propertyParts['associations']);

        /** @var \ReflectionClass $reflectionClass */
        $reflectionClass = $metadata->getReflectionClass();
        while (!$reflectionClass->hasProperty($propertyParts['field'])) {
            $reflectionClass = $reflectionClass->getParentClass();
            if (!$reflectionClass instanceof \ReflectionClass) {
                return null;
            }
        }
        /** @var \ReflectionNamedType $reflectionNamedType */
        $reflectionNamedType = $reflectionClass->getProperty($propertyParts['field'])->getType();

        return $reflectionNamedType->getName();
    }

    /**
     * @param array<mixed> $values
     */
    protected function hasValidTypedValues(array $values, ?string $doctrineType = null, ?string $classType = null): bool
    {
        foreach ($values as $value) {
            if (null !== $value) {
                $isDoctrineValid = true;
                switch ($doctrineType) {
                    case self::DOCTRINE_INTEGER_TYPE:
                        if (false === filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => -2_147_483_647, 'max_range' => 2_147_483_647]])) {
                            $isDoctrineValid = false;
                        }
                        break;
                    case self::DOCTRINE_SMALLINT_TYPE:
                        if (false === filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => -32_767, 'max_range' => 32_767]])) {
                            $isDoctrineValid = false;
                        }
                        break;
                    case self::DOCTRINE_UUID_TYPE:
                        try {
                            new Uuid($value);
                        } catch (\Throwable) {
                            $isDoctrineValid = false;
                        }
                        break;
                }

                if ($isDoctrineValid) {
                    if (null !== $classType && enum_exists($classType)) {
                        // Enum Type
                        $reflectionType = (new \ReflectionEnum($classType))->getBackingType();
                        if ($reflectionType instanceof \ReflectionType) {
                            settype($value, $reflectionType->getName());
                        }
                        try {
                            /* @phpstan-ignore-next-line */
                            $classType::from($value);
                        } catch (\Throwable) {
                            return false;
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }
}
