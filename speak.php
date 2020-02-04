<?php 
	session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Speak</title>
</head>
<body bgcolor="#FFCC22">
	<?php 
		header("Content-Type:text/html; charset=utf-8"); //PHP 送 Header 告訴瀏覽器這頁是 UTF-8 編碼，避免亂碼。

		if(isset($_POST["words"])){
			$words = $_POST["words"];
			$userID = $_SESSION["memberForOne"][0];;
		}

		if(isset($words)){
		    try{
		    	$dsn = "mysql:host=localhost;port=3306;dbname=member_system;charset=utf8";
			    $user = "root";	// mysql使用者名稱
			    $password = "";	// mysql使用者密碼
			    $options = array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
			    /*
					 ATTR_EERMODE：是個要用PDO時的一定要的基本設定。
					 ERRMODE_EXCEPTION. 它會自動告訴PDO每次查詢失敗時拋出異常。
			    */
			    $pdo = new PDO($dsn, $user,$password,$options);
			    $pdo->query("SET NAMES 'utf8'");  // 以UTF8編碼，SQL語法使用於資料庫。
			}catch(PDOException $e){	
			    echo "資料庫連線失敗！錯誤訊息：",$e->getMessage();	
			    exit;
			}

			$time = date("Y-m-j H:i:s");
			$sql = "INSERT INTO member_system.chatroom(time,content,name) VALUES (:Time,:Words,:UserID)";
			$stmt = $pdo->prepare($sql);
			/*
				使用PDO::prepare()方法來準備它。為執行準備一個句子，並回傳。
				沒問題就回傳一個pdo物件，而錯誤就回傳false.
			*/	
			$stmt->bindValue(':Time',$time); // 避免SQL injection。以 :Time 代替並放入語法內。
			$stmt->bindValue(':Words',$words); // 避免SQL injection。以 :Words 代替並放入語法內。
			$stmt->bindValue(':UserID',$userID); // 避免SQL injection。以 :UserID 代替並放入語法內。

			$stmt->execute() or exit("讀取chatroom資料表時，發生錯誤。"); //執行pdo物件；反之出錯。 ;
			$pdo = null;	//關閉 pdo 通道。
		}
	
		echo "<div align='center'>".$_SESSION["memberForOne"][0]."，Please input your message：</div>";
		echo "<br><br>";
	 ?>
	 <form action="speak.php" method="POST">
	 	<div align="center">
	 		<input type="text" name="words" cols="20">
	 		<input type="submit" value="送出">
	 	</div>
	 </form>
</body>
</html>