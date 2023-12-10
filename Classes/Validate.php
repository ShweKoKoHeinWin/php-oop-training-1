<?php

namespace Classes;

class Validate
{
    private static $errors = [];

    public static function rules($data, $rules)
    {
        self::$errors = [];
        foreach ($rules as $field => $fieldRules) {
            $fieldValue = isset($data[$field]) ? $data[$field] : null;

            $rulesArray = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);

            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $method = strtolower($ruleParts[0]);
                $params = isset($ruleParts[1]) ? $ruleParts[1] : null;
                if (method_exists('Classes\Validate', $method)) {
                    self::$method($field, $fieldValue, $params);
                }
            }
        }
    }

    protected static function required($field, $value, $params = null)
    {

        if (empty($value)) {
            self::addError($field, "The $field field is required.");
        }
    }

    protected static function max($field, $value, $params)
    {
        if (strlen($value) > $params) {
            self::addError($field, "The $field field must be at most $params characters long.");
        }
    }

    protected static function min($field, $value, $params)
    {
        if (strlen($value) < $params) {
            self::addError($field, "The $field field must be at least $params characters long.");
        }
    }

    protected static function email($field, $value, $params = null)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            self::addError($field, "The $field field must be a valid email address.");
        }
    }

    protected static function addError($field, $msg)
    {
        self::$errors[$field][] = $msg;
    }

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function encrypt($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function verify($password, $hashedPassword)
    {
        if (password_verify($password, $hashedPassword)) {
            return true;
        } else {
            // Password is incorrect
            return false;
        }
    }

    public static function confirm($field, $value, $params = null)
    {
        if ($value != $_POST['confirm-password']) {
            self::addError($field, "The Confirm Password field must same as $field.");
        }
    }
}

// Example usage:
// $data = [
//     'username' => 'john_doe',
//     'email' => 'john@example.com',
// ];

// $rules = [
//     'username' => 'required|max:10',
//     'email' => 'required|email',
// ];

// Validate::rules($data, $rules);

// $errors = Validate::getErrors();

// if (!empty($errors)) {
//     print_r($errors);
// } else {
//     // Data is valid, proceed with processing the form
// }



// class Validate
// {
//     public static $error = [];
//     public static function rules($array)
//     {
//         self::$error = [];
//         foreach ($array as $key => $values) {
//             if (is_array($values)) {
//                 foreach ($values as $value) {
//                     $method = strtolower($value);
//                     if (strpos($method, 'max') || strpos($method, 'min')) {
//                         $action = explode(':', $method);
//                         self::$action[0]($field, $action[1]);
//                     }
//                     self::$method($key);
//                 }
//             } else if (is_string($values)) {
//                 $values = explode('|', $values);
//                 foreach ($values as $value) {
//                     $method = strtolower($value);
//                     if (strpos($method, 'max') != false || strpos($method, 'min') != false) {
//                         $action = explode(':', $method);
//                         self::$action[0]($action[1]);
//                     } else {
//                         self::$method($key);
//                     }
//                 }
//             }
//         }
//     }

//     protected static function required($field)
//     {
//         if (!isset($field) || empty($field)) {
//             self::addError($field, "The $field field is required.");
//         }
//     }

//     protected static function max($field, $num)
//     {
//         if (strlen($field) > $num) {
//             self::addError($field, "The $field field must max $num.");
//         }
//     }

//     protected static function email($field)
//     {
//         if (!filter_var($field, FILTER_VALIDATE_EMAIL)) {
//             self::addError($field, "The $field must be a valid email address.");
//         }
//     }

//     protected static function addError($field, $msg)
//     {
//         self::$error[$field] = $msg;
//     }

//     public static function __callStatic($name, $value)
//     {
//         if (strpos($name, 'max')) {
//             $method = explode(':', $name);
//             $para = $method[1];
//             $method = $method[0];
//             self::$method($para);
//         }
//     }
// }
