[![GitHub tag](https://img.shields.io/packagist/v/devtronic/morpheus.svg)](https://github.com/Devtronic/morpheus)
[![License](https://img.shields.io/packagist/l/Devtronic/morpheus.svg)](https://github.com/Devtronic/morpheus/blob/master/LICENSE)
[![Travis](https://img.shields.io/travis/Devtronic/morpheus.svg)](https://travis-ci.org/Devtronic/morpheus)
[![Packagist](https://img.shields.io/packagist/dt/Devtronic/morpheus.svg)](https://packagist.org/packages/devtronic/morpheus)

# Morpheus
Morpheus is a matrix calculation class for PHP

## Installation
```bash
$ composer require devtronic/morpheus
```

## Usage
### Create a Matrix
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrix = new Matrix([
    [1, 2, 3],
    [4, 5, 6]
]);

// or

$matrix = new Matrix();
$matrix->setData([
    [1, 2, 3],
    [4, 5, 6]
]);
```

### Simple Operations

#### Add
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrixA = new Matrix([
    [1, 2, 3],
]);

$matrixB = new Matrix([
    [4, 5, 6],
]);

$matrixA->add($matrixB);

print_r($matrixA->getData());
// [ [5, 7, 9] ]
```

#### Subtract
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrixA = new Matrix([
    [4, 5, 6],
]);

$matrixB = new Matrix([
    [1, 2, 3],
]);

$matrixA->subtract($matrixB);

print_r($matrixA->getData());
// [ [3, 3, 3] ]
```

#### Multiply
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrixA = new Matrix([
    [1, 2, 3],
    [3, 2, 1],
]);

$matrixB = new Matrix([
    [1, 2],
    [10, 20],
    [100, 200],
]);

$matrixA->subtract($matrixB);

print_r($matrixA->getData());
// [
//     [321, 642],
//     [123, 246],
// ]
```

### Scalar Operations

#### Scalar Multiply
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrix = new Matrix([
    [1, 2, 3],
    [3, 2, 1],
]);

$matrix->scalarMultiply(5);

print_r($matrix->getData());
// [
//     [5, 10, 15],
//     [15, 10, 5],
// ]
```

#### Scalar Division
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrix = new Matrix([
    [10, 15, 30],
    [30, 10, 15],
]);

$matrix->scalarDivide(5);

print_r($matrix->getData());
// [
//     [2, 3, 10],
//     [10, 2, 3],
// ]
```

### Custom Operations


#### Scalar Operations
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrix = new Matrix([
    [1, 0, 0],
    [1, 1, 0],
]);

$matrix->scalarMatrixOperation(function($element) {
    return $element == 1 ? 0 : 1;
});

print_r($matrix->getData());
// [
//     [0, 1, 1],
//     [0, 0, 1],
// ]
```

#### "Synchronous" Operations
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrixA = new Matrix([
    [1, 0, 0],
    [1, 1, 0],
]);

$matrixB = new Matrix([
    [1, 1, 0],
    [0, 1, 0],
]);

// Simple XOR Operation
$matrixA->synchronousMatrixOperation($matrixB, function($left, $right) {
    return intval($left ^ $right);
});

print_r($matrixA->getData());
// [
//     [0, 1, 0],
//     [1, 0, 0],
// ]
```

### Transformation

#### Transpose
```php
<?php

use Devtronic\Morpheus\Matrix;

$matrix = new Matrix([
    [1, 2],
    [3, 4],
    [5, 6],
]);

$matrix->transpose();

print_r($matrixA->getData());
// [
//     [1, 3, 5],
//     [2, 4, 6],
// ]
```
