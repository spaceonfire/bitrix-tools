<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Components\Property;

use LogicException;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\BaseTag;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlock\Tags\TagWithType;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use ReflectionClass;
use Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyDescriptionExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyListExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

final class ClassPhpDocExtractor implements
    PropertyListExtractorInterface,
    PropertyTypeExtractorInterface,
    PropertyAccessExtractorInterface,
    PropertyDescriptionExtractorInterface
{
    /**
     * @var DocBlockFactoryInterface
     */
    private $docBlockFactory;
    /**
     * @var ContextFactory
     */
    private $contextFactory;
    /**
     * @var PhpDocTypeHelper
     */
    private $phpDocTypeHelper;
    /**
     * @var DocBlock[]
     */
    private $docBlocks = [];
    /**
     * @var array<Property|PropertyWrite|PropertyRead>
     */
    private $properties = [];
    /**
     * @var Context[]
     */
    private $contexts = [];

    /**
     * ClassPhpDocExtractor constructor.
     * @param DocBlockFactoryInterface|null $docBlockFactory
     */
    public function __construct(?DocBlockFactoryInterface $docBlockFactory = null)
    {
        if (!class_exists(DocBlockFactory::class)) {
            throw new LogicException(sprintf(
                'Unable to use the "%s" class as the "phpdocumentor/reflection-docblock" package is not installed.',
                __CLASS__
            ));
        }

        $this->docBlockFactory = $docBlockFactory ?: DocBlockFactory::createInstance();
        $this->contextFactory = new ContextFactory();
        $this->phpDocTypeHelper = new PhpDocTypeHelper();
    }

    /**
     * @inheritDoc
     */
    public function getProperties(string $class, array $context = []): array
    {
        return array_keys($this->getPropertiesTagsFromDocBlock($class));
    }

    /**
     * @inheritDoc
     */
    public function isReadable(string $class, string $property, array $context = []): bool
    {
        $propertiesTags = $this->getPropertiesTagsFromDocBlock($class);
        $propertyTag = $propertiesTags[$property] ?? null;
        return $propertyTag instanceof Property || $propertyTag instanceof PropertyRead;
    }

    /**
     * @inheritDoc
     */
    public function isWritable(string $class, string $property, array $context = []): bool
    {
        $propertiesTags = $this->getPropertiesTagsFromDocBlock($class);
        $propertyTag = $propertiesTags[$property] ?? null;
        return $propertyTag instanceof Property || $propertyTag instanceof PropertyWrite;
    }

    /**
     * @inheritDoc
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $propertiesTags = $this->getPropertiesTagsFromDocBlock($class);
        $propertyTag = $propertiesTags[$property] ?? null;
        return $propertyTag instanceof TagWithType ? $this->phpDocTypeHelper->getTypes($propertyTag->getType()) : null;
    }

    /**
     * @inheritDoc
     */
    public function getShortDescription(string $class, string $property, array $context = []): ?string
    {
        $propertiesTags = $this->getPropertiesTagsFromDocBlock($class);
        $propertyTag = $propertiesTags[$property] ?? null;

        if ($propertyTag instanceof BaseTag && null !== $desc = $propertyTag->getDescription()) {
            return $desc->render();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getLongDescription(string $class, string $property, array $context = []): ?string
    {
        return $this->getShortDescription($class, $property, $context);
    }

    private function getPropertiesTagsFromDocBlock(string $class): array
    {
        if (!isset($this->properties[$class])) {
            $this->properties[$class] = [];

            $docBlock = $this->getDocBlock($class);

            $tagNames = ['property', 'property-read', 'property-write'];

            foreach ($tagNames as $tagName) {
                /** @var Property|PropertyRead|PropertyWrite $propertyTag */
                foreach ($docBlock->getTagsByName($tagName) as $propertyTag) {
                    $this->properties[$class][$propertyTag->getVariableName()] = $propertyTag;
                }
            }
        }

        return $this->properties[$class];
    }

    private function getDocBlock(string $class): DocBlock
    {
        if (!isset($this->docBlocks[$class])) {
            $this->docBlocks[$class] = $this->docBlockFactory->create(
                $classReflection = new ReflectionClass($class),
                $this->createFromReflector($classReflection)
            );
        }

        return $this->docBlocks[$class];
    }

    /**
     * Prevents a lot of redundant calls to ContextFactory::createForNamespace().
     * @param ReflectionClass $reflector
     * @return Context
     */
    private function createFromReflector(ReflectionClass $reflector): Context
    {
        $cacheKey = $reflector->getNamespaceName() . ':' . $reflector->getFileName();

        if (isset($this->contexts[$cacheKey])) {
            return $this->contexts[$cacheKey];
        }

        $this->contexts[$cacheKey] = $this->contextFactory->createFromReflector($reflector);

        return $this->contexts[$cacheKey];
    }
}
