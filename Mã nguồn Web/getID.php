<?php
	$IDresult=$_POST["IDresult"];
	$Write="<?php $" . "IDresult='" . $IDresult . "'; " . "echo $" . "IDresult;" . " ?>";
	file_put_contents('UIDContainer.php',$Write);
?>