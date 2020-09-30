<?php
include 'db_config.php';
//$face_id=$_GET['face_id'];
// if(empty($_GET["select_date"])){
// 	$post_date=date('Y-m-d');
// }
// else if(!empty($_GET["select_date"])){
// 	$post_date=$_GET["select_date"];
// }
try {
	$db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $db->query("SELECT * FROM users");
	$sql_select = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//顔画像と名前を取得
	foreach($sql_select as $index => $s){
		$new_index = $index +1;
		$img[$new_index] = base64_encode($s['img']);
	    $id[$new_index] = $s['id'];
	    $name[$new_index] = $s['name'];
	}
	$db=null;
}catch(PDOException $e){
	echo $e->getMessage();
	exit;
}
?>

<html>
	<?php 	foreach($sql_select as $index => $s){ $new_index = $index +1;?>
	  <?php echo $name[$new_index];?>
      <img src="data:image/png;base64,<?php echo $img[$new_index];?>" />
    <?php } ?>
</html>