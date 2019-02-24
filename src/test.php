<?php
include './slsql.php';
$db = new slsql(array("dbName" => "m151"));
echo var_dump($db->connect());
$result = $db->send('SELECT * FROM chat', array());
$result2 = slsql::go('SELECT * FROM chat', array());

echo var_dump($result);

echo '<br><br><br><br>';

echo var_dump($result2);