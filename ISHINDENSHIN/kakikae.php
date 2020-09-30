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
        $stmt=$db->query("SELECT mokuhyou_total FROM demomokuhyouti WHERE user_id = {$i}");
        $user_mokuhyouti=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $user_mokuhyouti_json=json_encode($user_mokuhyouti);
        print_r($user_mokuhyouti_json);
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
    		$stmt=$db->query("SELECT total FROM demo_mokuhyoutasseiritu WHERE user_id = {$i}");//user_IDの部分変数に置き換える
    		// $stmt=$db->query("SELECT * FROM demo_mokuhyoutasseiritu");
    		$jikkouti_point=$stmt->fetchAll(PDO::FETCH_ASSOC);		
    		$jikkouti_total_json=json_encode($jikkouti_point);
        print_r($jikkouti_total_json);
        $db=null;
    }catch(PDOException $e){
        	echo $e->getMessage();
        	exit;
    }
    // try{
    // 	//接続
    //     $db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
    //     $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    // 	//目標達成率取得
    //     $stmt=$db->query("SELECT total FROM demo_mokuhyoutasseiritu WHERE id = {$i}");
    //     $user_mokuhyou=$stmt->fetchAll(PDO::FETCH_ASSOC);
    //     $percent_json=json_encode($user_mokuhyou);
    //     $db=null;
    //  }catch(PDOException $e){
    //   	echo $e->getMessage();
    //   	exit;
    //  }
     
    //  try{
    // 	//接続
    //     $db=new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
    //     $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    
    // 	//目標取得
    //     $stmt=$db->query("SELECT percent FROM percent WHERE id = {$i}");
    //     $user_target=$stmt->fetchAll(PDO::FETCH_ASSOC);
    //     $target_json=json_encode($user_target);
    //     $db=null;
    //  }catch(PDOException $e){
    //   	echo $e->getMessage();
    //   	exit;
    //  }
}

	//$target_json[$new_index]=json_encode($mokuhyou_total[$new_index]);//下の数字の部分を変数に
	// $target_json[$new_index]1=json_encode($target1);
	// $target_json[$new_index]2=json_encode($target2);
	// $target_json[$new_index]3=json_encode($target3);
	// $target_json[$new_index]4=json_encode($target4);
	// $target_json[$new_index]5=json_encode($target5);

	// $percent_json=json_encode($jikkouti_total[$new_index]);//下の数字の部分を変数に
	// $percent_json2=json_encode($percent2);
	// $percent_json3=json_encode($percent3);
	// $percent_json4=json_encode($percent4);
	// $percent_json5=json_encode($percent5);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="styles.css">
  <title>選手一覧ページ</title>
</head>
<body>
  <div class="search"> 
    <img src="img/ishindenshin.png" alt="システム名" width="300px" height="150px">
    <!--アプリに飛ぶ-->
    <input type="button" class="start" onclick="location.href='ishindenshin://'" value="練習開始！">
    <ul>
      <li>選手名検索<br>
        <div class="wrapper">
          <div class="search-area">
            <form> <input type="text" id="search-text" placeholder="選手名を入力"> </form>
            <div class="search-result">
              <div class="search-result__hit-num"></div>
              <div id="search-result__list"></div>
            </div>
          </div>
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
          <script>
            $(function () {
            		searchWord = function(){
            		var searchText = $(this).val(), // 検索ボックスに入力された値
            		targetText;
            
            		$('.target-area li').each(function() {
            				targetText = $(this).text();
            
            				// 検索対象となるリストに入力された文字列が存在するかどうかを判断
            				if (targetText.indexOf(searchText) != -1) {
            				$(this).removeClass('hidden');
            				} else {
            				$(this).addClass('hidden');
            				}
            				});
            		};
            
            		// searchWordの実行
            		$('#search-text').on('input', searchWord);
            		});
          </script>
          <script>
            $(function () {
            		searchWord = function(){
            		var searchText = $(this).val(), // 検索ボックスに入力された値
            		targetText;
            
            		$('.target-area li').each(function() {
            				targetText = $(this).text();
            
            				// 検索対象となるリストに入力された文字列が存在するかどうかを判断
            				if (targetText.indexOf(searchText) != -1) {
            				$(this).removeClass('hidden');
            				} else {
            				$(this).addClass('hidden');
            				}
            				});
            		};
            
            		// searchWordの実行
            		$('#search').on('input', searchWord);
            		});
          </script>
      </li>
      <li>日付選択<br>
        <form action="kakikae.php" method="get"> <input type="hidden" name="face_id" value="<?php echo $face_id;?>"> <input type="date" id="day" name="select_date" value="<?php echo $select_date = $post_date; ?>"> <input id="submit_button" name="date_change" type="submit" value="変更"> </form>
      </li><br> </ul> <br>
    <form action="teammokuhyou.php?date=<?php echo $post_date;?>" method="post"> <input id="submit_button" type="submit" name="team" value="チーム評価"></form> <br>
    <form action="mokuhyoutasseiritu.php?date=<?php $date=$post_date; echo $date;?>&user_id=<?php $user_id=$user_id[$new_index]; echo $user_id;?>" method="post"> <input id="submit_button" type="submit" name="team_target" value="目標達成率"></form> <br> </div>
    <div class=main> 
      <!--ここ以下をユーザーの数分まわす-->
      <?php foreach($users as $index => $user) { ?>
      <?php $new_index=$index+1;?>
      <ul class="target-area">
        <li class="list_item" data-percent="success">
          <div class="player">
            <div class="name"><?php echo $name[$new_index];?></div>
            <div hidden id="success_id<?php echo $new_index;?>"></div>
            <div class="pictures">
              <div><img width="200" height="300" src="data:<?php echo $row,$new_index['ext'];?>;base64,<?php echo $img[$new_index];?>"></div>
              <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
              <canvas id="line-chart" width="100px" height="50px"></canvas>
		
      <script>
			//目標値
			var target_arr =[];
			var target_count =[];
			var target = new XMLHttpRequest();
		    target=JSON.parse('<?php echo $user_mokuhyouti_json;?>');
				for(i=0;i<target.length;i++){
					target_arr[i]=(target[i].percent);
					}
			
			for(j=1;j<target.length;j++){
				target_count[j]=j;
			}
			
			//目標達成値
			var percent_arr =[];
			var percent = new XMLHttpRequest();
		    percent=JSON.parse('<?php echo $jikkouti_total_json; ?>');
				for(k=0;k<percent.length;k++){
					percent_arr[k]=(percent[k].pasento);
					}

			  var context = document.getElementById('line-chart').getContext('2d');
			  var line_chart = new Chart(context, {
			    type:'line', // グラフのタイプを指定
			    data:{
			      labels:target_count, // グラフ下部のラベル
			      datasets:[
					  {label:'目標値',  // データのラベル
				  	  data:target_arr, // グラフ化するデータの数値
			          fill:false, // グラフの下部を塗りつぶさない
			          borderColor:'rgb(50,144,229)'}, // 線の色
					  
					  {label:'目標達成率',  // データのラベル
			          data:percent_arr, // グラフ化するデータの数値
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
              <form action="afterrensyu.php?user_id=<?php $user_id = $id[$new_index]; echo $user_id; ?>&select_date=<?php $select_date=$post_date; echo $select_date;?>" method="post"> <input id="submit_button" type="submit" onclick="location:href='afterrensyu.php://'" name="serve01" value="サーブを見る"></form>
              <?php
              ?> 
              </div>
            <hr>
        </li>
      </ul>
      <!--ここまで-->
      <?php } ?> </div>
    </div>
    <script type="text/javascript" src="main.js"></script>
</body>
</html>