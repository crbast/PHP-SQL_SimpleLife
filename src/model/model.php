<?php
include 'slsql.php';
abstract class Model
{
    /**
     * Create or update model
     */
    abstract public function save();

    public static function get($condition, $arr = array())
    {
        $result = slsql::go('select * from ' . get_called_class() . ' where ' . $condition . ";", $arr)['value']->fetchAll();
        if ($result == null) {
            return new EmptyListModels;
        }
        $list = new ListModels;

        foreach ($result as $entry) {
            $list->add(new Users($entry['name'], $entry['psw'], $entry['email'], $entry['id']));
        }
        return $list;
    }

    /**
     * Remove model
     */
    public function remove()
    {
        slsql::go('DELETE FROM ' . get_called_class() . ' WHERE id = ?', array($this->id));
    }

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
