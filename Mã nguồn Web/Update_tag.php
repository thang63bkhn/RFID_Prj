<?php
     
    require 'database.php';
 
    if ( !empty($_POST)) {
        // keep track post values
		$id = $_POST['id'];
		$gender = $_POST['gender'];
        $species = $_POST['species'];
        $birthday = $_POST['birthday'];
        $history = $_POST['history'];
        $food = "none";
        $vaccine = 0;
        $food_history = "none";
        $vaccine_history = "none";

		// insert data
        $pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Chuẩn bị câu truy vấn INSERT
        $sql_insert = "INSERT INTO table_nodemcu_rfidrc522_mysql (id, gender, species, birthday, history, food, vaccine, food_history, vaccine_history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $q_insert = $pdo->prepare($sql_insert);
        $q_insert->execute(array($id, $gender, $species, $birthday, $history, $food, $vaccine, $food_history, $vaccine_history));
		Database::disconnect();
    }
?>