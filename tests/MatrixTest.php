<?php
/**
 * This file is part of the Devtronic Morpheus package.
 *
 * (c) Julian Finkler <julian@developer-heaven.de>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Devtronic\Tests\Morpheus;

use Devtronic\Morpheus\Matrix;
use PHPUnit\Framework\TestCase;

/**
 * MatrixTest
 * @package Devtronic\Tests\Morpheus
 */
class MatrixTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists(\Devtronic\Morpheus\Matrix::class));
    }

    public function testConstructSimple()
    {
        $matrix = new Matrix();
        $this->assertTrue($matrix instanceof Matrix);
    }

    public function testConstructAdvanced()
    {
        $data = [[0]];
        $matrix = new Matrix($data);
        $this->assertTrue($matrix instanceof Matrix);
        $this->assertSame($data, $matrix->getData());
    }

    public function testConstructFails()
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The data must be an array of array of mixed');
        new Matrix([0]);
    }

    public function testGetSetDataSimple()
    {
        $matrix = new Matrix();
        $this->assertSame([], $matrix->getData());
        $data = [
            [1, 3, 3],
            [4, 6, 5],
            [1, 2, 4],
        ];
        $matrix->setData($data);
        $this->assertSame($data, $matrix->getData());
    }

    public function testIsValid()
    {
        $matrix = new Matrix();
        $this->assertTrue($this->invokeMethod($matrix, 'isValid', [[
            [1, 2, 3],
            [4, 5, 6],
        ]]));

        $this->assertFalse($this->invokeMethod($matrix, 'isValid', [[
            [1, 2],
            [4, 5, 6],
        ]]));

        $this->assertFalse($this->invokeMethod($matrix, 'isValid', [[
            1,
            [4, 5, 6],
        ]]));

        $this->assertFalse($this->invokeMethod($matrix, 'isValid', [1]));
    }

    public function testGetRowCountSimple()
    {
        $matrix = new Matrix();
        $this->assertSame(0, $matrix->getRowCount());
    }

    public function testGetRowCountAdvanced()
    {
        $matrix = new Matrix([[1, 2], [1, 0], [1, 1]]);
        $this->assertSame(3, $matrix->getRowCount());
    }

    public function testGetColumnCountSimple()
    {
        $matrix = new Matrix();
        $this->assertSame(0, $matrix->getColumnCount());
    }

    public function testGetColumnCountAdvanced()
    {
        $matrix = new Matrix([[1, 2], [1, 0], [1, 1]]);
        $this->assertSame(2, $matrix->getColumnCount());
    }

    public function testSynchronousMatrixOperation()
    {
        $dataA = [
            [1, 0, 1],
            [1, 1, 1],
        ];
        $matrixA = new Matrix($dataA);

        $matrixB = new Matrix([
            [0, 1, 1],
            [0, 1, 0],
        ]);

        $result = $matrixA->synchronousMatrixOperation($matrixB, function ($leftHand, $rightHand) {
            return intval($leftHand ^ $rightHand);
        });

        $expected = [
            [1, 1, 0],
            [1, 0, 1],
        ];
        $this->assertSame($expected, $result);
        $this->assertSame($dataA, $matrixA->getData());
    }

    public function testAdd()
    {
        $dataA = [[1, 2, 3], [3, 2, 1]];

        $dataB = [[4, 5, 6], [5, 7, 3]];

        $expected = [[5, 7, 9], [8, 9, 4]];

        $matrixA = new Matrix($dataA);
        $matrixB = new Matrix($dataB);

        $result = $matrixA->add($matrixB);
        $this->assertTrue(is_array($result));
        $this->assertSame($expected, $result);
        $this->assertSame($matrixA->getData(), $result);

        $matrixA = new Matrix($dataA);
        $matrixB = new Matrix($dataB);

        $result = $matrixA->add($matrixB, true);
        $this->assertTrue(is_array($result));
        $this->assertSame($expected, $result);
        $this->assertNotSame($matrixA->getData(), $result);
    }

    public function testAddFails()
    {
        $matrixA = new Matrix([[1]]);
        $matrixB = new Matrix();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The size of the matrices must match');
        $matrixA->add($matrixB);
    }

    public function testSubtract()
    {
        $dataA = [[1, 2, 3], [3, 2, 1]];

        $dataB = [[4, 5, 6], [5, 7, 3]];

        $expected = [[-3, -3, -3], [-2, -5, -2]];

        $matrixA = new Matrix($dataA);
        $matrixB = new Matrix($dataB);

        $result = $matrixA->subtract($matrixB);
        $this->assertTrue(is_array($result));
        $this->assertSame($expected, $result);
        $this->assertSame($matrixA->getData(), $result);

        $matrixA = new Matrix($dataA);
        $matrixB = new Matrix($dataB);

        $result = $matrixA->subtract($matrixB, true);
        $this->assertTrue(is_array($result));
        $this->assertSame($expected, $result);
        $this->assertNotSame($matrixA->getData(), $result);
    }

    public function testSubtractFails()
    {
        $matrixA = new Matrix([[1]]);
        $matrixB = new Matrix();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The size of the matrices must match');
        $matrixA->subtract($matrixB);
    }

    public function testScalarMatrixOperation()
    {
        $data = [
            [1, 2],
            [3, 4]
        ];
        $matrix = new Matrix($data);
        $result = $matrix->scalarMatrixOperation(function ($element) {
            return $element / 2 + 1;
        });

        $expected = [
            [1.5, 2],
            [2.5, 3],
        ];
        $this->assertSame($expected, $result);
        $this->assertSame($data, $matrix->getData());
    }

    public function testScalarMultiply()
    {
        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);

        $matrix->scalarMultiply(2);

        $expected = [
            [2, 4, 6],
            [8, 10, 12],
        ];
        $this->assertSame($expected, $matrix->getData());
    }

    public function testScalarDivide()
    {

        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);
        $matrix->scalarDivide(2);

        $expected = [
            [0.5, 1, 1.5],
            [2, 2.5, 3],
        ];
        $this->assertSame($expected, $matrix->getData());
    }

    public function testScalarDivideFails()
    {
        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Divisor must not be zero');
        $matrix->scalarDivide(0);
    }

    public function testGetElement()
    {
        $matrix = new Matrix([
            [1, 2],
            [3, 4],
        ]);

        $this->assertSame(3, $matrix->getElement(1, 0));
        $this->assertSame(4, $matrix->getElement(1, 1));
    }

    public function testGetElementFails()
    {
        $matrix = new Matrix();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Element not found');
        $matrix->getElement(1, 3);
    }

    public function testMultiplySimple()
    {
        $matrixA = new Matrix([
            [2, 3, 5],
        ]);

        $matrixB = new Matrix([
            [4],
            [8],
            [10],
        ]);
        $result = $matrixA->multiply($matrixB);

        $expected = [
            [82],
        ];
        $this->assertSame($expected, $result);
        $this->assertSame($expected, $matrixA->getData());
    }

    public function testMultiplyWithMultipleResult()
    {
        $matrixA = new Matrix([
            [2, 4],
            [6, 8],
        ]);

        $matrixB = new Matrix([
            [1, 3, 5],
            [7, 9, 11],
        ]);
        $result = $matrixA->multiply($matrixB);

        $expected = [
            [30, 42, 54],
            [62, 90, 118],
        ];
        $this->assertSame($expected, $result);
        $this->assertSame($expected, $matrixA->getData());
    }

    public function testMultiplyFails()
    {
        $matrixA = new Matrix([
            [1, 2, 3],
        ]);
        $matrixB = new Matrix([
            [1],
        ]);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The Rows of second matrix must equals the columns of this matrix');
        $matrixA->multiply($matrixB);
    }

    public function testTransposeSimple()
    {
        $matrix = new Matrix([
            [1, 2],
            [3, 4],
            [5, 6],
        ]);

        $matrix->transpose();

        $expected = [
            [1, 3, 5],
            [2, 4, 6],
        ];

        $this->assertSame($expected, $matrix->getData());
    }

    public function testTransposeAdvanced()
    {
        $matrix = new Matrix([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9],
        ]);

        $matrix->transpose();

        $expected = [
            [1, 4, 7],
            [2, 5, 8],
            [3, 6, 9],
        ];

        $this->assertSame($expected, $matrix->getData());
    }

    /**
     * Call protected/private method of a class.
     *
     * @param object &$object Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}