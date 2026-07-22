<?php

/**
 * Validator Class
 * Laravel-like validation system compatible with PHP 5.2, 7, 8+
 * 
 * Usage:
 * $validator = new Validator($data, $rules, $messages);
 * if ($validator->fails()) {
 *     $errors = $validator->getErrors();
 * }
 * 
 * Or using helper:
 * $validator = validator($data, $rules, $messages);
 */

class Validator
{
    private $data;
    private $rules;
    private $messages;
    private $errors;
    private $customMessages;
    
    /**
     * Constructor
     * @param array $data - Data yang akan divalidasi
     * @param array $rules - Rules validasi
     * @param array $messages - Custom error messages (optional)
     */
    public function __construct($data, $rules, $messages = array())
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->errors = array();
        
        $this->validate();
    }
    
    /**
     * Jalankan validasi
     */
    private function validate()
    {
        foreach ($this->rules as $field => $rulesString) {
            // Split rules dengan |
            $rulesArray = explode('|', $rulesString);
            
            foreach ($rulesArray as $rule) {
                // Parse rule dan parameter (contoh: min_length[3])
                $ruleParts = $this->parseRule($rule);
                $ruleName = $ruleParts['name'];
                $ruleParams = $ruleParts['params'];
                
                // Get field value
                $value = isset($this->data[$field]) ? $this->data[$field] : null;
                
                // Validate
                $valid = $this->executeRule($field, $value, $ruleName, $ruleParams);
                
                if (!$valid) {
                    $errorMessage = $this->getErrorMessage($field, $ruleName, $ruleParams);
                    $this->addError($field, $errorMessage);
                }
            }
        }
    }
    
    /**
     * Parse rule string menjadi name dan params
     * Contoh: "min_length[3]" -> array('name' => 'min_length', 'params' => array(3))
     */
    private function parseRule($rule)
    {
        $result = array(
            'name' => $rule,
            'params' => array()
        );
        
        // Check jika ada parameter dalam []
        if (strpos($rule, '[') !== false) {
            preg_match('/^([a-z_]+)\[(.+)\]$/', $rule, $matches);
            if (count($matches) === 3) {
                $result['name'] = $matches[1];
                // Split params dengan koma
                $result['params'] = explode(',', $matches[2]);
            }
        }
        
        return $result;
    }
    
    /**
     * Execute validation rule
     */
    private function executeRule($field, $value, $ruleName, $params)
    {
        // Convert rule name to method name (PHP 5.2 compatible)
        // min_length -> MinLength -> validateMinLength
        $ruleName = str_replace('_', ' ', $ruleName);
        $ruleName = ucwords($ruleName);
        $ruleName = str_replace(' ', '', $ruleName);
        $methodName = 'validate' . $ruleName;
        
        // Check if method exists
        if (method_exists($this, $methodName)) {
            return call_user_func(array($this, $methodName), $field, $value, $params);
        }
        
        return true; // Jika rule tidak ditemukan, anggap valid
    }
    
    /**
     * Add error message
     */
    private function addError($field, $message)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = array();
        }
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get error message
     */
    private function getErrorMessage($field, $ruleName, $params)
    {
        // Check custom message
        if (isset($this->customMessages[$field][$ruleName])) {
            $message = $this->customMessages[$field][$ruleName];
        } else {
            $message = $this->getDefaultMessage($field, $ruleName, $params);
        }
        
        // Replace placeholders
        $message = str_replace(':field', $field, $message);
        $message = str_replace(':attribute', $field, $message);
        
        if (!empty($params)) {
            foreach ($params as $index => $param) {
                $message = str_replace(':param' . ($index + 1), $param, $message);
                $message = str_replace(':' . $index, $param, $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Get default error message
     */
    private function getDefaultMessage($field, $ruleName, $params)
    {
        $messages = array(
            'required' => 'Field :field wajib diisi.',
            'email' => 'Field :field harus berupa email yang valid.',
            'min_length' => 'Field :field minimal :param1 karakter.',
            'max_length' => 'Field :field maksimal :param1 karakter.',
            'min' => 'Field :field minimal :param1.',
            'max' => 'Field :field maksimal :param1.',
            'numeric' => 'Field :field harus berupa angka.',
            'integer' => 'Field :field harus berupa bilangan bulat.',
            'alpha' => 'Field :field hanya boleh berisi huruf.',
            'alpha_numeric' => 'Field :field hanya boleh berisi huruf dan angka.',
            'alpha_dash' => 'Field :field hanya boleh berisi huruf, angka, dash dan underscore.',
            'string' => 'Field :field harus berupa teks.',
            'url' => 'Field :field harus berupa URL yang valid.',
            'ip' => 'Field :field harus berupa IP address yang valid.',
            'regex' => 'Field :field format tidak valid.',
            'same' => 'Field :field harus sama dengan :param1.',
            'different' => 'Field :field harus berbeda dengan :param1.',
            'in' => 'Field :field tidak valid.',
            'not_in' => 'Field :field tidak valid.',
            'unique' => 'Field :field sudah digunakan.',
            'exists' => 'Field :field tidak ditemukan.',
            'confirmed' => 'Field :field konfirmasi tidak cocok.',
            'date' => 'Field :field harus berupa tanggal yang valid.',
            'before' => 'Field :field harus sebelum :param1.',
            'after' => 'Field :field harus setelah :param1.',
            'match' => 'Field :field tidak cocok dengan field :param1.',
        );
        
        if (isset($messages[$ruleName])) {
            return $messages[$ruleName];
        }
        
        return 'Field :field tidak valid.';
    }
    
    /**
     * Check if validation fails
     */
    public function fails()
    {
        return count($this->errors) > 0;
    }
    
    /**
     * Check if validation passes
     */
    public function passes()
    {
        return count($this->errors) === 0;
    }
    
    /**
     * Get all errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Get first error message
     */
    public function getFirstError()
    {
        if (count($this->errors) > 0) {
            $keys = array_keys($this->errors);
            $firstField = $keys[0];
            return $this->errors[$firstField][0];
        }
        return null;
    }
    
    /**
     * Get error for specific field
     */
    public function getError($field)
    {
        if (isset($this->errors[$field])) {
            return $this->errors[$field];
        }
        return array();
    }
    
    /**
     * Get all error messages as flat array
     */
    public function getErrorMessages()
    {
        $messages = array();
        foreach ($this->errors as $field => $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $messages[] = $error;
            }
        }
        return $messages;
    }
    
    // ==================== VALIDATION RULES ====================
    
    /**
     * Required validation
     */
    private function validateRequired($field, $value, $params)
    {
        if (is_null($value)) {
            return false;
        }
        
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        
        if (is_array($value) && count($value) < 1) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Email validation
     */
    private function validateEmail($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        // PHP 5.2 compatible email validation
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        }
        
        // Fallback untuk PHP 5.2
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($pattern, $value) === 1;
    }
    
    /**
     * Min length validation
     */
    private function validateMinLength($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $minLength = isset($params[0]) ? (int)$params[0] : 0;
        return strlen($value) >= $minLength;
    }
    
    /**
     * Max length validation
     */
    private function validateMaxLength($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $maxLength = isset($params[0]) ? (int)$params[0] : 0;
        return strlen($value) <= $maxLength;
    }
    
    /**
     * Min value validation
     */
    private function validateMin($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $min = isset($params[0]) ? $params[0] : 0;
        
        if (is_numeric($value)) {
            return (float)$value >= (float)$min;
        }
        
        return strlen($value) >= (int)$min;
    }
    
    /**
     * Max value validation
     */
    private function validateMax($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $max = isset($params[0]) ? $params[0] : 0;
        
        if (is_numeric($value)) {
            return (float)$value <= (float)$max;
        }
        
        return strlen($value) <= (int)$max;
    }
    
    /**
     * Numeric validation
     */
    private function validateNumeric($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return is_numeric($value);
    }
    
    /**
     * Integer validation
     */
    private function validateInteger($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_INT) !== false;
        }
        
        return preg_match('/^-?[0-9]+$/', $value) === 1;
    }
    
    /**
     * Alpha validation (only letters)
     */
    private function validateAlpha($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return preg_match('/^[a-zA-Z\s]+$/', $value) === 1;
    }
    
    /**
     * Alpha numeric validation
     */
    private function validateAlphaNumeric($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return preg_match('/^[a-zA-Z0-9]+$/', $value) === 1;
    }
    
    /**
     * Alpha dash validation (alpha numeric + dash + underscore)
     */
    private function validateAlphaDash($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return preg_match('/^[a-zA-Z0-9_-]+$/', $value) === 1;
    }
    
    /**
     * String validation
     */
    private function validateString($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return is_string($value);
    }
    
    /**
     * URL validation
     */
    private function validateUrl($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_URL) !== false;
        }
        
        // Fallback untuk PHP 5.2
        $pattern = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        return preg_match($pattern, $value) === 1;
    }
    
    /**
     * IP validation
     */
    private function validateIp($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        if (function_exists('filter_var')) {
            return filter_var($value, FILTER_VALIDATE_IP) !== false;
        }
        
        // Fallback untuk PHP 5.2
        $pattern = '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
        return preg_match($pattern, $value) === 1;
    }
    
    /**
     * Regex validation
     */
    private function validateRegex($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $pattern = isset($params[0]) ? $params[0] : '';
        return preg_match($pattern, $value) === 1;
    }
    
    /**
     * Same validation (compare with another field)
     */
    private function validateSame($field, $value, $params)
    {
        $otherField = isset($params[0]) ? $params[0] : '';
        $otherValue = isset($this->data[$otherField]) ? $this->data[$otherField] : null;
        
        return $value === $otherValue;
    }
    
    /**
     * Different validation
     */
    private function validateDifferent($field, $value, $params)
    {
        $otherField = isset($params[0]) ? $params[0] : '';
        $otherValue = isset($this->data[$otherField]) ? $this->data[$otherField] : null;
        
        return $value !== $otherValue;
    }
    
    /**
     * In validation (value in array)
     */
    private function validateIn($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return in_array($value, $params);
    }
    
    /**
     * Not in validation
     */
    private function validateNotIn($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        return !in_array($value, $params);
    }
    
    /**
     * Unique validation (check in database)
     * Format: unique[table.column,id_field,id_value]
     */
    private function validateUnique($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        // Parse params: table.column,id_field,id_value
        $tableDotColumn = isset($params[0]) ? $params[0] : '';
        $idField = isset($params[1]) ? $params[1] : null;
        $idValue = isset($params[2]) ? $params[2] : null;
        
        if (empty($tableDotColumn)) {
            return true;
        }
        
        // Split table and column
        $parts = explode('.', $tableDotColumn);
        $table = isset($parts[0]) ? $parts[0] : '';
        $column = isset($parts[1]) ? $parts[1] : $field;
        
        if (empty($table)) {
            return true;
        }
        
        try {
            $db = Database::getInstance();
            
            // Build query
            $query = "SELECT COUNT(*) as count FROM " . $table . " WHERE " . $column . " = :value";
            
            // Add exception for update (exclude current id)
            if ($idField && $idValue) {
                $query .= " AND " . $idField . " != :id";
            }
            
            $stmt = $db->query($query, array(':value' => $value, ':id' => $idValue));
            $result = $db->fetch($stmt);
            
            return (int)$result['count'] === 0;
        } catch (Exception $e) {
            return true; // Jika error, anggap valid
        }
    }
    
    /**
     * Exists validation (check if exists in database)
     * Format: exists[table.column]
     */
    private function validateExists($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $tableDotColumn = isset($params[0]) ? $params[0] : '';
        
        if (empty($tableDotColumn)) {
            return true;
        }
        
        // Split table and column
        $parts = explode('.', $tableDotColumn);
        $table = isset($parts[0]) ? $parts[0] : '';
        $column = isset($parts[1]) ? $parts[1] : $field;
        
        if (empty($table)) {
            return true;
        }
        
        try {
            $db = Database::getInstance();
            
            $query = "SELECT COUNT(*) as count FROM " . $table . " WHERE " . $column . " = :value";
            $stmt = $db->query($query, array(':value' => $value));
            $result = $db->fetch($stmt);
            
            return (int)$result['count'] > 0;
        } catch (Exception $e) {
            return true; // Jika error, anggap valid
        }
    }
    
    /**
     * Confirmed validation (check if field_confirmation exists and matches)
     */
    private function validateConfirmed($field, $value, $params)
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = isset($this->data[$confirmField]) ? $this->data[$confirmField] : null;
        
        return $value === $confirmValue;
    }
    
    /**
     * Date validation
     */
    private function validateDate($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        if (function_exists('strtotime')) {
            return strtotime($value) !== false;
        }
        
        return true;
    }
    
    /**
     * Before date validation
     */
    private function validateBefore($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $compareDate = isset($params[0]) ? $params[0] : '';
        
        if (empty($compareDate)) {
            return true;
        }
        
        if (function_exists('strtotime')) {
            $valueTime = strtotime($value);
            $compareTime = strtotime($compareDate);
            
            return $valueTime < $compareTime;
        }
        
        return true;
    }
    
    /**
     * After date validation
     */
    private function validateAfter($field, $value, $params)
    {
        if (empty($value)) {
            return true;
        }
        
        $compareDate = isset($params[0]) ? $params[0] : '';
        
        if (empty($compareDate)) {
            return true;
        }
        
        if (function_exists('strtotime')) {
            $valueTime = strtotime($value);
            $compareTime = strtotime($compareDate);
            
            return $valueTime > $compareTime;
        }
        
        return true;
    }
    
    /**
     * Match validation (match dengan field lain)
     */
    private function validateMatch($field, $value, $params)
    {
        return $this->validateSame($field, $value, $params);
    }
}
