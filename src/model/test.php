<?php
include "./user.class.php";

$user = Users::get('id = ? OR id = ? OR id = ?', array(14, 140, 150))->firstOrDefault(null);
var_dump($user);
echo '<br>';
//echo $user->remove();
echo '<br>';

var_dump(Users::getAllDistinct('email'));
/*
echo ('<br>');
var_dump(Users::getAllId());*/
