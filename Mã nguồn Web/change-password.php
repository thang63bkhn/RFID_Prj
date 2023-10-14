<?php 
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
    	<title>Change Password</title>
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
		<h2 align="center">Thiết kế hệ thống quản lý trang trại</h2>
		<ul class="topnav">
			<li><a href="user_data.php">Trang trại</a></li>
			<li><a href="registration.php">Thêm thẻ mới</a></li>
			<li><a href="registration_gm65.php">Vật tư</a></li>
			<li><a href="read_tag.php">Đọc thẻ</a></li>
			<li><a href="xuatchuong.php">Danh sách xuất chuồng</a></li>
            <li><a class="active" href="account.php">Tài khoản</a></li>
			<li class="right"><a href="logout.php">Đăng xuất</a></li>
		</ul>
		
		<br>
		<div class="container">
			<div class="center" style="margin: 0 auto; width:495px; border-style: solid; border-color: #f2f2f2;">
				<div class="row">
					<h3 align="center">Đổi mật khẩu</h3>
				</div>
				
				<form class="form-horizontal" action="change-p.php" method="post">
                 	<?php if (isset($_GET['error'])) { ?>
                 		<p class="error"><?php echo $_GET['error']; ?></p>
                 	<?php } ?>
            
                 	<?php if (isset($_GET['success'])) { ?>
                        <p class="success"><?php echo $_GET['success']; ?></p>
                    <?php } ?>
                    
                    <div class="control-group">
                        <label class="control-label">Mật khẩu hiện tại</label>
						<div class="controls">
							<input name="op" type="password"  placeholder="Điều mật khẩu hiện tại">
						</div>
					</div>

                    <div class="control-group">
                        <label class="control-label">Mật khẩu mới</label>
						<div class="controls">
							<input name="np" type="password"  placeholder="Điền mật khẩu mới">
						</div>
					</div>
					
                    <div class="control-group">
                        <label class="control-label">Xác nhận mật khẩu mới</label>
						<div class="controls">
							<input name="c_np" type="password"  placeholder="Điền mật khẩu mới">
						</div>
					</div>
					
					<div class="form-actions">
					    <a class="btn" href="account.php">Quay lại</a>
						<button type="submit" class="btn btn-success">Cập nhập</button>
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