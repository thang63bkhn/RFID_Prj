<?php 
session_start();
$sname= "localhost";
$unmae= "id20991497_doantotnghiep2k";
$password = "Ahihi123@";
$db_name = "id20991497_doantotnghiep2k";
$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
	echo "Connection failed!";
}
if (isset($_POST['uname']) && isset($_POST['password'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$uname = validate($_POST['uname']);
	$pass = validate($_POST['password']);

	if (empty($uname)) {
		header("Location: index.php?error=Tên đăng nhập không được bỏ trống");
	    exit();
	}else if(empty($pass)){
        header("Location: index.php?error=Mật khẩu không được bỏ trống");
	    exit();
	}else{
		$sql = "SELECT * FROM users WHERE user_name='$uname' AND password='$pass'";

		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) === 1) {
			$row = mysqli_fetch_assoc($result);
            if ($row['user_name'] === $uname && $row['password'] === $pass) {
            	$_SESSION['user_name'] = $row['user_name'];
            	$_SESSION['name'] = $row['name'];
            	$_SESSION['id'] = $row['id'];
            	$_SESSION['address'] = $row['address'];
            	$_SESSION['company'] = $row['company'];
            	$_SESSION['phone_number'] = $row['phone_number'];
            	header("Location: user_data.php");
		        exit();
            }else{
				header("Location: index.php?error=Tên đăng nhập hoặc mật khẩu không chính xác");
		        exit();
			}
		}else{
			header("Location: index.php?error=Tên đăng nhập hoặc mật khẩu không chính xác");
	        exit();
		}
	}
	
}else{
	header("Location: index.php");
	exit();
}