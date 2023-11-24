<?php

namespace Classes;

use PDOException;

class Db
{
    protected static $instance;
    protected static $conn;
    protected static $table;
    protected $query;
    protected $counter;
    protected $message;
    // protected $placeholders = [];

    public static function table($table)
    {
        self::$table = $table;
        if (!self::$instance) {
            self::$instance = new self();
        }

        if (!self::$conn) {
            try {
                $string = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;
                self::$conn = new \PDO($string, DB_USER, DB_PASSWORD);
                self::$instance->initiateQuery();
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
        return self::$instance;
    }

    protected function initiateQuery()
    {
        // initiation purpose -> once table is setted to do crud / we need to get the ids of the row to modify
        $this->query = 'SELECT `id` FROM ' . self::$table;
    }


    public function where($prop, $sign, $val = null)
    {
        // Add the WHERE or AND condition
        $this->query .= isset($this->counter) ? ' AND' : ' WHERE';

        // Process the condition
        if (isset($val)) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            $this->query .= ' `' . $prop . '` ' . $sign . ' "' . $val . '"';
        } else {
            $sign = filter_var($sign, FILTER_SANITIZE_STRING);
            $this->query .= ' `' . $prop . '`="' . $sign  . '"';
        }

        // Prepare and execute the statement
        $stm = self::$conn->prepare($this->query);
        $stm->execute();

        // set a counter to check if where is first time or not
        $this->counter = $stm->fetchAll(\PDO::FETCH_ASSOC);
        return self::$instance;
    }


    public function orWhere($prop, $sign, $val = null)
    {
        // Add the WHERE or AND condition
        $this->query .= isset($this->counter) ? ' OR' : ' WHERE';

        // Process the condition
        if (isset($val)) {
            $val = filter_var($val, FILTER_SANITIZE_STRING);
            $this->query .= ' `' . $prop . '` ' . $sign . ' "' . $val . '"';
        } else {
            $sign = filter_var($sign, FILTER_SANITIZE_STRING);
            $this->query .= ' `' . $prop . '`="' . $sign  . '"';
        }

        $stm = self::$conn->prepare($this->query);
        $stm->execute();
        $this->counter = $stm->fetchAll(\PDO::FETCH_ASSOC);
        return self::$instance;
    }

    public static function create(array $data)
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
                $conditions[] = "`" . $key . "` =" . "'" . $val . "'";
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
            echo 'ues';
            return "Data already exists";
        }

        // Do inserting query
        $col_names = '`';
        $col_values = ' ) VALUES ( ';
        self::$instance->query = "INSERT INTO " . self::$table . " ( ";
        $count = 0;
        foreach ($data as $key => $value) {
            $count++;
            if ($count < count($data)) {
                $col_names .= $key . '` , `';
                $col_values .= '"' . $value . '" , ';
            } else {
                $col_names .= $key . '` ';
                $col_values .= '"' . $value . '")';
            }
        }
        self::$instance->query .= $col_names . $col_values;
        $stm = self::$conn->prepare(self::$instance->query);

        // check inserting success
        if ($stm->execute()) {
            echo "INserted Successfully";
            return;
        }
        self::$instance->message = "error";
        return "Inserting Fail";
    }

    public function update()
    {

        return self::$instance;
    }

    public function store()
    {

        return self::$instance;
    }

    public function all()
    {
        $stm = self::$conn->prepare('SELECT * FROM ' . self::$table);
        $isSuccess = $stm->execute();
        if ($isSuccess) {
            $data = $stm->fetchAll(\PDO::FETCH_OBJ);
            if (is_array($data) && count($data) > 0) {
                return $data;
            }
        }
        return [];
    }

    public function get()
    {

        if (!isset($this->counter) || empty($this->counter)) {
            return [];
        }
        $ids = array_map(function ($obj) {
            return $obj['id'];
        }, $this->counter);

        $placeholders = implode(',', $ids);
        $sql = self::$conn->prepare('SELECT * FROM ' . self::$table . ' WHERE id IN (' . $placeholders . ')');
        $isSuccess = $sql->execute();
        if ($isSuccess) {
            $data = $sql->fetchAll(\PDO::FETCH_OBJ);
            if (is_array($data) && count($data) > 0) {
                return $data;
            }
        }
        return [];
    }
}
