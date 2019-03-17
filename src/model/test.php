<?php
include "./user.class.php";

$user = Users::get('id = ? OR id = ? OR id = ?', array(14, 140, 150))->firstOrDefault(null);
var_dump($user);
echo '<br>';
//echo $user->remove();
echo '<br>';

var_dump(Users::getAllDistinct('email'));

$user = new Users("name", "psw", "mail");
$user->Save();
$user->name = 'CreB';
$user->Save();

echo '<br>';

var_dump($user);
/*
echo ('<br>');
var_dump(Users::getAllId());*/
