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

if (isset($_POST['address']) && isset($_POST['company'])
    && isset($_POST['phone_number'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$address = validate($_POST['address']);
	$company = validate($_POST['company']);
	$phone_number = validate($_POST['phone_number']);
    
    if(empty($address)){
      header("Location: account.php?error=Chưa điền địa chỉ");
	  exit();
    }else if(empty($company)){
      header("Location: account.php?error=Chưa điền tên công ty");
	  exit();
    }else if(empty($phone_number)){
      header("Location: account.php?error=Chưa điền thông tin liên lạc");
	  exit();
    }else {
        $id = $_SESSION['id'];
        $uname = $_SESSION['user_name'];

        $sql = "SELECT password
                FROM users WHERE 
                id='$id' AND user_name='$uname'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) === 1){
            $sql = "UPDATE users 
                    SET address='$address', company='$company'
                    , phone_number='$phone_number' WHERE id='$id' AND user_name='$uname'";
            mysqli_query($conn, $sql);

            $sql = "SELECT name, address, company, phone_number FROM users WHERE id='$id' AND user_name='$uname'";
            $result = mysqli_query($conn, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['company'] = $row['company'];
                $_SESSION['phone_number'] = $row['phone_number'];
            }
        	header("Location: account.php?success=Cập nhập thành công");
	        exit();

        }else {
        	header("Location: account.php?error=Đã xảy ra lỗi");
	        exit();
        }

    }
}else{
	header("Location: account.php?error=Đã xảy ra lỗi");
    exit();
}
 ?>