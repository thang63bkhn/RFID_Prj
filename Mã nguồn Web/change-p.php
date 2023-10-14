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

if (isset($_POST['op']) && isset($_POST['np'])
    && isset($_POST['c_np'])) {

	function validate($data){
       $data = trim($data);
	   $data = stripslashes($data);
	   $data = htmlspecialchars($data);
	   return $data;
	}

	$op = validate($_POST['op']);
	$np = validate($_POST['np']);
	$c_np = validate($_POST['c_np']);
    
    if(empty($op)){
      header("Location: change-password.php?error=Old Password is required");
	  exit();
    }else if(empty($np)){
      header("Location: change-password.php?error=New Password is required");
	  exit();
    }else if($np !== $c_np){
      header("Location: change-password.php?error=The confirmation password  does not match");
	  exit();
    }else {
        $id = $_SESSION['id'];

        $sql = "SELECT password
                FROM users WHERE 
                id='$id' AND password='$op'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) === 1){
        	
        	$sql = "UPDATE users
        	          SET password='$np'
        	          WHERE id='$id'";
        	mysqli_query($conn, $sql);
        	
            $sql = "SELECT password FROM users WHERE id='$id' AND user_name='$uname'";
            $result = mysqli_query($conn, $sql);
            if ($row = mysqli_fetch_assoc($result)) {
                $_SESSION['password'] = $row['password'];
            }
        	header("Location: account.php?success=Your password has been changed successfully");
	        exit();

        }else {
        	header("Location: change-password.php?error=Incorrect password");
	        exit();
        }

    }

    
}else{
	header("Location: change-password.php");
	exit();
}