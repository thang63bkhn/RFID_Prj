<?php
    session_start();
	$Write="<?php $" . "IDresult=''; " . "echo $" . "IDresult;" . " ?>";
	file_put_contents('UIDContainer.php',$Write);

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    	<title>Hệ thống quản lý trang trại</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<script src="js/bootstrap.min.js"></script>
		<script src="jquery.min.js"></script>
		<script>
			$(document).ready(function(){
				 $("#getID").load("UIDContainer.php");
				setInterval(function() {
					$("#getID").load("UIDContainer.php");
				}, 500);
			});
		</script>
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
           width: 80%;
           border-radius: 5px;
           margin: 20px auto;
        }
        
        .success {
           background: #D4EDDA;
           color: #40754C;
           padding: 10px;
           width: 80%;
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
					<h3 align="center">Registration Form</h3>
                 	<?php if (isset($_GET['error'])) { ?>
                 		<p class="error"><?php echo $_GET['error']; ?></p>
                 	<?php } ?>
            
                 	<?php if (isset($_GET['success'])) { ?>
                        <p class="success"><?php echo $_GET['success']; ?></p>
                    <?php } ?>
				</div>

				<form class="form-horizontal" action="insertDB_gm65.php" method="post" >
					<div class="control-group">
						<label class="control-label">ID</label>
						<div class="controls">
							<input name="ID" id="getID" type="text" placeholder="Please Tag your Card" readonly>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Tên</label>
						<div class="controls">
							<input name="NAME" type="text"  placeholder="Điền tiếng Việt không dấu" required>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Phân loại</label>
						<div class="controls">
							<select name="CLASS">
								<option value="Food">Food</option>
								<option value="Vaccine">Vaccine</option>
							</select>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label">Hạn sử dụng</label>
						<div class="controls">
							<input name="HSD" type="date"  placeholder="" required>
						</div>
					</div>
					
					<div class="form-actions">
						<button type="submit" class="btn btn-success">Cập nhập</button>
						<button type="button" onclick="checkVattu()" class="btn btn-success">Kiểm tra</button>
                    </div>
				</form>
                <script>
                function checkVattu() {
                    var inputValue = document.getElementById("getID").value;
                    window.location.href = "vattu.php?Id=" + inputValue;
                }
                </script>
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