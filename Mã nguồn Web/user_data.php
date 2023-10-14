<?php
    session_start();
	if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {
?>

<!DOCTYPE html>
<html lang="en">
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<script src="js/bootstrap.min.js"></script>
		<style>
		html {
			font-family: Arial;
			display: inline-block;
			margin: 0px auto;
			text-align: center;
		}
		
        .error {
           background: #F2DEDE;
           color: #A94442;
           padding: 10px;
           width: 80%;
           border-radius: 5px;
           margin: 20px auto;
        }
        
        .success {
           background: #D4EDDA;
           color: #40754C;
           padding: 10px;
           width: 80%;
           border-radius: 5px;
           margin: 20px auto;
        }

		ul.topnav {
			list-style-type: none;
			margin: auto;
			padding: 0;
			overflow: hidden;
			background-color: #4CAF50;
			width: 70%;
		}

		ul.topnav li {float: left;}

		ul.topnav li a {
			display: block;
			color: white;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
		}

		ul.topnav li a:hover:not(.active) {background-color: #3e8e41;}

		ul.topnav li a.active {background-color: #333;}

		ul.topnav li.right {float: right;}

		@media screen and (max-width: 600px) {
			ul.topnav li.right, 
			ul.topnav li {float: none;}
		}
		
		.table {
			margin: auto;
			width: auto; 
		}
		
		thead {
			color: #FFFFFF;
		}
		
        .btn-group .button {
            background-color: #0c6980; /* Green */
            border: 1px solid #e3e3e3;
            color: white;
            padding: 5px 8px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            float: center;
        }
        
        .btn-group .button:not(:last-child) {
            border-right: none; /* Prevent double borders */
        }
        
        .btn-group .button:hover {
            background-color: #094c5d;
        }
        
        .btn-group .button:active {
            background-color: #0c6980;
            transform: translateY(1px);
        }
        
        .btn-group .button:disabled,
        .button.disabled{
            color:#fff;
            background-color: #a0a0a0; 
            cursor: not-allowed;
            pointer-events:none;
        }
		</style>
		
		<title>Hệ thống quản lý trang trại</title>
	</head>
	
	<body>
		<h2>Thiết kế hệ thống truy suất nguồn gốc gia súc bằng RFID</h2>
		<ul class="topnav">
			<li><a class="active" href="user_data.php">Trang trại</a></li>
			<li><a href="registration.php">Thêm thẻ mới</a></li>
			<li><a href="registration_gm65.php">Vật tư</a></li>
			<li><a href="read_tag.php">Đọc thẻ</a></li>
			<li><a href="xuatchuong.php">Danh sách xuất chuồng</a></li>
            <li><a href="account.php">Tài khoản</a></li>
			<li class="right"><a href="logout.php">Đăng xuất</a></li>
		</ul>
		<br>
		<div class="container">
            <div class="row">
                <h3>Thông tin trang trại</h3>
             	<?php if (isset($_GET['error'])) { ?>
             		<p class="error"><?php echo $_GET['error']; ?></p>
             	<?php } ?>
        
             	<?php if (isset($_GET['success'])) { ?>
                    <p class="success"><?php echo $_GET['success']; ?></p>
                <?php } ?>
            </div>
            <div class="row">
                <table class="table table-striped table-bordered" id= "table_id">
                  <thead>
                    <tr bgcolor="#10a0c5" color="#FFFFFF">
                      <th>ID</th>
                      <th>Giống</th>
					  <th>Giới tính</th>
					  <th>Tháng tuổi</th>
					  <th>Thức ăn</th>
					  <th>Tiêm phòng</th>
                      <th>Lịch sử dịch tễ</th>
					  <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="tbody_table_record">
                  <?php
                   include 'database.php';
                   $num = 0;
                   $pdo = Database::connect();
                   $sql = 'SELECT * FROM table_nodemcu_rfidrc522_mysql ORDER BY id ASC';
                   
                   foreach ($pdo->query($sql) as $row) {
                        $currentDate = date('Y-m-d');
                        $birthday = $row['birthday'];
                        $age = floor((strtotime($currentDate) - strtotime($birthday)) / (60 * 60 * 24 * 30));
                        $num++;
                        echo '<tr>';
                        echo '<td>'. $row['id'] . '</td>';
                        echo '<td>'. $row['species'] . '</td>';
                        echo '<td>'. $row['gender'] . '</td>';
                        echo '<td>'. $age . ' tháng</td>';
						echo '<td>'. $row['food'] . '</td>';
						echo '<td>'. $row['vaccine'] . '</td>';
						echo '<td>'. $row['history'] . '</td>';
						echo '<td><a class="btn btn-success" href="user_data_edit.php?id='.$row['id'].'">Chi tiết</a>';
						echo ' ';
						echo '<a class="btn btn-danger" href="user_data_delete.php?id='.$row['id'].'">Xuất chuồng</a>';
						echo '</td>';
                        echo '</tr>';
                   }
                   Database::disconnect();
                  ?>
                  </tbody>
				</table>
    
    <br>
    
    <div class="btn-group">
      <button class="button" id="btn_prev" onclick="prevPage()">Trang trước</button>
      <button class="button" id="btn_next" onclick="nextPage()">Trang sau</button>
      <div style="display: inline-block; position:relative; border: 0px solid #e3e3e3; float: center; margin-left: 2px;;">
        <p style="position:relative; font-size: 14px;"> Table : <span id="page"></span></p>
      </div>
      <select name="number_of_rows" id="number_of_rows">
        <option value="10">10</option>
        <option value="25">25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <button class="button" id="btn_apply" onclick="apply_Number_of_Rows()">Xem thêm</button>
    </div>

    <br>
    
    <script>
      //------------------------------------------------------------
      var current_page = 1;
      var records_per_page = 10;
      var l = document.getElementById("table_id").rows.length
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function apply_Number_of_Rows() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function prevPage() {
        if (current_page > 1) {
            current_page--;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function nextPage() {
        if (current_page < numPages()) {
            current_page++;
            changePage(current_page);
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function changePage(page) {
        var btn_next = document.getElementById("btn_next");
        var btn_prev = document.getElementById("btn_prev");
        var listing_table = document.getElementById("table_id");
        var page_span = document.getElementById("page");
       
        // Validate page
        if (page < 1) page = 1;
        if (page > numPages()) page = numPages();

        [...listing_table.getElementsByTagName('tr')].forEach((tr)=>{
            tr.style.display='none'; // reset all to not display
        });
        listing_table.rows[0].style.display = ""; // display the title row

        for (var i = (page-1) * records_per_page + 1; i < (page * records_per_page) + 1; i++) {
          if (listing_table.rows[i]) {
            listing_table.rows[i].style.display = ""
          } else {
            continue;
          }
        }
          
        page_span.innerHTML = page + "/" + numPages() + " (Total Number of Rows = " + (l-1) + ") | Number of Rows : ";
        
        if (page == 0 && numPages() == 0) {
          btn_prev.disabled = true;
          btn_next.disabled = true;
          return;
        }

        if (page == 1) {
          btn_prev.disabled = true;
        } else {
          btn_prev.disabled = false;
        }

        if (page == numPages()) {
          btn_next.disabled = true;
        } else {
          btn_next.disabled = false;
        }
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      function numPages() {
        return Math.ceil((l - 1) / records_per_page);
      }
      //------------------------------------------------------------
      
      //------------------------------------------------------------
      window.onload = function() {
        var x = document.getElementById("number_of_rows").value;
        records_per_page = x;
        changePage(current_page);
      };
      //------------------------------------------------------------
    </script>
  </body>
</html>
<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>