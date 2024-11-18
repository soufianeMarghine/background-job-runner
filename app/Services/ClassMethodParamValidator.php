<?php

namespace App\Services;

use Exception;
use ReflectionMethod;
use ReflectionNamedType;

class ClassMethodParamValidator
{
    /**
     * Validate the parameters for a given class method.
     *
     * @param string $className
     * @param string $methodName
     * @param array $parameters
     * @return bool
     * @throws Exception
     */
    public function validate(string $className, string $methodName, array $parameters): bool
    {
        // Using reflection to get method parameter information
        $reflectionMethod = new ReflectionMethod($className, $methodName);
        $methodParams = $reflectionMethod->getParameters();

    /*     echo count($parameters);
echo '|||||||||||||||||||||||||||||';
        echo count(array_filter($methodParams, fn($param) => !$param->isOptional()));
        exit; */
        // Check if enough parameters are provided
        if (count($parameters) < count(array_filter($methodParams, fn($param) => !$param->isOptional()))) {
            throw new Exception("Not enough parameters provided for method $methodName in class $className.");
        }

        foreach ($methodParams as $index => $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();

            // Skip if no more parameters are provided
            if (!array_key_exists($index, $parameters)) {
                if (!$param->isOptional()) {
                    throw new Exception("Missing required parameter \$$paramName for method $methodName in class $className.");
                }
                continue;
            }

            $actualValue = $parameters[$index];
            $actualType = gettype($actualValue);

            if ($paramType instanceof ReflectionNamedType) {
                $expectedType = $paramType->getName();
                $allowsNull = $paramType->allowsNull();

                // Check for nullability
                if (is_null($actualValue) && !$allowsNull) {
                    throw new Exception("Parameter \$$paramName for method $methodName in class $className cannot be null.");
                }

                // Validate the type for non-null values
                if (!is_null($actualValue) && !$this->isTypeValid($actualValue, $expectedType)) {
                    throw new Exception("Parameter \$$paramName for method $methodName in class $className must be of type $expectedType, $actualType given.");
                }
            }
        }

        return true;
    }

    /**
     * Check if a value matches the expected type.
     *
     * @param mixed $value
     * @param string $expectedType
     * @return bool
     */
    private function isTypeValid($value, string $expectedType): bool
    {
        $typeChecks = [
            'int' => 'is_int',
            'integer' => 'is_int',
            'float' => 'is_float',
            'double' => 'is_float',
            'string' => 'is_string',
            'bool' => 'is_bool',
            'boolean' => 'is_bool',
            'array' => 'is_array',
        ];

        if (isset($typeChecks[$expectedType])) {
            return $typeChecks[$expectedType]($value);
        }

        // Check if the value is an instance of the expected class/interface
        return is_object($value) && is_a($value, $expectedType);
    }
}
