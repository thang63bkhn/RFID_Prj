<?php
     
    require 'database.php';
 
    if ( !empty($_POST)) {
        // keep track post values
		$id = $_POST['id'];
		$gender = $_POST['gender'];
        $species = $_POST['species'];
        $birthday = $_POST['birthday'];
        $history = $_POST['history'];
        
		// insert data
        $pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $sql = "SELECT * FROM table_nodemcu_rfidrc522_mysql WHERE id = ?";
        $result = $pdo->prepare($sql);
        $result->execute(array($id));
        
        if ($result->rowCount() > 0) {
			header("Location: registration.php?error=ID đã tồn tại");
	        exit();
		}else {
            $sql = "INSERT INTO table_nodemcu_rfidrc522_mysql (id,gender,species,food,history,birthday,vaccine,food_history,vaccine_history) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
    		$q->execute(array($id, $gender, $species, 'none', $history, $birthday, '0', 'none', 'none'));
    		Database::disconnect();
            if ($q) {
                header("Location: registration.php?success=Thêm thành công");
             exit();
            }else {
               	header("Location: registration.php?error=Đã xảy ra lỗi");
                exit();
            }
		}
    }
?>