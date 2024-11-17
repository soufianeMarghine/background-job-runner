<?php

namespace App\Services;

use Exception;

class AllowedClassMethodValidator
{
    /**
     * List of allowed classes and their methods.
     *
     * @var array
     */
    protected array $allowedClasses = [
        'App\\Jobs\\SendEmailJob' => ['execute'],

    ];

    /**
     * Validate the given class and method against the allowed list.
     *
     * @param string $className
     * @param string $methodName
     * @return bool
     * @throws Exception
     */
    public function validate(string $className, string $methodName): bool
    {
        if (!array_key_exists($className, $this->allowedClasses)) {
            throw new Exception("Class $className is not allowed for execution.");
        }

        if (!in_array($methodName, $this->allowedClasses[$className], true)) {
            throw new Exception("Method $methodName is not allowed in class $className.");
        }

        return true;
    }
}
