<?php
include "./user.class.php";

$user = Users::get('id = ? OR id = ? OR id = ?', array(130, 140, 150))->firstOrDefault('Hello');
var_dump($user);
echo '<br>';
echo $user->id;
echo '<br>';

var_dump(Users::getAllDistinct('email'));
/*
echo ('<br>');
var_dump(Users::getAllId());*/
