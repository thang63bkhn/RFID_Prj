<?php 
session_start(); 
$sname= "localhost";
$unmae= "id20991497_doantotnghiep2k";
$password = "Ahihi123@";
$db_name = "id20991497_doantotnghiep2k";
$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (isset($_POST['uname']) && isset($_POST['password'])
    && isset($_POST['name']) && isset($_POST['re_password'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$uname = validate($_POST['uname']);
	$pass = validate($_POST['password']);

	$re_pass = validate($_POST['re_password']);
	$name = validate($_POST['name']);

	$user_data = 'uname='. $uname. '&name='. $name;


	if (empty($uname)) {
		header("Location: signup.php?error=Tên đăng nhập không được bỏ trống&$user_data");
	    exit();
	}else if(empty($pass)){
        header("Location: signup.php?error=Mật khẩu không được bỏ trống&$user_data");
	    exit();
	}
	else if(empty($re_pass)){
        header("Location: signup.php?error=Mật khẩu không được bỏ trống&$user_data");
	    exit();
	}

	else if(empty($name)){
        header("Location: signup.php?error=Điền đầy đủ tên người dùng&$user_data");
	    exit();
	}

	else if($pass !== $re_pass){
        header("Location: signup.php?error=Mật khẩu xác nhận không khớp&$user_data");
	    exit();
	}

	else{
	    $sql = "SELECT * FROM users WHERE user_name='$uname' ";
		$result = mysqli_query($conn, $sql);

		if (mysqli_num_rows($result) > 0) {
			header("Location: signup.php?error=Tên đăng nhập đã được dùng&$user_data");
	        exit();
		}else {
           $sql2 = "INSERT INTO users(user_name, password, name, address, company, phone_number) VALUES('$uname', '$pass', '$name', 'none', 'none', 'none')";
           $result2 = mysqli_query($conn, $sql2);
           if ($result2) {
           	 header("Location: index.php?success=Tài khoản được tạo thành công");
	         exit();
           }else {
	           	header("Location: signup.php?error=Tạo tài khoản thất bại&$user_data");
		        exit();
           }
		}
	}
	
}else{
	header("Location: signup.php");
	exit();
}