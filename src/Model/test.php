<?php
include "./user.class.php";
include "../slsql.php";

//$user = Users::get()->count();
//var_dump($user);
echo '<br>';
//echo $user->remove();
echo '<br>';

//var_dump(Users::allDistinct('email'));

$user = new Users();
$user->name = 'CrBast';
$user->Save();
echo '<br>';
var_dump($user);
echo '<br>';

$user = Users::get()->last();
$user->name = "CrBast2";
$user->psw = 123;
$user->email = "uioehbfi@nfo.com";
$user->save();
//$user->remove();
var_dump($user);
$user->save();
echo '<br>';
//var_dump($user->name);
/*
echo ('<br>');
var_dump(Users::getAllId());*/
