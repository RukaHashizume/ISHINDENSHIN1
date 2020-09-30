<?php
include 'db_config.php';
try{
	$db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $db->query("SELECT * FROM users"); //usersテーブルを引っ張ってくる
	$users_name = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$db=null;
}  
	//選手の名前・なんかを取ってくる
	foreach($users_name as $index => $un){
    $new_index = $index +1;
    $id[$new_index] = $un['id'];
    $name[$new_index] = $un['name'];
    echo $name[$new_index];
	}
?>