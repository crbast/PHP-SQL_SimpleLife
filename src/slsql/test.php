<?php
include './slsql.php';

$db = new slsql(array("dbName" => "m151"));
echo var_dump($db->connect());
$result = $db->send('SELECT * FROM chat', array());
$result2 = slsql::go('SELECT * FROM chat', array());

echo var_dump($result);

echo '<br><br><br><br>';

echo var_dump($result2);

/*$trans = new SLTransaction();
$trans->add('SELECT * from users');
$trans->add('INSERT INTO `users` (`name`, `description`) VALUES (?, ?)', array(10, 10));
$trans->add('SELECT * from users');
$temp = $trans->go();
var_dump($temp['value']->fetchAll());*/
