<?php

namespace App\Services;

use Exception;
use ReflectionMethod;

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
        //using reflection to get method parameter information
        $reflectionMethod = new ReflectionMethod($className, $methodName);
        $methodParams = $reflectionMethod->getParameters();

        if (count($parameters) < count($methodParams)) {
            throw new Exception("Not enough parameters provided for method $methodName in class $className.");
        }

        foreach ($methodParams as $index => $param) {
            $paramName = $param->getName();
            $paramType = $param->getType();

            // Check if the parameter type matches
            if ($paramType && isset($parameters[$index])) {
                $expectedType = $paramType->getName();
                $actualType = gettype($parameters[$index]);

                if ($expectedType !== $actualType && !is_subclass_of($parameters[$index], $expectedType)) {
                    throw new Exception("Parameter $paramName for method $methodName in class $className must be of type $expectedType, $actualType given.");
                }
            }

            // Handle default values
            if (!$param->isOptional() && !array_key_exists($index, $parameters)) {
                throw new Exception("Missing required parameter $paramName for method $methodName in class $className.");
            }
        }

        return true;
    }
}
