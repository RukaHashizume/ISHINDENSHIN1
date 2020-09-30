<?php
include 'db_config.php';
//$face_id=$_GET['face_id'];
// if(empty($_GET["select_date"])){
// 	$post_date=date('Y-m-d');
// }
// else if(!empty($_GET["select_date"])){
// 	$post_date=$_GET["select_date"];
// }

//⬇️顔画像と名前を取得
try{
	$db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $db->query("SELECT * FROM users");
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

	//顔画像と名前を取得
	foreach($users as $index => $ui)
  　{
	  $new_index = $index +1;
	  $img[$new_index] = base64_encode($ui['img']);
	  $id[$new_index] = $ui['id'];//WHEREの時に使う
	  $name[$new_index] = $ui['name'];
	}
	$db=null;
 }catch(PDOException $e){
  	echo $e->getMessage();
  	exit;
 }

try{
	$db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt = $db->query("SELECT COUNT( id ) as users FROM users"); // 選手の数を数える
	$usercount = $stmt->fetch(PDO::FETCH_ASSOC);
	$sum_users = $usercount['users'];
 }catch(PDOException $e){
  	echo $e->getMessage();
  	exit;
 }


for($i=1;$i<=$sum_users;$i++){
    try{
    	//接続
        $db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    	//目標値取得
        $stmt=$db->query("SELECT mokuhyou_total FROM demomokuhyouti WHERE user_id = $i");
        $user_mokuhyouti=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $db=null;
     }catch(PDOException $e){
      	echo $e->getMessage();
      	exit;
     }
 
     try{
    	//接続
        $db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
        $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		//実測値ぽいんと取得
		$stmt=$db->query("SELECT total FROM demo_mokuhyoutasseiritu WHERE user_id = $i");//user_IDの部分変数に置き換える
		$jikkouti_point=$stmt->fetchAll(PDO::FETCH_ASSOC);		
		$jikkouti_total_json=json_encode($jikkouti_point);
        $db=null;
    }catch(PDOException $e){
        	echo $e->getMessage();
        	exit;
    }
}
?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
		<canvas id="line-chart"  width="100px" height="50px"></canvas>
		
		<script>
			//目標値
			var target_arr =[];
			var target_count =[];
			var target = new XMLHttpRequest();
		    target=JSON.parse('<?php echo $user_mokuhyouti;?>');
				for(i=0;i<target.length;i++){
					target_arr[i]=(target[i].percent);
					}
			
			for(j=1;j<target.length;j++){
				target_count[j]=j;
			}
			
			//目標達成値
			var percent_arr =[];
			var percent = new XMLHttpRequest();
		    percent=JSON.parse('<?php echo $jikkouti_total_json;?>');
				for(k=0;k<percent.length;k++){
					percent_arr[k]=(percent[k].pasento);
					}
		
			  var context = document.getElementById('line-chart').getContext('2d');
			  var line_chart = new Chart(context, {
			    type:'line', // グラフのタイプを指定
			    data:{
			      labels:target_count1, // グラフ下部のラベル
			      datasets:[
					  {label:'目標値',  // データのラベル
				  	  data:target_arr1, // グラフ化するデータの数値
			          fill:false, // グラフの下部を塗りつぶさない
			          borderColor:'rgb(50,144,229)'}, // 線の色
					  
					  {label:'目標達成率',  // データのラベル
			          data:percent_arr1, // グラフ化するデータの数値
			          fill:false, // グラフの下部を塗りつぶさない
			          borderColor:'rgb(255, 52, 84)'}, // 線の色
			      ]
			    },
			    options:{
			      scales:{
			        yAxes:[{
			          ticks:{
			            min:0, // グラフの最小値
			         
			          }
			        }]
			      },
			      elements:{
			        line:{
			          tension: 0 // 線グラフのベジェ曲線を無効にする
			        }
			      }
				  }
		});
		</script>