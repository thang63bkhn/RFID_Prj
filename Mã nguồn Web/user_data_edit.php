<?php
    session_start();
    require 'database.php';
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    $pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM table_nodemcu_rfidrc522_mysql where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
	if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
?>

<!DOCTYPE html>
<html lang="en">
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<script src="js/bootstrap.min.js"></script>
		
		<style>
		html {
			font-family: Arial;
			display: inline-block;
			margin: 0px auto;
		}
		
		textarea {
			resize: none;
		}

		ul.topnav {
			list-style-type: none;
			margin: auto;
			padding: 0;
			overflow: hidden;
			background-color: #4CAF50;
			width: 70%;
		}

		ul.topnav li {float: left;}

		ul.topnav li a {
			display: block;
			color: white;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
		}

		ul.topnav li a:hover:not(.active) {background-color: #3e8e41;}

		ul.topnav li a.active {background-color: #333;}

		ul.topnav li.right {float: right;}

		@media screen and (max-width: 600px) {
			ul.topnav li.right, 
			ul.topnav li {float: none;}
		}
		</style>
		
		<title>Hệ thống quản lý trang trại</title>
		
	</head>
	
	<body>

		<h2 align="center">Thiết kế hệ thống truy suất nguồn gốc gia súc bằng RFID</h2>
		<ul class="topnav">
			<li><a class="active" href="user_data.php">Trang trại</a></li>
			<li><a href="registration.php">Thêm thẻ mới</a></li>
			<li><a href="registration_gm65.php">Vật tư</a></li>
			<li><a href="read_tag.php">Đọc thẻ</a></li>
			<li><a href="xuatchuong.php">Danh sách xuất chuồng</a></li>
            <li><a href="account.php">Tài khoản</a></li>
			<li class="right"><a href="logout.php">Đăng xuất</a></li>
		</ul>
		
		<br>
		
		<div class="container">
     
			<div class="center" style="margin: 0 auto; width:495px; border-style: solid; border-color: #f2f2f2;">
				<div class="row">
					<h3 align="center">Registration Form</h3>
					<p id="defaultGender" hidden><?php echo $data['gender'];?></p>
				</div>
		 
				<form class="form-horizontal" action="user_data_edit_tb.php?id=<?php echo $id?>" method="post">
					<div class="control-group">
						<label class="control-label">ID</label>
						<div class="controls">
							<input name="id" type="text"  placeholder="" value="<?php echo $data['id'];?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Giống</label>
						<div class="controls">
							<input name="species" type="text"  placeholder="" value="<?php echo $data['species'];?>" required>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Giới tính</label>
						<div class="controls">
							<select name="gender" id="mySelect">
								<option value="Male">Đực</option>
								<option value="Female">Cái</option>
							</select>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Ngày sinh</label>
						<div class="controls">
							<input name="birthday" type="date" placeholder="" value="<?php echo $data['birthday'];?>" required>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Hồ sơ cho ăn</label>
						<div class="controls">
							<input name="food_history" type="text"  placeholder="" value="<?php echo $data['food_history'];?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Hồ sơ tiêm</label>
						<div class="controls">
							<input name="vaccine_history" type="text"  placeholder="" value="<?php echo $data['vaccine_history'];?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Lịch sử dịch tễ</label>
						<div class="controls">
							<input name="history" type="text"  placeholder="" value="<?php echo $data['history'];?>" required>
						</div>
					</div>
					
					<div class="form-actions">
					    <a class="btn" href="user_data.php">Quay lại</a>
						<button type="submit" class="btn btn-success">Cập nhập</button>
					</div>
				</form>
			</div>               
		</div> <!-- /container -->	
		
		<script>
			var g = document.getElementById("defaultGender").innerHTML;
			if(g=="Male") {
				document.getElementById("mySelect").selectedIndex = "0";
			} else {
				document.getElementById("mySelect").selectedIndex = "1";
			}
		</script>
	</body>
</html>
<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>