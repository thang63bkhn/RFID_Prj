<?php
    require 'database.php';
 
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
     
    if ( !empty($_POST)) {
		$id = $_POST['id'];
		$gender = $_POST['gender'];
        $species = $_POST['species'];
        $birthday = $_POST['birthday'];
        $history = $_POST['history'];
         
        $pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "UPDATE table_nodemcu_rfidrc522_mysql  set history = ?, gender =?, species =?, birthday =? WHERE id = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($history,$gender,$species,$birthday,$id));
		Database::disconnect();
		header("Location: user_data.php");
    }
?>