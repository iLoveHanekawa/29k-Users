<?php 
namespace _29kUsers;
class ValidationResult {
    /** 
        *@var bool 
    */
    private $success;
    /**  
        *@var Error[]
    */
    private $errors;
    /** 
        *@var int 
    */
    private $statusCode;
    public function __construct(bool $success, int $statusCode = 200, array $errors = array()) {
        $this->success = $success;
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }
    public function getErrors(): array {
        return $this->errors;
    }
    public function getSuccessStatus(): bool {
        return $this->success;
    }
    public function getProcessedErrors(): array {
        $errors = [];
        foreach($this->errors as $error) {
            $errors[$error->getKey()] = $error->getMessages();
        }
        return $errors;
    }
    public function addErrors(ValidationResult $secondResult): ValidationResult {
        if($this->success === true) $this->success = $secondResult->getSuccessStatus();
        $this->statusCode = $secondResult->getStatusCode();
        $this->errors = array_merge($this->errors, $secondResult->getErrors());
        return $this;
    }
    public function getStatusCode(): int {
        return $this->statusCode;
    }
}
?>