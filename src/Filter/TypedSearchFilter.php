<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Api\IdentifiersExtractorInterface;
use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterInterface;
use ApiPlatform\Doctrine\Common\Filter\SearchFilterTrait;
use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryBuilderHelper;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class TypedSearchFilter extends AbstractFilter implements SearchFilterInterface
{
    use SearchFilterTrait;
    use TypedSearchFilterTrait;

    public const DOCTRINE_INTEGER_TYPE = Types::INTEGER;

    /**
     * @param array<mixed> $properties
     */
    public function __construct(ManagerRegistry $managerRegistry, IriConverterInterface $iriConverter, ?PropertyAccessorInterface $propertyAccessor = null, ?LoggerInterface $logger = null, ?array $properties = null, ?IdentifiersExtractorInterface $identifiersExtractor = null, ?NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);

        $this->iriConverter = $iriConverter;
        $this->identifiersExtractor = $identifiersExtractor;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    protected function getIriConverter(): IriConverterInterface
    {
        return $this->iriConverter;
    }

    protected function getPropertyAccessor(): PropertyAccessorInterface
    {
        return $this->propertyAccessor;
    }

    /**
     * @param array<mixed> $context
     */
    protected function filterProperty(string $property, mixed $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (
            null === $value
            || !$this->isPropertyEnabled($property, $resourceClass)
            || !$this->isPropertyMapped($property, $resourceClass, true)
        ) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $property;

        $values = $this->normalizeTypedValues((array) $value, $property);
        if (null === $values) {
            return;
        }

        $associations = [];
        if ($this->isPropertyNested($property, $resourceClass)) {
            [$alias, $field, $associations] = $this->addJoinsForNestedProperty($property, $alias, $queryBuilder, $queryNameGenerator, $resourceClass, Join::INNER_JOIN);
        }

        $caseSensitive = true;
        $strategy = $this->properties[$property] ?? self::STRATEGY_EXACT;

        // prefixing the strategy with i makes it case insensitive
        if (str_starts_with((string) $strategy, 'i')) {
            $strategy = substr((string) $strategy, 1);
            $caseSensitive = false;
        }

        $metadata = $this->getNestedMetadata($resourceClass, $associations);

        if ($metadata->hasField($field)) {
            if ('id' === $field) {
                $values = array_map($this->getIdFromValue(...), $values);
                // todo: handle composite IDs
            }

            $doctrineTypeField = $this->getDoctrineFieldType($property, $resourceClass);
            $classTypeField = $this->getClassFieldType($property, $resourceClass);
            if (!$this->hasValidTypedValues($values, $doctrineTypeField, $classTypeField)) {
                throw new InvalidArgumentException(sprintf('Values for field "%s" are not valid according to the type.', $field));
            }

            $this->addWhereByStrategy($strategy, $queryBuilder, $queryNameGenerator, $alias, $field, $values, $caseSensitive);

            return;
        }

        // metadata doesn't have the field, nor an association on the field
        if (!$metadata->hasAssociation($field)) {
            return;
        }

        // association, let's fetch the entity (or reference to it) if we can so we can make sure we get its orm id
        $associationResourceClass = $metadata->getAssociationTargetClass($field);
        $associationMetadata = $this->getClassMetadata($associationResourceClass);
        $associationFieldIdentifier = $associationMetadata->getIdentifierFieldNames()[0];
        $doctrineTypeField = $this->getDoctrineFieldType($associationFieldIdentifier, $associationResourceClass);
        $classTypeField = $this->getClassFieldType($associationFieldIdentifier, $associationResourceClass);

        $values = array_map(function ($value) use ($property, $associationFieldIdentifier, $doctrineTypeField, $classTypeField) {
            try {
                $item = $this->getIriConverter()->getResourceFromIri($value, ['fetch_data' => false]);

                return $this->propertyAccessor->getValue($item, $associationFieldIdentifier);
            } catch (InvalidArgumentException) {
                if (!$this->hasValidTypedValues([$value], $doctrineTypeField, $classTypeField)) {
                    throw new InvalidArgumentException(sprintf('Values for field "%s" are not valid according to the type.', $property));
                }

                return $value;
            }
        }, $values);

        $expected = \count($values);
        $values = array_filter($values, static fn ($value) => null !== $value);
        if ($expected > \count($values)) {
            throw new InvalidArgumentException(sprintf('Values for field "%s" are not valid according to the doctrine type.', $property));
        }

        $associationAlias = $alias;
        $associationField = $field;
        if ($metadata->isCollectionValuedAssociation($associationField) || $metadata->isAssociationInverseSide($field)) {
            $associationAlias = QueryBuilderHelper::addJoinOnce($queryBuilder, $queryNameGenerator, $alias, $associationField);
            $associationField = $associationFieldIdentifier;
        }

        $this->addWhereByStrategy($strategy, $queryBuilder, $queryNameGenerator, $associationAlias, $associationField, $values, $caseSensitive);
    }

    /**
     * Adds where clause according to the strategy.
     *
     * @throws InvalidArgumentException If strategy does not exist
     */
    protected function addWhereByStrategy(string $strategy, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $alias, string $field, mixed $values, bool $caseSensitive): void
    {
        if (!\is_array($values)) {
            $values = [$values];
        }

        $wrapCase = $this->createWrapCase($caseSensitive);
        $valueParameter = ':'.$queryNameGenerator->generateParameterName($field);
        $aliasedField = sprintf('%s.%s', $alias, $field);

        if (!$strategy || self::STRATEGY_EXACT === $strategy) {
            if (1 === \count($values)) {
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($wrapCase($aliasedField), $wrapCase($valueParameter)))
                    ->setParameter($valueParameter, $values[0]);

                return;
            }

            $queryBuilder
                ->andWhere($queryBuilder->expr()->in($wrapCase($aliasedField), $valueParameter))
                ->setParameter($valueParameter, $caseSensitive ? $values : array_map('strtolower', $values));

            return;
        }

        $ors = [];
        $parameters = [];
        foreach ($values as $key => $value) {
            $keyValueParameter = sprintf('%s_%s', $valueParameter, $key);
            $parameters[] = [$caseSensitive ? $value : strtolower((string) $value), $keyValueParameter];

            $ors[] = match ($strategy) {
                self::STRATEGY_PARTIAL => $queryBuilder->expr()->like(
                    $wrapCase($aliasedField),
                    $wrapCase((string) $queryBuilder->expr()->concat("'%'", $keyValueParameter, "'%'"))
                ),
                self::STRATEGY_START => $queryBuilder->expr()->like(
                    $wrapCase($aliasedField),
                    $wrapCase((string) $queryBuilder->expr()->concat($keyValueParameter, "'%'"))
                ),
                self::STRATEGY_END => $queryBuilder->expr()->like(
                    $wrapCase($aliasedField),
                    $wrapCase((string) $queryBuilder->expr()->concat("'%'", $keyValueParameter))
                ),
                self::STRATEGY_WORD_START => $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like(
                        $wrapCase($aliasedField),
                        $wrapCase((string) $queryBuilder->expr()->concat($keyValueParameter, "'%'"))
                    ),
                    $queryBuilder->expr()->like(
                        $wrapCase($aliasedField),
                        $wrapCase((string) $queryBuilder->expr()->concat("'% '", $keyValueParameter, "'%'"))
                    )
                ),
                default => throw new InvalidArgumentException(sprintf('strategy %s does not exist.', $strategy)),
            };
        }

        $queryBuilder->andWhere($queryBuilder->expr()->orX(...$ors));
        foreach ($parameters as $parameter) {
            $queryBuilder->setParameter($parameter[1], $parameter[0]);
        }
    }

    /**
     * Creates a function that will wrap a Doctrine expression according to the
     * specified case sensitivity.
     *
     * For example, "o.name" will get wrapped into "LOWER(o.name)" when $caseSensitive
     * is false.
     */
    protected function createWrapCase(bool $caseSensitive): \Closure
    {
        return static function (string $expr) use ($caseSensitive): string {
            if ($caseSensitive) {
                return $expr;
            }

            return sprintf('LOWER(%s)', $expr);
        };
    }

    protected function getType(string $doctrineType): string
    {
        return match ($doctrineType) {
            Types::ARRAY => 'array',
            Types::BIGINT, Types::INTEGER, Types::SMALLINT => 'int',
            Types::BOOLEAN => 'bool',
            Types::DATE_MUTABLE, Types::TIME_MUTABLE, Types::DATETIME_MUTABLE, Types::DATETIMETZ_MUTABLE, Types::DATE_IMMUTABLE, Types::TIME_IMMUTABLE, Types::DATETIME_IMMUTABLE, Types::DATETIMETZ_IMMUTABLE => \DateTimeInterface::class,
            Types::FLOAT => 'float',
            default => 'string',
        };
    }
}
