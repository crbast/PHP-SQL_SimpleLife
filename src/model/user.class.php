<?php
include 'model.php';
class Users extends Model
{
    public $name, $psw, $mail, $id;
    public function __construct($name, $password, $mail = "test", $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->psw = $password;
        $this->mail = $mail;
    }

    // Create / Update
    public function save()
    {
        if ($this->id == null) {
            slsql::go('INSERT INTO ' . get_class() . ' (`name`, `psw`, `email`) VALUES (?, ?, ?)', array($this->name, $this->psw, $this->mail));
            $this->id = slsql::go('SELECT id FROM ' . get_class() . ' ORDER BY id DESC LIMIT 1', array())['value']->fetch()['id'];
        } else {
            slsql::go('UPDATE ' . get_class() . ' SET `name`=?,`psw`=?,`email`=? WHERE id = ?', array($this->name, $this->psw, $this->mail, $this->id));
        }
    }

    public function remove()
    {
        slsql::go('DELETE FROM ' . get_class() . ' WHERE id = ?', array($this->id));
    }

    public static function get($condition, $arr = array())
    {
        $result = slsql::go('select * from ' . get_called_class() . ' where ' . $condition . ";", $arr)['value']->fetchAll();
        if (!$result) {
            return new EmptyListModels;
        }
        $list = new ListModels();
        foreach ($result as $entry) {
            $list->add(new self($entry['name'], $entry['psw'], $entry['email'], $entry['id']));
        }
        return $list;
    }
}
