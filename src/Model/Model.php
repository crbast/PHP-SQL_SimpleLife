<?php namespace slsql\Model;

/*
 * WTFPL License (http://www.wtfpl.net/) - https: //github.com/CrBast/PHP-SQL_SimpleLife/blob/master/LICENSE
 *
 * SimpleLifeSQL
 *
 * Documentation : https: //github.com/CrBast/PHP-SQL_SimpleLife/wiki/Model
 */

/**
 * Class Model
 */
abstract class Model
{
    /**
     * @throws Exception
     * @example user->Save
     * Save Model on database
     */
    public function save()
    {
        foreach (get_class_vars(get_called_class()) as $name => $value) {
            // Here for ignore variable
            if ($name != 'id') {
                $fields[] = $name;
            }
        }
        $count = count($fields);
        $query_name_values = "";
        $query_values = array();
        $query_values_after = "";

        if ($this->id == null) {
            if ($count == 1) {
                $query_values_after .= '(?)';
                $query_name_values = '(`' . $fields[0] . '`)';
                $query_values = array($this->{$fields[0]});
            } else {
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
                    $query_values[] .= $this->{$fields[$i]};
                }
            }
            Slsql::go('INSERT INTO ' . get_called_class() . ' ' . $query_name_values . ' VALUES ' . $query_values_after, $query_values);
            $this->id = Slsql::go('SELECT id FROM ' . get_called_class() . ' ORDER BY id DESC LIMIT 1', array())['value']->fetch()['id'];
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
            Slsql::go('UPDATE ' . get_called_class() . ' SET ' . $query_set . ' WHERE id = ?', $query_values);
        }
        unset($query_values, $query_name_values, $query_values_after, $query_set);
    }

    /**
     * @param null $condition
     * @param array $arr
     * @return EmptyListModels|ListModels
     * @throws Exception
     */
    public static function get($condition = null, $arr = array())
    {
        if (!$condition) {
            $result = Slsql::go('select * from ' . get_called_class(), $arr)['value']->fetchAll();
        } else {
            $result = Slsql::go('select * from ' . get_called_class() . ' where ' . $condition . ";", $arr)['value']->fetchAll();
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

    /**
     * @throws Exception
     */
    public function remove()
    {
        Slsql::go('DELETE FROM ' . get_called_class() . ' WHERE id = ?', array($this->id));
    }

    /**
     * @return array
     * @throws Exception
     */
    public static function ids()
    {
        $list = array();
        $rows = Slsql::go('SELECT id FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row['id'];
        }
        return $list;
    }

    /**
     * @param string $field
     * @return array
     * @throws Exception
     */
    public static function all($field = 'id')
    {
        $list = array();
        $rows = Slsql::go('SELECT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }

    /**
     * @param string $field
     * @return array
     * @throws Exception
     */
    public static function allDistinct($field = 'id')
    {
        $list = array();
        $rows = Slsql::go('SELECT DISTINCT ' . $field . ' FROM ' . get_called_class())['value']->fetchAll();
        foreach ($rows as $row) {
            $list[] = $row[$field];
        }
        return $list;
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function count()
    {
        return Slsql::go('SELECT count(*) FROM ' . get_called_class())['value']->fetchColumn();
    }

    /**
     * @param null $condition
     * @param array $arr
     * @return int
     * @throws Exception
     */
    public static function countWhere($condition = null, $arr = array())
    {
        if (!$condition) {
            return get_called_class()::count();
        } else {
            return Slsql::go('SELECT count(*) FROM ' . get_called_class() . ' WHERE ' . $condition, $arr)['value']->fetchColumn();
        }

    }
}

/**
 * Class ListModels
 */
class ListModels
{
    private $arr;
    public $isEmpty = false;

    /**
     * @param Model $model
     * Do not use this function
     */
    public function add(Model $model)
    {
        $this->arr[] = $model;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->arr;
    }

    /**
     * @param null $default
     * @return Model|$default
     */
    public function firstOrDefault($default = null)
    {
        return !$this->isEmpty ? reset($this->arr) : $default;
    }

    /**
     * @return Model|null
     */
    public function first()
    {
        return !$this->isEmpty ? reset($this->arr) : null;
    }

    /**
     * @param null $default
     * @return Model|null
     */
    public function lastOrDefault($default = null)
    {
        return !$this->isEmpty ? end($this->arr) : $default;
    }

    /**
     * @return Model|null
     */
    public function last()
    {
        return !$this->isEmpty ? end($this->arr) : null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->arr);
    }
}

/**
 * Class EmptyListModels
 */
class EmptyListModels extends ListModels
{
    public function __construct()
    {
        $this->isEmpty = true;
    }
}
