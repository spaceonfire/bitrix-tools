<?php

declare(strict_types=1);

namespace spaceonfire\BitrixTools\Components\Property;

use BadMethodCallException;
use Bitrix\Main\Diag\Helper;
use CBitrixComponent;
use InvalidArgumentException;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use Webmozart\Assert\Assert;

final class PropertyBag
{
    /**
     * @var string[]
     */
    private const MAGIC_METHODS = ['__get', '__set', '__isset', '__unset'];
    /**
     * @var PropertyInfoExtractor
     */
    private static $propertyInfo;
    /**
     * @var CBitrixComponent
     */
    private $component;
    /**
     * @var string
     */
    private $className;

    public function __construct(CBitrixComponent $component)
    {
        $this->component = $component;
        $this->className = get_class($component);
    }

    public function __get($name)
    {
        $this->assertPropertyReadable($name);
        return $this->component->arResult[$name] ?? null;
    }

    public function __set($name, $value)
    {
        $this->assertPropertyWriteable($name);
        $this->component->arResult[$name] = $this->assertPropertyValue($name, $value);
    }

    public function __isset($name): bool
    {
        $this->assertPropertyReadable($name);
        return isset($this->component->arResult[$name]);
    }

    public function __unset($name)
    {
        $this->assertPropertyWriteable($name);
        unset($this->component->arResult[$name]);
    }

    private static function getPropertyInfo(): PropertyInfoExtractor
    {
        if (self::$propertyInfo === null) {
            // a full list of extractors is shown further below
            $classPhpDocExtractor = new ClassPhpDocExtractor();

            // list of PropertyListExtractorInterface (any iterable)
            $listExtractors = [$classPhpDocExtractor];

            // list of PropertyTypeExtractorInterface (any iterable)
            $typeExtractors = [$classPhpDocExtractor];

            // list of PropertyDescriptionExtractorInterface (any iterable)
            $descriptionExtractors = [$classPhpDocExtractor];

            // list of PropertyAccessExtractorInterface (any iterable)
            $accessExtractors = [$classPhpDocExtractor];

            // list of PropertyInitializableExtractorInterface (any iterable)
            $propertyInitializableExtractors = [];

            self::$propertyInfo = new PropertyInfoExtractor(
                $listExtractors,
                $typeExtractors,
                $descriptionExtractors,
                $accessExtractors,
                $propertyInitializableExtractors
            );
        }

        return self::$propertyInfo;
    }

    private function assertPropertyReadable(string $propertyName): void
    {
        $trace = Helper::getBackTrace(4, null, 3);
        $isInsideClass = false;

        foreach ($trace as $item) {
            if ($item['class'] === $this->className && !in_array($item['function'], self::MAGIC_METHODS, true)) {
                $isInsideClass = true;
                break;
            }
        }

        if (!$isInsideClass && !self::getPropertyInfo()->isReadable($this->className, $propertyName)) {
            throw new BadMethodCallException('Cannot get value of read-only property');
        }
    }

    private function assertPropertyWriteable(string $propertyName): void
    {
        $trace = Helper::getBackTrace(4, null, 3);
        $isInsideClass = false;

        foreach ($trace as $item) {
            if ($item['class'] === $this->className && !in_array($item['function'], self::MAGIC_METHODS, true)) {
                $isInsideClass = true;
                break;
            }
        }

        if (!$isInsideClass && !self::getPropertyInfo()->isWritable($this->className, $propertyName)) {
            throw new BadMethodCallException('Cannot set value to read-only property');
        }
    }

    /**
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     */
    private function assertPropertyValue(string $propertyName, $value)
    {
        /** @var Type[] $propertyTypes */
        $propertyTypes = self::getPropertyInfo()->getTypes($this->className, $propertyName) ?? [];

        /** @var InvalidArgumentException $lastException */
        $lastException = null;

        foreach ($propertyTypes as $type) {
            try {
                return $this->assertValueType($type, $value);
            } catch (InvalidArgumentException $e) {
                $lastException = $e;
                continue;
            }
        }

        throw new InvalidArgumentException($lastException->getMessage(), $lastException->getCode(), $lastException);
    }

    /**
     * @param Type $type
     * @param mixed $value
     * @return mixed
     */
    private function assertValueType(Type $type, $value)
    {
        if ($value === null) {
            if ($type->isNullable() || $type->getBuiltinType() === Type::BUILTIN_TYPE_NULL) {
                return null;
            }

            throw new InvalidArgumentException(sprintf(
                'Expected a value of type: "%s". Got: "null"',
                $type->getClassName() ?? $type->getBuiltinType()
            ));
        }

        if ($type->isCollection()) {
            Assert::isIterable($value);

            $collectionKeyType = $type->getCollectionKeyType();
            $collectionValueType = $type->getCollectionValueType();

            if ($collectionKeyType || $collectionValueType) {
                foreach ($value as $k => $v) {
                    if ($collectionKeyType && $collectionKeyType->getBuiltinType() !== Type::BUILTIN_TYPE_INT) {
                        $this->assertValueType($collectionKeyType, $k);
                    }
                    if ($collectionValueType) {
                        $this->assertValueType($collectionValueType, $v);
                    }
                }
            }

            return $value;
        }

        switch ($type->getBuiltinType()) {
            case Type::BUILTIN_TYPE_INT:
                Assert::integerish($value);
                return (int)$value;
                break;

            case Type::BUILTIN_TYPE_FLOAT:
                Assert::numeric($value);
                return (float)$value;
                break;

            case Type::BUILTIN_TYPE_STRING:
                Assert::string($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_BOOL:
                Assert::boolean($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_RESOURCE:
                Assert::resource($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_ARRAY:
                Assert::isArray($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_CALLABLE:
                Assert::isCallable($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_ITERABLE:
                Assert::isIterable($value);
                return $value;
                break;

            case Type::BUILTIN_TYPE_OBJECT:
            default:
                Assert::object($value);
                if ($type->getClassName()) {
                    Assert::isInstanceOf($value, $type->getClassName());
                }
                return $value;
                break;
        }
    }
}
