<?php

namespace Classes;

use PDOException;

class Db
{
    public static $instance; //instance of object
    public static $conn;     //store db obj
    public static $table;    //store table name
    public $query;           //for concatanation of query by many function
    public $counter = [];    //for conditino of checking for how many time the same code call
    public $message;
    // private $type = false;
    // public $stop = false;    //for condition of checking for some reason use this to stop
    // public $placeholders = [];

    public static function table($table)
    {
        self::$table = $table;
        if (!self::$instance) {
            self::$instance = new static();
        }

        if (!self::$conn) {
            try {
                $string = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
                self::$conn = new \PDO($string, DB_USER, DB_PASSWORD);;
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
        self::$instance->initiateQuery();
        return self::$instance;
    }

    public function initiateQuery()
    {
        // initiation purpose -> once table is setted to do crud / we need to get the ids of the row to modify
        $this->query = 'SELECT `id` FROM ' . self::$table;
    }


    public function where($prop, $sign, $val = null)
    {
        // if (!$this->stop) {

        // Add the WHERE or AND condition
        $this->query .= !empty($this->counter) ? ' AND' : ' WHERE';

        // Process the condition
        if (isset($val)) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            if (is_numeric($val)) {
                $this->query .= ' `' . $prop . '` ' . $sign .  $val . " ";
            } else {
                $this->query .= ' `' . $prop . '` ' . $sign . '"' . $val . '" ';
            }
        } else {
            $sign = filter_var($sign, FILTER_SANITIZE_STRING);
            if (is_numeric($sign)) {
                $this->query .= ' `' . $prop . '`=' . $sign . ' ';
            } else {
                $this->query .= ' `' . $prop . '`="' . $sign  . '" ';
            }
        }
        // var_dump($this->query);
        // Prepare and execute the statement
        $stm = self::$conn->prepare($this->query);
        $stm->execute();

        // set a counter to check if where is first time or not
        $this->counter = $stm->fetchAll(\PDO::FETCH_ASSOC);
        // }
        // if (empty($this->counter)) {
        //     // $this->stop = true;
        // }
        // $this->type = true;
        return self::$instance;
    }


    public function orWhere($prop, $sign, $val = null)
    {
        // if (!$this->stop) {
        // Add the WHERE or AND condition
        $this->query .= isset($this->counter) ? ' OR' : ' WHERE';

        // Process the condition
        if (isset($val)) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            if (is_numeric($val)) {
                $this->query .= ' `' . $prop . '` ' . $sign .  $val . ' ';
            } else {
                $this->query .= ' `' . $prop . '` ' . $sign . '"' . $val . '" ';
            }
        } else {
            $sign = filter_var($sign, FILTER_SANITIZE_STRING);
            if (is_numeric($sign)) {
                $this->query .= ' `' . $prop . '`=' . $sign;
            } else {
                $this->query .= ' `' . $prop . '`="' . $sign  . '"';
            }
        }

        $stm = self::$conn->prepare($this->query);
        $stm->execute();
        $this->counter = $stm->fetchAll(\PDO::FETCH_ASSOC);
        // // }
        // if (empty($this->counter)) {
        //     // $this->stop = true;
        // }
        // $this->type = true;
        return self::$instance;
    }

