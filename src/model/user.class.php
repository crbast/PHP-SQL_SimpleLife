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

    public function save()
    {
        if ($this->id == null) {
            slsql::go('INSERT INTO ' . get_class() . ' (`name`, `psw`, `email`) VALUES (?, ?, ?)', array($this->name, $this->psw, $this->mail));
            $this->id = slsql::go('SELECT id FROM ' . get_class() . ' ORDER BY id DESC LIMIT 1', array())['value']->fetch()['id'];
        } else {
            slsql::go('UPDATE ' . get_class() . ' SET `name`=?,`psw`=?,`email`=? WHERE id = ?', array($this->name, $this->psw, $this->mail, $this->id));
        }
    }
}
