<?php
  include 'database.php';
  
  //---------------------------------------- Condition to check that POST value is not empty.
  if (!empty($_GET)) {
    // keep track post values
    $user = $_GET['user'];
    
    $myData = (object)array();
    $pdo = Database::connect();
	$sql = 'SELECT * FROM users WHERE user_name="' . $user . '"';
    foreach ($pdo->query($sql) as $row) {
        $myData->password = $row['password'];
        
        $my_JSON = json_encode($myData);
        // Set the appropriate header for JSON response
        header('Content-Type: application/json');
        echo $my_JSON;
    }
    Database::disconnect();
  }
?>