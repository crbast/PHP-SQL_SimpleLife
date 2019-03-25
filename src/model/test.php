<?php
include "./user.class.php";
include "../slsql.php";

$user = Users::get()->count();
var_dump($user);
echo '<br>';
//echo $user->remove();
echo '<br>';

var_dump(Users::allDistinct('email'));

/*$user = new Users("name", "psw", "mail");
$user->Save();
$user->name = 'CreB';
$user->Save();*/

echo '<br>';
/*
echo ('<br>');
var_dump(Users::getAllId());*/
