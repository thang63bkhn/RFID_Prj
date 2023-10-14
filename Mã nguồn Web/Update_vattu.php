<?php
  require 'database.php';
  if (!empty($_POST)) {
	$id = $_POST['id'];
	$name = $_POST['name'];
    $class = $_POST['class'];
    $hsd = $_POST['hsd'];
    
    $found_empty = false;
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM table_nodemcu_gm65_mysql WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
  
    if (!$data = $q->fetch()) {
        $found_empty = true;
    }
	
    if ($found_empty) {
        $sql = "INSERT INTO table_nodemcu_gm65_mysql (id,name,class,hsd) values(?, ?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute(array($id, $name, $class, $hsd));
    }
    Database::disconnect();
  }
?>