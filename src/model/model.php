<?php
include 'slsql.php';
abstract class Model
{
    abstract public function save();
    abstract public function remove();
    abstract public static function Get($condition, $arr = array());

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
    private $isEmpty = false;

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
        return !$this->isEmpty ? $this->arr[0] : $default;
    }

    public function first()
    {
        return !$this->isEmpty ? $this->arr[0] : null;
    }
}
class EmptyListModels extends ListModels
{
    public function __construct()
    {
        $this->isEmpty = true;
    }
}
