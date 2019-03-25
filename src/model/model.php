<?php
/*
 * WTFPL License (http://www.wtfpl.net/) - https: //github.com/CrBast/PHP-SQL_SimpleLife/blob/master/LICENSE
 *
 * SimpleLifeSQL
 *
 * Documentation : https: //github.com/CrBast/PHP-SQL_SimpleLife/wiki/Model
 */
abstract class Model
{
    public function save()
    {
        foreach (get_class_vars(get_called_class()) as $name => $value) {
            // Here for ignore variable
            if ($name != 'id') {
                $fields[] = $name;
            }
        }
        $count = count($fields);

        if ($this->id == null) {
            $query_name_values = "";
            $query_values = array();
            $query_values_after = "";
            for ($i = 0; $i < $count; $i++) {
                if ($i == 0) {
                    $query_values_after .= '(?';
                    $query_name_values .= '(`' . $fields[$i] . '`';
                } elseif ($i == $count - 1) {
                    $query_values_after .= ',?)';
                    $query_name_values .= ',`' . $fields[$i] . '`)';
                } else {
                    $query_values_after .= ',?';
                    $query_name_values .= ',`' . $fields[$i] . '`';
                }
                $query_values[] = $this->{$fields[$i]};
            }

            slsql::go('INSERT INTO ' . get_called_class() . ' ' . $query_name_values . ' VALUES ' . $query_values_after . '', $query_values);
            $this->id = slsql::go('SELECT id FROM ' . get_called_class() . ' ORDER BY id DESC LIMIT 1', array())['value']->fetch()['id'];
        } else {
            $query_set = "";
            $query_values = array();

            for ($i = 0; $i < $count; $i++) {
                if ($i == 0) {
                    $query_set .= '`' . $fields[$i] . '`=?';
                } else {
                    $query_set .= ',`' . $fields[$i] . '`=?';
                }
                $query_values[] = $this->{$fields[$i]};
            }
            $query_values[] = $this->{'id'};
            slsql::go('UPDATE ' . get_called_class() . ' SET ' . $query_set . ' WHERE id = ?', $query_values);
        }
        unset($query_values, $query_name_values, $query_values_after, $query_set);
    }

    public static function get($condition = null, $arr = array())
    {
        if (!$condition) {
            $result = slsql::go('select * from ' . get_called_class(), $arr)['value']->fetchAll();
        } else {
            $result = slsql::go('select * from ' . get_called_class() . ' where ' . $condition . ";", $arr)['value']->fetchAll();
        }

        if ($result == null) {
            return new EmptyListModels;
        }
        $list = new ListModels;

        foreach (get_class_vars(get_called_class()) as $name => $value) {
            $fields[] = array('name' => $name, 'val' => $value);
        }
        $count = count($fields);

        $temp = get_called_class();
        foreach ($result as $model) {
            $tempModel = new $temp;
            for ($i = 0; $i < $count; $i++) {
                $tempModel->{$fields[$i]['name']} = $model[$fields[$i]['name']];
            }
            $list->add($tempModel);
        }
        return $list;
    }

    public function remove()
    {
        slsql::go('DELETE FROM ' . get_called_class() . ' WHERE id = ?', array($this->id));
    }

    public static function ids()
    {
        $list = null;
        $rows = slsql::go('SELECT id FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row['id'];
        }
        return $list;
    }

    public static function all($field = 'id')
    {
        $list = null;
        $rows = slsql::go('SELECT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }

    public static function allDistinct($field = 'id')
    {
        $list = null;
        $rows = slsql::go('SELECT DISTINCT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }

    public static function count()
    {
        return slsql::go('SELECT count(*) FROM ' . get_called_class())['value']->fetchColumn();
    }

    public static function countWhere($condition = null, $arr = array())
    {
        if (!$condition) {
            return get_called_class()::count();
        } else {
            return slsql::go('SELECT count(*) FROM ' . get_called_class() . ' WHERE ' . $condition, $arr)['value']->fetchColumn();
        }

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

    public function all()
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

    public function count()
    {
        return count($this->arr);
    }
}
class EmptyListModels extends ListModels
{
    public function __construct()
    {
        $this->isEmpty = true;
    }
}
