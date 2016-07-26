<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/db.php
 *
 * Робота з базою даних.
 * Версія 1.0.1 (25.04.2013 - додано getAllData(), getAllDataByFieldInArray(), language(), latterUAtoEN())
 * Версія 1.0.2 (16.07.2014) - додано getCount(), register(); розширено (можуть приймати в якості умови масив): getAllDataById(), getAllDataByFieldInArray(+сорутвання)
 * Версія 1.0.3 (06.11.2014) - додано getQuery(), (12.11.2014) до getAllDataById(), getAllDataByFieldInArray() додано авто підтримку до умови IN
 * Версія 2.0 (28.09.2015) - переписано код getAllDataById(), getAllDataByFieldInArray(), getCount(). Додано службову функцію makeWhere(). Додано запити по конструкції: prefix(), select(), join(), order(), limit(), get().
 * Версія 2.0.1 (26.03.2016) - до get() додано параметр debug, що дозволяє бачити кінцевий запит перед запуском, виправлено помилку декількаразового запуску get()
 * Версія 2.0.2 (01.04.2016) - до makeWhere() додано параметр сортування НЕ '!'
 * Версія 2.0.3 (26.07.2016) - адаптовано до php7
 */

class Db {

    private $connects = array();
    private $current = 0;
    private $result;

    /*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function __construct($cfg){
        $this->newConnect($cfg['host'], $cfg['user'], $cfg['password'], $cfg['database']);
    }

    /**
     * Створюємо з'єднання
     *
     * @param <string> $host назва серверу
     * @param <string> $user ім'я користувача
     * @param <string> $password пароль
     * @param <string> $database назва бази даних
     */
    function newConnect($host, $user, $password, $database){
        $this->connects[] = new mysqli($host, $user, $password, $database);
        $this->current = count($this->connects) - 1;
        $this->executeQuery('SET NAMES utf8');
    }

    /**
     * Виконуємо запит
     *
     * @param <string> $query запит
     */
    function executeQuery($query){
        $result = $this->connects[$this->current]->query($query);
        if(!$result) {
            echo $this->connects[$this->current]->error;
        } else {
            $this->result = $result;
        }

    }

    function updateRow($table, $changes, $id, $row_key = 'id'){
        $update = "UPDATE ".$table." SET ";
        foreach ($changes as $key => $value){
            $value = $this->sanitizeString($value);
            $update .= "`{$key}` = '{$value}',";
        }
        $update = substr($update, 0, -1);
        $update .= "WHERE `{$row_key}` = '{$id}'";
        $this->executeQuery($update);
        if($this->affectedRows() > 0){
            return true;
        } else return false;
    }

    function insertRow($table, $changes){
        $update = "INSERT INTO ".$table." ( ";
        $values = '';
        foreach ($changes as $key => $value){
            $value = $this->sanitizeString($value);
            $update .= '`' . $key . '`, ';
            $values .= "'{$value}', ";
        }
        $update = substr($update, 0, -2);
        $values = substr($values, 0, -2);
        $update .= ' ) VALUES ( ' . $values . ' ) ';
        $this->executeQuery($update);
        if($this->affectedRows() > 0){
            return true;
        } else return false;
    }

    function getLastInsertedId(){
        return $this->connects[$this->current]->insert_id;
    }

    function deleteRow($table = '', $id, $id_name = 'id'){
        if($table != '' && $id != ''){
            $this->executeQuery("DELETE FROM {$table} WHERE `{$id_name}` = '{$id}'");
            if($this->affectedRows() > 0){
                return true;
            } else return false;
        } else return false;
    }

    /**
     * Отримуємо рядки
     *
     * @return <array>
     */
    function getRows($type = ''){
        if($this->result->num_rows > 1 || $type == 'array'){
            $objects = array();
            while($obj = $this->result->fetch_object()){
                array_push($objects, $obj);
            }
            return $objects;
        }

        return $this->result->fetch_object();
    }

    /**
     * Отримуємо кількість рядків
     *
     * @return <int>
     */
    function numRows(){
        return $this->result->num_rows;
    }

    /**
     * Отримуємо кількість задіяних рядків
     *
     * @return <int>
     */
    function affectedRows(){
        return $this->result;
    }

    /**
     * Очистити рядок
     *
     * @param <string> $data дані
     *
     * @return <string>
     */
    function sanitizeString($data){
        if(get_magic_quotes_gpc()){
            $data = stripslashes($data);
        }

        $data = $this->connects[$this->current]->escape_string($data);

        return $data;
    }

    function mysql_real_escape_string($q){
        return $this->connects[$this->current]->real_escape_string($q);
    }

    public function getQuery($query = false, $getRows = '')
    {
        if($query){
            $this->executeQuery($query);
            if($this->numRows() > 0){
                return $this->getRows($getRows);
            }
        }
        return false;
    }

