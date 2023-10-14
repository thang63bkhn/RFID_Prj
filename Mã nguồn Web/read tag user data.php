<?php
    require 'database.php';
    $id = null;
    if ( !empty($_GET['id'])) {
        $id = $_REQUEST['id'];
    }
    $currentDate = date('Y-m-d');
    $pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM table_nodemcu_rfidrc522_mysql where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
	
	$msg = null;
	if (null==$data['species']) {
		$msg = "Thẻ chưa được đăng ký";
		$data['id']=$id;
		$data['gender']="--------";
		$data['species']="--------";
		$data['food']="--------";
		$data['history']="--------";
		$data['food_history']="--------";
		$data['vaccine_history']="--------";
		$age="--------";
	} else {
		$msg = null;
		$age = floor((strtotime($currentDate) - strtotime($data['birthday'])) / (60 * 60 * 24 * 30));
	}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<style>
		td.lf {
			padding-left: 15px;
			padding-top: 12px;
			padding-bottom: 12px;
		}
	</style>
</head>
 
	<body>	
		<div>
			<form>
				<table  width="600" border="1" bordercolor="#10a0c5" align="center"  cellpadding="0" cellspacing="1"  bgcolor="#000" style="padding: 2px">
					<tr>
						<td  height="40" align="center"  bgcolor="#10a0c5"><font  color="#FFFFFF">
						<b>Thông tin vật nuôi</b></font></td>
					</tr>
					<tr>
						<td bgcolor="#f9f9f9">
							<table width="600"  border="0" align="center" cellpadding="5"  cellspacing="0">
								<tr>
									<td width="113" align="left" class="lf">ID</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['id'];?></td>
								</tr>
								<tr bgcolor="#f2f2f2">
									<td align="left" class="lf">Giống</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['species'];?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Giới tính</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['gender'];?></td>
								</tr>
								<tr bgcolor="#f2f2f2">
									<td align="left" class="lf">Ngày sinh</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['birthday'];?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Tháng tuổi</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $age;?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Thức ăn hiện tại</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['food'];?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Hồ sơ cho ăn</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['food_history'];?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Hồ sơ tiêm</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['vaccine_history'];?></td>
								</tr>
								<tr>
									<td align="left" class="lf">Lịch sử dịch tễ</td>
									<td style="font-weight:bold">:</td>
									<td align="left"><?php echo $data['history'];?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<h3 align="center" style="color:red;"><?php echo $msg;?></h3>
	</body>
</html>