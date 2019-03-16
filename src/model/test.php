<?php
include "./user.class.php";

$user = Users::get('id = ?', array('21'))->firstOrDefault('hello');
var_dump($user);
echo $user->id;

var_dump(Users::getAllDistinct('email'));
/*
echo ('<br>');
var_dump(Users::getAllId());*/
