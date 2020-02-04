<?php 
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

 	function back($url){
		echo "<script type='text/javascript'>";
		echo "window.location.href='$url'";
		echo "</script>"; 
	}
	function notice($say){
		echo "<script type='text/javascript'>";
		echo "alert('$say');";
		echo "</script>";
	}

	$userID = $_POST['UserID'];
	$psw = $_POST['psw'];
	$psw2 = $_POST['psw2'];
	$name = $_POST['name'];
	$email = $_POST['email'];

	$sql = "SELECT * FROM member_system.member WHERE UserID = :UserID";
	$stmt = $pdo->prepare($sql);
	/*
		使用PDO::prepare()方法來準備它。為執行準備一個句子，並回傳。
		沒問題就回傳一個pdo物件，而錯誤就回傳false.
	*/	
	$stmt->bindValue(':UserID',$userID); // 避免SQL injection。以:UserID放入語法內。
	$stmt->execute() or exit("讀取member資料表時，發生錯誤。"); //執行pdo物件；反之出錯。 ;
	$row = $stmt->fetchALL(PDO::FETCH_ASSOC); // 將帳號資料照索引順序一一取出，並以陣列放入$row。
	$nRows = Count($row);  // 資料幾筆，預設：只取出一筆。

	if(empty($userID) || empty($psw) || empty($name) || empty($email) ){ // 避免空值進入資料庫。
		notice("加入會員失敗，不可以有 空值 ！！");
		back("join.html");
	}
	elseif($psw != $psw2){
		notice(" 再次輸入密碼項 要輸入相同密碼哦！！");
		back("join.html");
	}
	elseif($nRows <> 0){
		notice("錯誤，已有相同 帳號 。");
		echo $nRows;
		back("join.html");	
	}
	else{
		$sql = "INSERT INTO member_system.member (UserID, Password, Name, Email) VALUES ('$userID','$psw','$name','$email')";
		$stmt = $pdo->prepare($sql);
		/*
			使用PDO::prepare()方法來準備它。為執行準備一個句子，並回傳。
			沒問題就回傳一個pdo物件，而錯誤就回傳false.
		*/	
		$stmt->bindValue(':UserID',$userID); // 避免SQL injection。以 :UserID 代替並放入語法內。
		$stmt->bindValue(':Psw',$psw); // 避免SQL injection。以 :Psw 代替並放入語法內。
		$stmt->bindValue(':Name',$name); // 避免SQL injection。以 :Name 代替並放入語法內。
		$stmt->bindValue(':Email',$email); // 避免SQL injection。以 :Email 代替並放入語法內。

		$stmt->execute() or exit("讀取member資料表時，發生錯誤。"); //執行pdo物件；反之出錯。 ;
		$pdo = null;	//關閉 pdo 通道。
		notice(" 註冊成功。 ");
		back("login.html");
	}

?>