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
  	$db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD);
  	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  	$stmt = $db->query("SELECT * FROM users");
  	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  	//顔画像と名前を取得
  	foreach($users as $index => $ui){
  		$img[$index+1] = base64_encode($ui['img']);
  	  $id[$index+1] = $ui['id']; //WHEREの時に使う
  	  $name[$index+1] = $ui['name'];
  	}
    // 全ユーザ数
    $sum_users = count($id);
    // print_r($img);
    // print_r($id);
    // print_r($name);
    // print($sum_users);
    // print("以上");
    
    //目標値取得
    $stmt = $db->query("SELECT mokuhyou_total FROM demomokuhyouti");
    $user_mokuhyouti = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $user_mokuhyouti_json = json_encode($user_mokuhyouti);
    
    //実測値ぽいんと取得
    $stmt = $db->query("SELECT total FROM demo_mokuhyoutasseiritu");
    $jikkouti_point = $stmt->fetchAll(PDO::FETCH_ASSOC);		
    $jikkouti_total_json = json_encode($jikkouti_point);
    
    // DB切断
    $db=null;
    
   }catch(PDOException $e){
    	echo $e->getMessage();
    	exit;
   }
   
   // print_r($user_mokuhyouti_json);
   // print_r($jikkouti_total_json);
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
        <form action="web.php" method="get"> <input type="hidden" name="face_id" value="<?php echo $face_id;?>"> <input type="date" 　 id="day" name="select_date" value="<?php echo $select_date = $post_date; ?>"> <input id="submit_button" name="date_change" type="submit" value="変更"> </form>
      </li><br> </ul> <br>
    <form action="teammokuhyou.php?date=<?php echo $post_date;?>" method="post"> <input id="submit_button" type="submit" name="team" value="チーム評価"></form> <br>
    <form action="mokuhyoutasseiritu.php?date=<?php $date=$post_date; echo $date;?>&user_id=<?php $user_id=$user_id[$new_index]; echo $user_id;?>" method="post"> <input id="submit_button" type="submit" name="team_target" value="目標達成率"></form> <br> </div>
    
    
    
    
    <div class=main> 
      <!--ここ以下をユーザーの数分まわす-->
      <?php 
        foreach($users as $index => $user) { 
          $new_index=$index+1;
      ?>
      <ul class="target-area">
        <li class="list_item" data-percent="success">
          <div class="player">
            <div class="name"><?php echo $name[$new_index];?></div>
            <div hidden id="success_id<?php echo $new_index;?>"></div>
            <div id="parent-div">
              <div class="pictures">
                <div><img width="200" height="300" src="data:<?php echo $row,$new_index['ext'];?>;base64,<?php echo $img[$new_index];?>"></div>
                
                <!--グラフ描画-->
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
                <canvas id="line-chart" width="100px" height="50px"></canvas>
  		
                <script>
                // 追加する要素を作成します
                var newElement = document.createElement("p"); // p要素作成
                var newContent = document.createTextNode("子要素２"); // テキストノードを作成
                newElement.appendChild(newContent); // p要素にテキストノードを追加
                newElement.setAttribute("id","child-p2"); // p要素にidを設定
                
                // 子要素１の後に追加します
                // 親要素（div）への参照を取得
                var parentDiv = document.getElementById("parent-div");
                
                // 子要素１への参照を取得
                var childP1 = document.getElementById("child-p1");
                
                // 追加
                parentDiv.insertBefore(newElement, childP1.nextSibling);
                
                
          			//目標値
                var target_arr = <?php echo $user_mokuhyouti_json;?>;
              	var target_count = [];
              	for(j=1;j<target_arr.length;j++){
              		target_count[j]=j;
              	}
          			
          			//目標達成値
          			var percent_arr = <?php echo $jikkouti_total_json; ?>;
                
                var a = [2,3,4,5,4,3];
                var b = [9,7,5,8,5,4];
                
                
          
        			  var context = document.getElementById('line-chart').getContext('2d');
        			  // var line_chart = new Chart(
                //   context, {
                //     type:'line', // グラフのタイプを指定
                //     data:{
                //       labels:target_count, // グラフ下部のラベル
                //       datasets:[
                //         {
                //           label:'目標値',  // データのラベル
                //           data:target_arr, // グラフ化するデータの数値
                //           fill:false, // グラフの下部を塗りつぶさない
                //           borderColor:'rgb(50,144,229)' // 線の色
                //         },
                //         {
                //           label:'目標達成率',  // データのラベル
                //           data:percent_arr, // グラフ化するデータの数値
                //           fill:false, // グラフの下部を塗りつぶさない
                //           borderColor:'rgb(255, 52, 84)' // 線の色
                //         }
            		// 	    ]
                //      },
                     
                //      options:{
                //        scales:{
                //          yAxes:[{
                //            ticks:{
                //              min:0, // グラフの最小値
                //            }
                //          }]
                //        },
                //        elements:{
                //          line:{
                //            tension: 0 // 線グラフのベジェ曲線を無効にする
                //          }
                //        }
                //      }
                     
                //    }
                //  );
                var line_chart = new Chart(
                  context, {
                    type:'line', // グラフのタイプを指定
                    data:{
                      labels:target_count, // グラフ下部のラベル
                      datasets:[
                        {
                          label:'目標値',  // データのラベル
                          data:b, // グラフ化するデータの数値
                          fill:false, // グラフの下部を塗りつぶさない
                          borderColor:'rgb(50,144,229)' // 線の色
                        },
                        {
                          label:'目標達成率',  // データのラベル
                          data:percent_arr, // グラフ化するデータの数値
                          fill:false, // グラフの下部を塗りつぶさない
                          borderColor:'rgb(255, 52, 84)' // 線の色
                        }
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
                     
                   }
                 );
          			</script>
                       
                <form action="afterrensyu.php?user_id=<?php $user_id = $id[$new_index]; echo $user_id; ?>&select_date=<?php $select_date=$post_date; echo $select_date;?>" method="post"> <input id="submit_button" type="submit" onclick="location:href='afterrensyu.php://'" name="serve01" value="サーブを見る"></form>
              </div>
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