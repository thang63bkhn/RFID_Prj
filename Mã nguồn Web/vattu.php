<?php
    session_start();
    require 'database.php';
    $id = null;
    if (isset($_GET['Id'])) {
        $id = $_GET['Id'];
    }
     
    $pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM table_nodemcu_gm65_mysql where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
	if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
?>
    <!DOCTYPE html>
    <html>
    <head>
    	<title>Hệ thống quản lý trang trại</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<style>
		html {
			font-family: Arial;
			display: inline-block;
			margin: 0px auto;
		}
		
        .error {
           background: #F2DEDE;
           color: #A94442;
           padding: 10px;
           width: 95%;
           border-radius: 5px;
           margin: 20px auto;
        }
        
        .success {
           background: #D4EDDA;
           color: #40754C;
           padding: 10px;
           width: 95%;
           border-radius: 5px;
           margin: 20px auto;
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
    </head>
    <body>
		<h2 align="center">Thiết kế hệ thống truy suất nguồn gốc gia súc bằng RFID</h2>
		<ul class="topnav">
			<li><a href="user_data.php">Trang trại</a></li>
			<li><a href="registration.php">Thêm thẻ mới</a></li>
			<li><a class="active" href="registration_gm65.php">Vật tư</a></li>
			<li><a href="read_tag.php">Đọc thẻ</a></li>
			<li><a href="xuatchuong.php">Danh sách xuất chuồng</a></li>
            <li><a href="account.php">Tài khoản</a></li>
			<li class="right"><a href="logout.php">Đăng xuất</a></li>
		</ul>

		<div class="container">
			<br>
			<div class="center" style="margin: 0 auto; width:495px; border-style: solid; border-color: #f2f2f2;">
				<div class="row">
					<h3 align="center">Kết quả kiểm tra</h3>
				</div>

				<form class="form-horizontal" action="registration_gm65.php" method="post" >
					<div class="control-group">
						<label class="control-label">ID</label>
						<div class="controls">
							<input name="ID" type="text"  placeholder="" value="<?php echo $id;?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Tên</label>
						<div class="controls">
							<input name="NAME" type="text"  placeholder="" value="<?php echo $data['name'];?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Phân loại</label>
						<div class="controls">
							<input name="CLASS" type="text"  placeholder="" value="<?php echo $data['class'];?>" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Hạn sử dụng</label>
						<div class="controls">
							<input name="HSD" type="text"  placeholder="" value="<?php echo $data['hsd'];?>" readonly>
						</div>
					</div>
					
					<div class="form-actions">
						<button type="submit" class="btn btn-success">Quay lại</button>
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