    /**
     * Допоміжні функції
     */
    function getAllData($table = false, $order = ''){
        if($table){
            if($order != '') $order = ' ORDER BY '.$order;
            $this->executeQuery("SELECT * FROM {$table} {$order}");
            if($this->numRows() > 0){
                return $this->getRows('array');
            } else return false;
        } else return false;
    }

    function getAllDataById($table = '', $key, $row_key = 'id'){
        if($table != ''){
            $where = $this->makeWhere($key, $row_key);
            if($where != ''){
                $this->executeQuery("SELECT * FROM {$table} WHERE {$where}");
                if($this->numRows() == 1){
                    return $this->getRows();
                }
            }
        }
        return false;
    }

    function getAllDataByFieldInArray($table = '', $key, $row_key = 'id', $order = ''){
        if($table != ''){
            $where = $this->makeWhere($key, $row_key);
            if($where != ''){
                if(is_array($key) && $row_key != '') $where .= ' ORDER BY '.$row_key;
                elseif($order != '') $where .= ' ORDER BY '.$order;
                $this->executeQuery("SELECT * FROM {$table} WHERE {$where}");
                if($this->numRows() > 0){
                    return $this->getRows('array');
                }
            }
        }
        return false;
    }

    function getCount($table = '', $key = '', $row_key = 'id'){
        if($table != ''){
            $where = $this->makeWhere($key, $row_key);
            if($where != ''){
                $where = "WHERE {$where}";
            }
            $this->executeQuery("SELECT count(*) as count FROM {$table} {$where}");
            if($this->numRows() == 1){
                $count = $this->getRows();
                return $count->count;
            }
        }
        return null;
    }

