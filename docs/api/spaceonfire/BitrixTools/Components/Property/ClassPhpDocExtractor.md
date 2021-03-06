# Class ClassPhpDocExtractor

-   Full name: `\spaceonfire\BitrixTools\Components\Property\ClassPhpDocExtractor`
-   This class implements:
    -   `\Symfony\Component\PropertyInfo\PropertyListExtractorInterface`
    -   `\Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface`
    -   `\Symfony\Component\PropertyInfo\PropertyAccessExtractorInterface`
    -   `\Symfony\Component\PropertyInfo\PropertyDescriptionExtractorInterface`

## Methods

### \_\_construct()

ClassPhpDocExtractor constructor.

| Param              | Type                                                           | Description |
| ------------------ | -------------------------------------------------------------- | ----------- |
| `$docBlockFactory` | _\phpDocumentor\Reflection\DocBlockFactoryInterface&#124;null_ |             |

```php
public function ClassPhpDocExtractor::__construct(?\phpDocumentor\Reflection\DocBlockFactoryInterface $docBlockFactory = null): mixed
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:60`

### getLongDescription()

```php
public function ClassPhpDocExtractor::getLongDescription(string $class, string $property, array $context = []): ?string
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:130`

### getProperties()

```php
public function ClassPhpDocExtractor::getProperties(string $class, array $context = []): array
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:77`

### getShortDescription()

```php
public function ClassPhpDocExtractor::getShortDescription(string $class, string $property, array $context = []): ?string
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:115`

### getTypes()

```php
public function ClassPhpDocExtractor::getTypes(string $class, string $property, array $context = []): ?array
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:105`

### isReadable()

```php
public function ClassPhpDocExtractor::isReadable(string $class, string $property, array $context = []): bool
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:85`

### isWritable()

```php
public function ClassPhpDocExtractor::isWritable(string $class, string $property, array $context = []): bool
```

File location: `src/Components/Property/ClassPhpDocExtractor.php:95`

---

This file automatically generated by [Simple PHP ApiDoc](https://github.com/spaceonfire/simple-php-apidoc)
