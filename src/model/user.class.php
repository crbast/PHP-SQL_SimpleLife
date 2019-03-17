<?php
include 'model.php';
class Users extends Model
{
    public $name, $psw, $email, $id;
    public function __construct($name, $password, $mail = "test", $id = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->psw = $password;
        $this->email = $mail;
    }
}
