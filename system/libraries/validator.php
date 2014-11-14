<?php if (!defined('SYS_PATH')) exit('Access denied');

/*
 * Шлях: SYS_PATH/libraries/validator.php
 *
 * Перевіряємо дані
 */

class validator {

    private $errors = array();

    /**
     * Назначаємо правила
     *
     * @param <string> $field назва поля
     * @param <string> $data дані
     * @param <string> $rules правила, наприклад 'required|3..10'
     */
    function setRules($field, $data, $rules){
        $rules = explode('|', $rules);
        foreach ($rules as $rule){
            if(method_exists('validator', $rule)){
                $this->$rule($field, $data);
            }
            if(strpos($rule, '..')){
                $min = intval(substr($rule,0,strpos($rule,'..'))); //intval(strstr($rule, '..', true));
                $max = intval(substr(strstr($rule,'..'), 2));
                $this->valid_length($field, $data, $min, $max);
            }
        }
    }

    /**
     * Перевіряємо якщо валідація пройдена
     *
     * @return <boolean>
     */
    function run(){
        if(empty($this->errors)){
            return true;
        }

        return false;
    }

    /**
     * Перевіряємо довжину даних
     *
     * @param <string> $field
     * @param <string> $data
     * @param <int> $min
     * @param <int> $max
     */
    private function valid_length($field, $data, $min, $max){
        if(strlen($data) < $min || strlen($data) > $max){
            array_push($this->errors, $field.' повинно містити від '.$min.' до '.$max.' символів.');
        }
    }

    /**
     * Перевіряємо емейл-адресу
     *
     * @param <string> $field
     * @param <string> $data
     */
    private function email($field, $data){
        if(!preg_match('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})^', $data)){
            array_push($this->errors, $field.' неправильний.');
        }
    }

    /**
     * Перевіряємо якщо поле необхідне
     *
     * @param <string> $field
     * @param <string> $data
     */
    private function required($field, $data = ''){
        if($data == ''){
            array_push($this->errors, $field.' обов\'язкове.');
        }
    }

    /**
     * Перевіряємо числове поле
     *
     * @param <string> $field
     * @param <string> $data
     */
    private function number($field, $data = ''){
        if(!is_numeric($data)){
            array_push($this->errors, $field.' має бути числове.');
        }
    }

    /**
     * Повертаємо помилки
     *
     * @param <String> $open_tag
     * @param <String> $closed_tag
     *
     * @return <string>
     */

    function getErrors($open_tag = '<p>', $closed_tag = '</p>'){
        $errors = '';
        foreach ($this->errors as $error){
            $errors .= $open_tag.$error.$closed_tag;
        }

        return $errors;
    }
}

?>
