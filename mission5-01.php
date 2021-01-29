<?php
//まずデータベースに接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//あらかじめ作成しておいたmission_5_01というtableを使う
$sql = "CREATE TABLE IF NOT EXISTS mission_5_01"
." ("
. "id INT AUTO_INCREMENT PRIMARY KEY,"
. "name char(32),"
. "comment TEXT,"
. "date TEXT,"
. "pwd TEXT"
.");";
$stmt = $pdo->query($sql);

//postから受け取ってtableに値を入れるのは、commentに値があるとき
if(!empty($_POST["comment"])){
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d/ H:i:s");
    $pwd = $_POST["pwd"];
    
    //ここから、編集する場合の処理
    if(!empty($_POST["edit_post"])){
        $re_id = $_POST["edit_post"];
        $re_date = $date."編集済み";
        $sql = 'UPDATE mission_5_01 SET name=:name,comment=:comment,date=:re_date,pwd=:pwd 
        WHERE id=:re_id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':re_date', $re_date, PDO::PARAM_STR);
        $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);
        $stmt->bindParam(':re_id', $re_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    //編集でないときは、普通に入力
    else{
        $sql = $pdo -> prepare("INSERT INTO mission_5_01 (name, comment, date, pwd) 
        VALUES (:name, :comment, :date, :pwd)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
        $sql -> bindParam(':pwd', $pwd, PDO::PARAM_STR);
        $sql -> execute();
    }
    
}


//削除機能の実装
elseif(!empty($_POST["del_num"])){
    $del_pwd = $_POST["del_pwd"];
    $del_id = $_POST["del_num"]; //idにpostで受け取った消去対象番号を入れる
    //andで繋げれば複数の条件で絞り込めるはず
    $sql = 'delete from mission_5_01 where id=:del_id and pwd=:del_pwd';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
    $stmt->bindParam(':del_pwd', $del_pwd, PDO::PARAM_INT);
    $stmt->execute();
}

//編集機能の実装 編集対象番号が入力されたとき、そのデータを表示する
elseif(isset($_POST["edit_num"])){
    $edit_pwd = $_POST["edit_pwd"];
    $edit_id = $_POST["edit_num"];
    $sql = 'SELECT * FROM mission_5_01 WHERE id=:edit_id and pwd=:edit_pwd';
    $stmt = $pdo->prepare($sql); 
    $stmt->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);
    $stmt->bindParam(':edit_pwd', $edit_pwd, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll(); 
    
    //ここから編集機能へ渡す これで名前とコメントが再表示されるはず
    foreach($results as $row){
        $edit_name = $row['name'];
        $edit_comment = $row['comment'];
        $edit_key = $edit_id;
    }
}


//とりあえず表示してみる
$sql = 'SELECT * FROM mission_5_01';
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
	//$rowの中にはテーブルのカラム名が入る
	echo $row['id']."\t";
	echo $row['name']."\t";
	echo $row['comment']."\t";
	echo $row['date'].'<br>';
}
echo "<hr>";


?>

<DOCTYPE html>   <!--おそらくhtmlの部分は同じでいいはず  -->
<html>
    <head>
        <meta charset = "utf-8">
        <title>mission_5-01</title>
    </head>
    
    <body>
        <form action = "" method = "post">
            
            <!-- 編集番号を記録する処理 -->
            <input type = "hidden" name = "edit_post" value = <?php if(isset($edit_key))
            {echo $edit_key;}?>>

            <p>お名前</p>
            <input type = "text" name = "name" value = <?php if(isset($edit_name))
            {echo $edit_name;} else{echo "名無し";}?>>
            
            <p>コメント</p>
            <input type = "text" name = "comment" placeholder = "コメントを入力してください" 
            value = <?php if(isset($edit_comment)){echo $edit_comment;}?>>
            
            <p>パスワード</p>
            <input type = "text" name = "pwd">
            
            <input type = "submit" name = "submit" value = "送信">
        </form>
        
        
        <form action = "" method = "post">
            <p>削除対象番号</p>
            <input type = "number" name = "del_num">
            <p>パスワード</p>
            <input type = "text" name = "del_pwd">
            <input type = "submit" name = "submit_del" value = "削除">
        </form>
        
        <form action = "" method ="post">
            <p>編集対象番号</p>
            <input type = "number" name = "edit_num">
            <p>パスワード</p>
            <input type = "text" name = "edit_pwd">
            <input type = "submit" name = "submit_edit" value = "編集">
            
        </form>
        
    </body>
</html>