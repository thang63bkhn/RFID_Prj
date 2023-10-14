<?php
  include 'database.php';
  
  //---------------------------------------- Condition to check that POST value is not empty.
  if (!empty($_GET)) {
    // keep track post values
    $id = $_GET['id'];
    
    $myObj = (object)array();
    $pdo = Database::connect();
	$sql = 'SELECT * FROM table_nodemcu_rfidrc522_mysql WHERE id="' . $id . '"';
    foreach ($pdo->query($sql) as $row) {
        $currentDate = date('Y-m-d');
        $birthday = $row['birthday'];
        $age = floor((strtotime($currentDate) - strtotime($birthday)) / (60 * 60 * 24 * 30));
        
        $myObj->gender = $row['gender'];
        $myObj->species = $row['species'];
        $myObj->vaccine = $row['vaccine'];
        $myObj->food = $row['food'];
        $myObj->history = $row['history'];
        $myObj->age = $age;
        
        $myJSON = json_encode($myObj);
        
        // Set the appropriate header for JSON response
        header('Content-Type: application/json');
        echo $myJSON;
    }
    Database::disconnect();
  }
?>