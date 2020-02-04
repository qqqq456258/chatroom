<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Chatroom</title>
</head>
<body bgcolor="#FFF">
	<?php 
		header("Refresh:1;url=display.php"); 
		header("Content-Type:text/html; charset=utf-8"); //PHP 送 Header 告訴瀏覽器這頁是 UTF-8 編碼，避免亂碼。
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

		$sql = "SELECT * FROM member_system.chatroom ORDER BY :time;";
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(':time',"time");
		$stmt->execute() or exit("讀取chatroom資料表時，發生錯誤。"); //執行pdo物件；反之出錯。 ;
		$rows = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料取出。
		$inRows = Count($rows); // 總行數。

		if($inRows < 20) $l=$inRows; else $l=20;

		for($i=1 ; $i<=$l ; $i++){		//印出聊天的內容前10筆。
			echo $rows[$i-1]["time"];
			echo "&emsp;";
			echo $rows[$i-1]["name"];
			echo "-->";
			echo $rows[$i-1]["content"];
			echo "<br>";
		}

		if($inRows >=20){				//若有20行聊天紀錄，則加20行以後的紀錄刪除掉避免占空間。
			$last = $rows[19]["time"];
			$sql = "DELETE FROM chatroom WHERE time < '$last'-10 ;";
			$pdo->query($sql);	//以PDO通道直接執行SQL。
		}
			$pdo = null;  // 關閉pdo通道。
	 ?>
</body>
</html>