    private function makeWhere($data, $row_key = 'id', $prefix = false)
    {
        $where = '';
        if(is_array($data)){
            foreach ($data as $key => $value) {
                if(!is_numeric($key) && $key != ''){
                    if($prefix){
                        $where .= "{$prefix}.{$key}";
                    } else {
                        $where .= "`{$key}`";
                    }
                    if(is_array($value)){
                        $where .= " IN ( ";
                        foreach ($value as $v) {
                            $where .= "'{$v}', ";
                        }
                        $where = substr($where, 0, -2);
                        $where .= ') AND ';
                    } elseif($value != '') {
                        $value = $this->sanitizeString($value);
                        if($value[0] == '%'){
                            $where .= " LIKE '{$value}%' AND ";
                        } elseif($value[0] == '>'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= " >= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= " > '{$value}' AND ";
                            }
                        } elseif($value[0] == '<'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= " <= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= " < '{$value}' AND ";
                            }
                        } else {
                            if($value[0] == '#'){
                                $value = substr($value, 1);
                                $where .= " = {$value} AND ";
                            } elseif($value[0] == '!'){
                                $value = substr($value, 1);
                                $where .= " != '{$value}' AND ";
                            } else {
                                $where .= " = '{$value}' AND ";
                            }
                        }
                    } else $where .= " = '' AND ";
                }
            }
            if($where != ''){
                $where = substr($where, 0, -4);
            }
        } elseif($data != ''){
            if($prefix) {
                $row_key = "{$prefix}.{$row_key}";
            } else {
                $row_key = "`{$row_key}`";
            }
            $data = $this->sanitizeString($data);
            if($data[0] == '#'){
                $data = substr($data, 1);
                $where = "{$row_key} = {$data}";
            } else {
                $where = "{$row_key} = '{$data}'";
            }
        }
        return $where;
    }

    private $query_table = false;
    private $query_prefix = false;
    private $query_fields = '*';
    private $query_where = false;
    private $query_join = array();
    private $query_order = false;
    private $query_order_prefix = false;
    private $query_limit = false;

    public function prefix($prefix)
    {
        if($this->query_prefix == false){
            $this->query_prefix = $prefix;
        } else {
            exit('Work with DB. Prefix of table name has to be set before function select!');
        }
    }

    public function select($table, $fields = '*', $key = '', $row_key = 'id')
    {
        $table = preg_replace("|[\s]+|", " ", $table);
        $table = explode(' ', $table);
        if(count($table) == 3 && ($table[1] == 'as' || $table[1] == 'AS' || $table[1] == 'As')){
            $this->query_prefix = $table[2];
        }
        $this->query_table = $table[0];
        $this->query_fields = $fields;
        if($this->query_prefix == false) $this->query_prefix = $table[0];
        $this->query_where = $this->makeWhere($key, $row_key, $this->query_prefix);
    }

    public function join($table, $fields, $key = '', $row_key = 'id', $type = 'LEFT')
    {
        $table = preg_replace("|[\s]+|", " ", $table);
        $table = explode(' ', $table);
        $prefix = $table[0];
        if(count($table) == 3 && ($table[1] == 'as' || $table[1] == 'AS' || $table[1] == 'As')){
            $prefix = $table[2];
        }
        $join = new stdClass();
        $join->table = $table[0];
        $join->prefix = $prefix;
        $join->fields = $fields;
        $join->where = $this->makeWhere($key, $row_key, $prefix);
        $join->type = $type;
        $this->query_join[] = $join;
    }

    public function order($order, $prefix = false)
    {
        $this->query_order_prefix = $prefix;
        $this->query_order = $order;
    }

    public function limit($limit, $offset = 0)
    {
        $this->query_limit = 'LIMIT '.$limit;
        if($offset > 0) $this->query_limit .= ', '.$offset;
    }

    /**
     * Виконати запит до БД
     *
     * @param <string> $type - тип запиту:
     *                       auto   якщо один рядок об'єкт, якщо декілька - масив об'єктів
     *                       single тільки один об'єкт. Якщо більше ніж один - false
     *                       array  завжди масив об'єктів
     *                       count  повертає кількість знайдених рядків згідно запиту
     * @param <bool> $clear очистити дані запиту (для нового)
     *
     * @return <object>
     */
    public function get($type = 'auto', $clear = true, $debug = false, $get = true)
    {
        if($this->query_table){
            $data = NULL;
            if($type == 'count'){
                $data = 0;
                $where = '';
                if($this->query_prefix){
                    $where = "AS {$this->query_prefix} ";
                }
                if($this->query_where != '') {
                    $where .= 'WHERE '.$this->query_where;
                }
                $row = $this->getQuery("SELECT count(*) as count FROM {$this->query_table} {$where}");
                if(is_object($row)){
                    $data = $row->count;
                }
            } else {
                $query = "SELECT ";
                // fields
                if(!empty($this->query_join)){
                    if(!is_array($this->query_fields)) $this->query_fields = explode(',', $this->query_fields);
                    $prefix = $this->query_table;
                    if($this->query_prefix) $prefix = $this->query_prefix;
                    foreach ($this->query_fields as $field) {
                        if($field != ''){
                            $field = trim($field);
                            $query .= $prefix.'.'.$field.', ';
                        }
                    }
                    foreach ($this->query_join as $join) {
                        if(!is_array($join->fields)) $join->fields = explode(',', $join->fields);
                        foreach ($join->fields as $field) {
                            if($field != ''){
                                $field = trim($field);
                                $query .= $join->prefix.'.'.$field.', ';
                            }
                        }
                    }
                    $query = substr($query, 0, -2);
                } else {
                    $query .= $this->query_fields;
                }

                //from
                $query .= " FROM `{$this->query_table}` ";
                if($this->query_prefix){
                    $query .= "AS {$this->query_prefix} ";
                }

                //join
                if(!empty($this->query_join)){
                    foreach ($this->query_join as $join) {
                        $query .= "{$join->type} JOIN `{$join->table}` ";
                        if($join->prefix != $join->table) {
                            $query .= "AS {$join->prefix} ";
                        }
                        $query .= "ON {$join->where} ";
                    }
                }

                //where
                if($this->query_where){
                    $query .= "WHERE {$this->query_where} ";
                }

                //order
                if($this->query_order){
                    if($this->query_prefix || $this->query_order_prefix){
                        if($this->query_order_prefix == false) $this->query_order_prefix = $this->query_prefix;
                        $query .= "ORDER BY {$this->query_order_prefix}.{$this->query_order} ";
                    } else {
                        $query .= "ORDER BY {$this->query_order} ";
                    }
                }

                //limit
                if($this->query_limit){
                    $query .= $this->query_limit;
                }

                if($debug) echo($query);

                if($get)
                {
                    //get data
                    if($type == 'single'){
                        $this->executeQuery($query);
                        if($this->numRows() == 1){
                            $data = $this->getRows();
                        }
                    } else {
                        $data = $this->getQuery($query, $type);
                    }
                }
            }
            if($clear){
                $this->clear();
            }
            return $data;
        }
        return false;
    }

    public function clear()
    {
        $this->query_table = false;
        $this->query_prefix = false;
        $this->query_fields = '*';
        $this->query_where = false;
        $this->query_join = array();
        $this->query_order = false;
        $this->query_limit = false;
    }

    public function register($do, $additionally = '', $user = 0)
    {
        $register = $this->getAllDataById('wl_user_register_do', $do, 'name');
        if($register){
            $data['date'] = time();
            $data['do'] = $register->id;
            if($user == 0) $data['user'] = $_SESSION['user']->id;
            else $data['user'] = $user;
            $data['additionally'] = $additionally;
            if($this->insertRow('wl_user_register', $data)) return true;
        }
        return false;
    }

}

?>