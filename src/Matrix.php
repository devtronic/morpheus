<?php
/**
 * This file is part of the Devtronic Morpheus package.
 *
 * (c) Julian Finkler <julian@developer-heaven.de>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Devtronic\Morpheus;

/**
 * Matrix Class
 * @package Devtronic\Morpheus
 */
class Matrix
{
    /** @var array The Matrix Data */
    protected $data = [];

    /** @var int Number of rows in the matrix */
    protected $rowCount = 0;

    /** @var int Number of columns in the matrix */
    protected $columnCount = 0;

    /**
     * Matrix constructor.
     * @param array $matrix The Matrix Data
     */
    public function __construct(array $matrix = [])
    {
        if (!empty($matrix)) {
            $this->setData($matrix);
        }
    }

    /**
     * Add another matrix
     * @param Matrix $matrix The Matrix to add
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The result data
     */
    public function add(Matrix $matrix, $onlyCalculate = false)
    {
        $resultData = $this->synchronousMatrixOperation($matrix, function ($leftHand, $rightHand) {
            return $leftHand + $rightHand;
        });

        if ($onlyCalculate === false) {
            $this->setData($resultData);
        }

        return $resultData;
    }

    /**
     * Subtract another matrix
     * @param Matrix $matrix The Matrix to add
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The result data
     */
    public function subtract(Matrix $matrix, $onlyCalculate = false)
    {
        $resultData = $this->synchronousMatrixOperation($matrix, function ($leftHand, $rightHand) {
            return $leftHand - $rightHand;
        }, $onlyCalculate);

        return $resultData;
    }

    /**
     * Multiplies another matrix
     * @param Matrix $matrix The Matrix to multiply
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The result data
     */
    public function multiply(Matrix $matrix, $onlyCalculate = false)
    {
        if ($this->getColumnCount() != $matrix->getRowCount()) {
            throw new \LogicException('The Rows of second matrix must equals the columns of this matrix');
        }

        $resultData = [];
        for ($i = 0; $i < $this->rowCount; $i++) {
            for ($k = 0; $k < $matrix->columnCount; $k++) {
                $sum = 0;
                for ($j = 0; $j < $this->columnCount; $j++) {
                    $leftHand = $this->getElement($i, $j);
                    $rightHand = $matrix->getElement($j, $k);
                    $sum += $leftHand * $rightHand;
                }
                $resultData[$i][$k] = $sum;
            }
        }

        if ($onlyCalculate === false) {
            $this->setData($resultData);
        }

        return $resultData;
    }

    /**
     * Performs an scalar multiply operation on the matrix
     *
     * @param integer|float|double $multiplier The Multiplier
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The result data
     */
    public function scalarMultiply($multiplier, $onlyCalculate = false)
    {
        $resultData = $this->scalarMatrixOperation(function ($matrixEntry) use ($multiplier) {
            return $matrixEntry * $multiplier;
        }, $onlyCalculate);

        return $resultData;
    }


    /**
     * Performs an scalar divide operation on the matrix
     *
     * @param integer|float|double $divisor The divisor
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The result data
     * @throws \DivisionByZeroError
     */
    public function scalarDivide($divisor, $onlyCalculate = false)
    {
        if ($divisor == 0) {
            throw new \DivisionByZeroError('Divisor must not be zero');
        }

        $resultData = $this->scalarMatrixOperation(function ($matrixEntry) use ($divisor) {
            return $matrixEntry / $divisor;
        }, $onlyCalculate);

        return $resultData;
    }

    /**
     * Transpose the Matrix
     * @return self
     */
    public function transpose()
    {
        $resultData = [];
        foreach ($this->data as $y => $row) {
            foreach ($row as $x => $element) {
                $resultData[$x][$y] = $element;
            }
        }
        $this->setData($resultData);

        return $this;
    }

    /**
     * Performs an operation on two same-size matrices
     *
     * @param Matrix $matrixB The other matrix
     * @param callable $callback The operation Callback($thisMatrixValue, $otherMatrixValue)
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The Result-Data
     */
    public function synchronousMatrixOperation(Matrix $matrixB, callable $callback, $onlyCalculate = true)
    {
        if ($matrixB->getRowCount() != $this->getRowCount() ||
            $matrixB->getColumnCount() != $this->getColumnCount()
        ) {
            throw new \LogicException('The size of the matrices must match');
        }

        $resultData = [];
        $aData = $this->getData();
        $bData = $matrixB->getData();
        for ($y = 0; $y < $this->rowCount; $y++) {
            for ($x = 0; $x < $this->columnCount; $x++) {
                if (!isset($resultData[$y])) {
                    $resultData[$y] = array();
                }
                $resultData[$y][$x] = $callback($aData[$y][$x], $bData[$y][$x]);
            }
        }

        if ($onlyCalculate === false) {
            $this->setData($resultData);
        }

        return $resultData;
    }

    /**
     * Performs an scalar operation on the matrix
     *
     * @param callable $callback The operation Callback($matrixEntry)
     * @param bool $onlyCalculate If true only the result will be returned, otherwise the matrix data gets updated
     * @return array The Result-Data
     */
    public function scalarMatrixOperation(callable $callback, $onlyCalculate = true)
    {
        $resultData = [];
        $data = $this->getData();
        for ($y = 0; $y < $this->rowCount; $y++) {
            for ($x = 0; $x < $this->columnCount; $x++) {
                if (!isset($resultData[$y])) {
                    $resultData[$y] = array();
                }
                $resultData[$y][$x] = $callback($data[$y][$x]);
            }
        }

        if ($onlyCalculate === false) {
            $this->setData($resultData);
        }

        return $resultData;
    }

    /**
     * Checks if the given matrix is valid
     *
     * @param array $data The Matrix
     * @return bool
     */
    private function isValid($data)
    {
        $rowsOk = (is_array($data) && count($data) > 0);
        $columnsOk = true;

        if ($rowsOk) {
            $expectedColumns = count($data[0]);
            foreach ($data as $columns) {
                if (!is_array($columns) || count($columns) != $expectedColumns) {
                    $columnsOk = false;
                    break;
                }
            }
        }

        return $rowsOk && $columnsOk;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns an element of the matrix
     *
     * @param int $row The row
     * @param int $column The column
     * @return mixed The element
     * @throws \Exception
     */
    public function getElement($row, $column)
    {
        if (!isset($this->data[$row][$column])) {
            throw new \Exception('Element not found');
        }
        return $this->data[$row][$column];
    }

    /**
     * @param array $data
     * @return Matrix
     */
    public function setData($data)
    {
        if (!$this->isValid($data)) {
            throw new \LogicException('The data must be an array of array of mixed');
        }

        $this->rowCount = count($data);
        $this->columnCount = ($this->rowCount > 0 ? count($data[0]) : 0);

        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount;
    }
}