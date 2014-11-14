<?php  if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/db.php
 *
 * Робота з базою даних.
 * Версія 1.0.1 (25.04.2013 - додано getAllData(), getAllDataByFieldInArray(), language(), latterUAtoEN())
 * Версія 1.0.2 (16.07.2014) - додано getCount(), register(); розширено (можуть приймати в якості умови масив): getAllDataById(), getAllDataByFieldInArray(+сорутвання)
 * Версія 1.0.3 (06.11.2014) - додано getQuery(), (12.11.2014) до getAllDataById(), getAllDataByFieldInArray() додано авто підтримку до умови IN
 */
 
class Db {

    private $connects = array();
    private $current = 0;
    private $result;
    
    /*
     * Отримуємо дані для з'єднання з конфігураційного файлу
     */
    function Db($cfg){
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
    
    function getAllDataById($table = '', $id, $id_name = 'id'){
        if($table != '' && is_array($id)){
            $where = '';
            foreach ($id as $key => $value) {
                if(!is_numeric($key) && $key != ''){
                    if(is_array($value)){
                        $where .= "`{$key}` IN ( ";
                        foreach ($value as $v) {
                            $where .= "'{$v}', ";
                        }
                        $where = substr($where, 0, -2);
                        $where .= ') AND ';
                    } elseif($value != '') {
                        $value = $this->sanitizeString($value);
                        if($value[0] == '%'){
                            $where .= "`{$key}` LIKE '{$value}%' AND ";
                        } elseif($value[0] == '>'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` >= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` > '{$value}' AND ";
                            }
                        } elseif($value[0] == '<'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` <= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` < '{$value}' AND ";
                            }
                        } else $where .= "`{$key}` = '{$value}' AND ";
                    } else $where .= "`{$key}` = '' AND ";
                }
            }
            if($where != ''){
                $where = substr($where, 0, -4);
                $this->executeQuery("SELECT * FROM {$table} WHERE {$where}");
                if($this->numRows() == 1){
                    return $this->getRows();
                }
            }
        } elseif($table != '' && $id != ''){
            $id = $this->sanitizeString($id);
            $this->executeQuery("SELECT * FROM {$table} WHERE `{$id_name}` = '{$id}'");
            if($this->numRows() == 1){
                return $this->getRows();
            }
        }
        return false;
    }
    
    function getAllDataByFieldInArray($table = '', $id, $id_name = 'id', $order = ''){
        if($table != '' && is_array($id)){
            $where = '';
            foreach ($id as $key => $value) {
                if(!is_numeric($key) && $key != ''){
                    if(is_array($value)){
                        $where .= "`{$key}` IN ( ";
                        foreach ($value as $v) {
                            $where .= "'{$v}', ";
                        }
                        $where = substr($where, 0, -2);
                        $where .= ') AND ';
                    } elseif($value != '') {
                        $value = $this->sanitizeString($value);
                        if($value[0] == '%'){
                            $where .= "`{$key}` LIKE '{$value}%' AND ";
                        } elseif($value[0] == '>'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` >= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` > '{$value}' AND ";
                            }
                        } elseif($value[0] == '<'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` <= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` < '{$value}' AND ";
                            }
                        } else $where .= "`{$key}` = '{$value}' AND ";
                    } else $where .= "`{$key}` = '' AND ";
                }
            }
            if($where != ''){
                $where = substr($where, 0, -4);
                if($id_name != '') $where .= ' ORDER BY '.$id_name;
                $this->executeQuery("SELECT * FROM {$table} WHERE {$where}");
                if($this->numRows() > 0){
                    return $this->getRows('array');
                } else return false;
            }
        } elseif($table != '' && $id != ''){
            $id = $this->sanitizeString($id);
            if($order != '') $order = ' ORDER BY '.$order;
            $this->executeQuery("SELECT * FROM {$table} WHERE `{$id_name}` = '{$id}' {$order}");
            if($this->numRows() > 0){
                return $this->getRows('array');
            } else return false;
        } else return false;
    }

    function getCount($table = '', $id = array()){
        if($table != ''){
            $where = '';
            if(!empty($id)){
                foreach ($id as $key => $value) {
                    if(!is_numeric($key) && $key != '' && $value != ''){
                        $value = $this->sanitizeString($value);
                        if($value[0] == '%'){
                            $where .= "`{$key}` LIKE '{$value}%' AND ";
                        } elseif($value[0] == '>'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` >= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` > '{$value}' AND ";
                            }
                        } elseif($value[0] == '<'){
                            if($value[1] == '='){
                                $value = substr($value, 2);
                                $where .= "`{$key}` <= '{$value}' AND ";
                            } else {
                                $value = substr($value, 1);
                                $where .= "`{$key}` < '{$value}' AND ";
                            }
                        } else $where .= "`{$key}` = '{$value}' AND ";
                    }
                }
                if($where != ''){
                    $where = substr($where, 0, -4);
                    $this->executeQuery("SELECT count(*) as count FROM {$table} WHERE {$where}");
                }
            } else {
                $this->executeQuery("SELECT count(*) as count FROM {$table}");
            }
            if($this->numRows() == 1){
                $count = $this->getRows();
                return $count->count;
            }
        }
        return null;
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
    
    function latterUAtoEN($text){
        $text = mb_strtolower($text, "utf-8");      
        $ua = array('а', 'б', 'в', 'г', 'ґ', 'д', 'е', 'є', 'ж', 'з', 'и', 'і', 'ї', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь', 'ю', 'я' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '_', ' ', '`', '~', '!', '@', '#', '$', '%', '^', '&', '"', ',', '\.', '\?', '/', ';', ':', '\'', 'ы', 'ё');
        $en = array('a', 'b', 'v', 'h', 'g', 'd', 'e', 'e', 'zh', 'z', 'y', 'i', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'c', 'ch', 'sh', 'sch', '', 'u', 'ja' , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '-', '-', '-', '*', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '*', 'y', 'e');
        for($i = 0; $i < count($ua); $i++){
            $text = mb_eregi_replace($ua[$i], $en[$i], $text);
        }
        return $text;
    }

}

?>
