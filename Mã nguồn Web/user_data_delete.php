<?php
    session_start();
    require 'database.php';
    $id = 0;
     
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    if ( !empty($_POST)) {
        // keep track post values
        $id = $_POST['id'];
         
        // delete data
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql_select = "SELECT * FROM table_nodemcu_rfidrc522_mysql WHERE id = ?";
        $q_select = $pdo->prepare($sql_select);
        $q_select->execute(array($id));
        
        if ($data = $q_select->fetch()) {
            $sql_insert = "INSERT INTO table_out (id, gender, species, food, history, birthday, vaccine, food_history, vaccine_history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $q_insert = $pdo->prepare($sql_insert);
            $q_insert->execute(array($data['id'], $data['gender'], $data['species'], $data['food'], $data['history'], $data['birthday'], $data['vaccine'], $data['food_history'], $data['vaccine_history']));
        }
        
        if ($q_insert) {
            $sql_delete = "DELETE FROM table_nodemcu_rfidrc522_mysql WHERE id = ?";
            $q_delete = $pdo->prepare($sql_delete);
            $q_delete->execute(array($id));
            header("Location: user_data.php?success=Xuất chuồng thành công");
            exit();
        }else {
           	header("Location: user_data.php?error=Đã xảy ra lỗi");
            exit();
        }
        Database::disconnect();
    }
	if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<title>Delete</title>
</head>
 
<body>
	<h2 align="center">Thiết kế hệ thống truy suất nguồn gốc gia súc bằng RFID</h2>

    <div class="container">
     
		<div class="span10 offset1">
			<div class="row">
				<h3 align="center">Delete Form</h3>
			</div>

			<form class="form-horizontal" action="user_data_delete.php" method="post">
				<input type="hidden" name="id" value="<?php echo $id;?>"/>
				<p class="alert alert-error">Nhấn đồng ý để xuất chuồng, dữ liệu sẽ được chuyển sang danh sách xuất chuồng?</p>
				<div class="form-actions">
					<button type="submit" class="btn btn-danger">Đồng ý</button>
					<a class="btn" href="user_data.php">Hủy</a>
				</div>
			</form>
		</div>
                 
    </div> <!-- /container -->
  </body>
</html>
<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>