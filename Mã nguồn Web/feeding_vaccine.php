<?php
  require 'database.php';
  if (!empty($_POST)) {
	$UID = $_POST['UID'];
	$ID = $_POST['ID'];
    
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT * FROM table_nodemcu_gm65_mysql WHERE id = ?";
    $result  = $pdo->prepare($sql);
    $result ->execute(array($ID));
    if ($result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $name = $row["name"];
        $class = $row["class"];
        
        $sql_select_history = "SELECT vaccine_history, food_history FROM table_nodemcu_rfidrc522_mysql WHERE id=?";
        $q_select_history = $pdo->prepare($sql_select_history);
        $q_select_history->execute(array($UID));
        
        if ($q_select_history->rowCount() > 0) {
            $row_history = $q_select_history->fetch(PDO::FETCH_ASSOC);
            $vaccine_history = $row_history["vaccine_history"];
            $food_history = $row_history["food_history"];
            $food = $row_history["food"];
            $vaccine = $row_history["vaccine"];
            if ($food === "none"){
                $food_history = date("Y-m-d H:i:s") . " " . $name;
            }elseif ($food === $name){
                $food_history = $food_history;
            }else{
                $food_history .= ", " . date("Y-m-d H:i:s") . " " . $name;
            }
            $food = $name;
            if ($vaccine_history === "none"){
                $vaccine_history = date("Y-m-d H:i:s") . " " . $name;
            }else{
                $vaccine_history .= ", " . date("Y-m-d H:i:s") . " " . $name;
            }
            if ($class === "Vaccine") {
                $vaccine +=1;
                $sql_update = "UPDATE table_nodemcu_rfidrc522_mysql SET vaccine_history=?, vaccine=? WHERE id=?";
                $q_update = $pdo->prepare($sql_update);
                $q_update->execute(array($vaccine_history,$vaccine,$UID));
            }elseif ($class === "Food") {
                $sql_update = "UPDATE table_nodemcu_rfidrc522_mysql SET food_history=?, food=? WHERE id=?";
                $q_update = $pdo->prepare($sql_update);
                $q_update->execute(array($food_history,$food,$UID));
            }
        }
        
    }
    Database::disconnect();
  }
?>