    public function create(array $data)
    {
        // $stmt = self::$conn->prepare("SELECT * FROM " . self::$table);
        // $stmt->execute();

        // // Get metadata for each column
        // echo "<pre>";
        // $tableCols = [];
        // $columnCount = $stmt->columnCount();
        // for ($i = 0; $i < $columnCount; $i++) {
        //     $meta = $stmt->getColumnMeta($i);
        //     // $isEssential = !($meta['flags'] & \PDO::NULL_FLAG);
        //     // $isEssential = !in_array('pdo_null_flag', $meta['flags']);
        //     // $isNotEssential = ($meta['pdo_type'] === \PDO::PARAM_NULL) || in_array('pdo_null_flag', $meta['flags']);
        //     $isEssential = !(

        //         isset($meta['default']) ||
        //         strtoupper($meta['name']) === 'ID'
        //         // Change 'CREATED_AT' to your timestamp column name

        //     );
        //     echo "Column: " . $meta['name'] . "\n";
        //     echo "Essential: " . ($isEssential ? 'Yes' : 'No') . "\n";
        //     echo "\n";
        //     print_r($meta);
        // }

        // Get table structure to decide which cols are necessary
        // reset

        if (self::$instance->type) {
            return 'Syntax errr';
        }
        $this->reset();

        $stmt = self::$conn->prepare("DESCRIBE " . self::$table);
        $stmt->execute();
        $columns = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $tableColumns = [];
        foreach ($columns as $column) {
            $tableColumns[] = [
                'name' => $column['Field'],
                'type' => $column['Type'],
                'unique' => $column['Key'] == 'UNI',
                'user_input' => $column['Extra'] === 'auto_increment' || !empty($column['Default']) || ($column['Null']) != "NO",
                // Add more properties as needed
            ];
        }
        // Check $table col fetch is success so that the process is fine
        if (empty($tableColumns)) {
            return "Unable to fetch table columns.";
        }
        // reassign $tableColumns with necessary col
        $tableColumns = array_filter($tableColumns, function ($col) {
            return $col['user_input'] == false;
        });

        // Validate that the required keys are present in user input array
        foreach ($tableColumns as $col) {
            if (!array_key_exists($col['name'], $data)) {
                return "{$col['name']} is Required";
            }
        }
        // Validate that values are not empty
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return "Invalid input data. Empty value for key: $key";
            }
        }
        // reassign which has Unique key
        $tableColumns = array_filter($tableColumns, function ($col) {
            return $col['unique'];
        });
        // Get Name of cols which is unique
        $colNames = [];
        foreach ($tableColumns as $val) {
            $colNames[] = $val['name'];
        }
        // Select from table with the unique field and check if data exist
        $query = "SELECT id From " . self::$table . " WHERE ";
        $conditions = [];
        foreach ($data as $key => $val) {
            if (in_array($key, $colNames)) {
                if (is_numeric($val)) {
                    $conditions[] = "`" . $key . "` =" . $val . " ";
                } else {
                    $conditions[] = "`" . $key . "` =" . "'" . $val . "'";
                }
            }
        }
        // Check query is not empty
        if (!empty($conditions)) {
            $query .= implode(" OR ", $conditions);
            $stm = self::$conn->prepare($query);
            $stm->execute();
            $counter = $stm->fetchAll(\PDO::FETCH_ASSOC);
        }
        // WHen data exist
        if (!empty($counter)) {
            return "Data already exists";
        }

        // Do inserting query
        $col_names = '`';
        $col_values = ' ) VALUES ( ';
        self::$instance->query = "INSERT INTO " . self::$table . " ( ";
        $count = 0;
        foreach ($data as $key => $value) {
            $count++;
            if (is_numeric($value)) {
                if ($count < count($data)) {
                    $col_names .= $key . '` , `';
                    $col_values .= '' . $value . ' , ';
                } else {
                    $col_names .= $key . '` ';
                    $col_values .= '' . $value . ')';
                }
            } else {
                if ($count < count($data)) {
                    $col_names .= $key . '` , `';
                    $col_values .= '"' . $value . '" , ';
                } else {
                    $col_names .= $key . '` ';
                    $col_values .= '"' . $value . '")';
                }
            }
        }
        self::$instance->query .= $col_names . $col_values;
        $stm = self::$conn->prepare(self::$instance->query);

        // check inserting success
        if ($stm->execute()) {
            return "INserted Successfully";
        }
        // reset
        return "Inserting Fail";
    }

    public function update($array)
    {
        if (!isset($this->counter) || empty($this->counter)) {
            $this->reset();
            return [];
        }
        $complete = 0;
        foreach ($this->counter as $key => $id) {
            $query = 'UPDATE ' . self::$table . ' SET ';
            $count = 0;
            foreach ($array as $col => $value) {
                $count++;
                if ($count < count($array)) {
                    if (is_numeric($value)) {
                        $query .= '`' . $col . '`=' . $value . ',';
                    } else {
                        $query .= '`' . $col . '`=`' . $value . '",';
                    }
                } else {
                    if (is_numeric($value)) {
                        $query .= '`' . $col . '`=' . $value;
                    } else {
                        $query .= '`' . $col . '`=`' . $value . '`';
                    }
                }
            }
            $query .= ' WHERE `id`=' . $id['id'];
            // var_dump($query);
            $stm = self::$conn->prepare($query);
            if ($stm->execute()) {
                $complete += $stm->rowCount();
            }
        }
        var_dump($this->counter);
        $this->reset();
        if ($complete > 0) {
            return $complete . 'Rows Affected';
        }
        return "Fail";
    }

    public function store()
    {

        return self::$instance;
    }

    public function all()
    {
        // if ($this->type) {
        //     return 'Syntax errr';
        // }
        $stm = self::$conn->prepare('SELECT * FROM ' . self::$table);
        $isSuccess = $stm->execute();
        if ($isSuccess) {
            $data = $stm->fetchAll(\PDO::FETCH_OBJ);
            if (is_array($data) && count($data) > 0) {
                return $data;
            }
        }
        // var_dump();
        return [];
    }

    public static function find($id)
    {
        try {
            $stm = self::$conn->prepare('SELECT id FROM ' . self::$table . ' WHERE id=' . $id);
            var_dump($stm);
            // $stm->bindParam(':id', $id, \PDO::PARAM_INT);
            $isSuccess = $stm->execute();
            if ($isSuccess) {
                $data = $stm->fetchAll(\PDO::FETCH_ASSOC);
                if (is_array($data) && count($data) > 0) {
                    self::$instance->counter = $data;
                    // return $this->get();
                    // $this->type = true;
                    // // reset
                    // $this->reset();
                    return self::$instance;
                }
            }
        } catch (\PDOException $e) {
            self::$instance->reset();
            // Handle database error
            return [];
        }

        // reset
        self::$instance->reset();
        return [];
    }

    public function get()
    {
        var_dump($this->query);
        if (!isset($this->counter) || empty($this->counter)) {
            $this->reset();
            return [];
        }

        $ids = array_map(function ($obj) {
            return $obj['id'];
        }, $this->counter);

        try {
            $placeholders = implode(',', $ids);
            $sql = self::$conn->prepare('SELECT * FROM ' . self::$table . ' WHERE id IN (' . $placeholders . ')');
            $isSuccess = $sql->execute();
            if ($isSuccess) {
                $data = $sql->fetchAll(\PDO::FETCH_ASSOC);
                if (is_array($data) && count($data) > 0) {
                    return $data;
                }
            }
        } catch (\PDOException $e) {
            // Handle database error
            $this->reset();
            return [];
        }

        $this->reset();
        return [];
    }

    public function destroy()
    {
        if (!empty($this->counter)) {
            foreach ($this->counter as $id) {
                $this->query = "DELETE FROM " . self::$table . ' WHERE id=:id';
                $stm = self::$conn->prepare($this->query);
                $stm->bindParam(':id', $id['id'], \PDO::PARAM_INT);
                $stm->execute();
            }
            $affectedRows = $stm->rowCount();
            if ($affectedRows === 0) {
                $this->reset();
                // Handle the case where the record is not found
                return "Record not found1.";
            } else {
                $this->reset();
                // Record deleted successfully
                return "Record deleted successfully.";
            }
        } else {
            // Record deleted successfully
            $this->reset();
            return "Record not found.";
        }
    }

    // Reset Variable
    public function reset()
    {
        $this->query = '';
        $this->counter = [];
        // $this->stop = false;
        // $this->type = false;
    }

    public function __destruct()
    {
        // if ($this->type === 'find') {
        //     if (!empty($this->counter)) {
        //         return $this->get();
        //     } else {
        //         return [];
        //     }
        // }
        $this->reset();
    }
}
