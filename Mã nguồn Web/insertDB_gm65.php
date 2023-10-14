<?php
    require 'database.php';
 
    if ( !empty($_POST)) {
        // keep track post values
		$ID = $_POST['ID'];
		$NAME = $_POST['NAME'];
        $CLASS = $_POST['CLASS'];
        $HSD = $_POST['HSD'];
        
		// insert data
        $pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $sql = "SELECT * FROM table_nodemcu_gm65_mysql WHERE id = ?";
        $result = $pdo->prepare($sql);
        $result->execute(array($ID));
        
        if ($result->rowCount() > 0) {
			header("Location: registration_gm65.php?error=ID đã tồn tại");
	        exit();
		}else {
            $sql = "INSERT INTO table_nodemcu_gm65_mysql (id,name,class,hsd) values(?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
    		$q->execute(array($ID, $NAME, $CLASS, $HSD));
    		Database::disconnect();
            if ($q) {
                header("Location: registration_gm65.php?success=Thêm thành công");
             exit();
            }else {
               	header("Location: registration_gm65.php?error=Đã xảy ra lỗi");
                exit();
            }
		}
    }
?>