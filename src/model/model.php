<?php
include 'slsql.php';
abstract class Model
{
    abstract public function save();
    abstract public function remove();
    abstract public static function get($condition, $arr = array());

    public static function getAllId()
    {
        $rows = slsql::go('SELECT id FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row['id'];
        }
        return $list;
    }

    public static function getAll($field = 'id')
    {
        $rows = slsql::go('SELECT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }
    public static function getAllDistinct($field = 'id')
    {
        $rows = slsql::go('SELECT DISTINCT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }
}

class ListModels
{
    private $arr;
    public $isEmpty = false;

    public function add(Model $model)
    {
        $this->arr[] = $model;
    }

    public function get()
    {
        return $this->arr;
    }

    public function firstOrDefault($default = null)
    {
        echo ('Valeur : ' . var_dump($this->isEmpty));
        return !$this->isEmpty ? reset($this->arr) : $default;
    }

    public function first()
    {
        return !$this->isEmpty ? reset($this->arr) : null;
    }

    public function lastOrDefault($default = null)
    {
        return !$this->isEmpty ? end($this->arr) : $default;
    }

    public function last()
    {
        return !$this->isEmpty ? end($this->arr) : null;
    }
}
class EmptyListModels extends ListModels
{
    public function __construct()
    {
        $this->isEmpty = true;
    }
}
