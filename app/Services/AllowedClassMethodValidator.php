<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class AllowedClassMethodValidator
{
    /**
     * List of allowed classes and their methods.
     *
     * @var array
     */
    protected array $allowedClasses;

    public function __construct()
    {
        // Load allowed classes from config with a fallback to an empty array
        $this->allowedClasses = config('background_jobs_settings.allowed_classes', []);
        
        // Log the loaded config to check if it's being loaded properly
        Log::debug('Allowed Classes:', $this->allowedClasses);
        
        // Ensure allowed classes are in the correct format (class => [methods])
        if (!is_array($this->allowedClasses)) {
            throw new Exception('Configuration "allowed_classes" should be an array.');
        }
    }

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
        if (empty($this->allowedClasses)) {
            throw new Exception('No allowed classes configured.');
        }
    
        // Ensure the class exists
        if (!class_exists($className)) {
            throw new Exception("Class '$className' does not exist.");
        }
    
        // Check if the class is in the allowed list and has allowed methods
        if (!isset($this->allowedClasses[$className])) {
            throw new Exception("Class '$className' is not allowed for execution.");
        }
    
        // Check if the method is in the allowed methods for this class
        if (!in_array($methodName, $this->allowedClasses[$className], true)) {
            throw new Exception("Method '$methodName' is not allowed in class '$className'.");
        }
    
        return true;
    }
    
    
}
