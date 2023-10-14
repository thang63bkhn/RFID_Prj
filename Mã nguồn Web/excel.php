<?php
    require 'database.php';
    $pdo = Database::connect();
    $sql = 'SELECT * FROM table_out ORDER BY id ASC';
    
    // Tạo chuỗi dữ liệu HTML cho bảng
    $data = "<table>
                <tr>
                    <th>Mã định danh</th>
                    <th>Tên con giống</th>
                    <th>Giới tính</th>
                    <th>Ngày sinh</th>
                    <th>Hồ sơ cho ăn</th>
                    <th>Hồ sơ tiêm</th>
                    <th>Lịch sử dịch tễ</th>
                </tr>";

    foreach ($pdo->query($sql) as $row) {
        $data .= "<tr><td>".$row['id']."</td>";
        $data .= "<td>".$row['species']."</td>";
        $data .= "<td>".$row['gender']."</td>";
        $data .= "<td>".$row['birthday']."</td>";
        $data .= "<td>".$row['food_history']."</td>";
        $data .= "<td>".$row['vaccine_history']."</td>";
        $data .= "<td>".$row['history']."</td></tr>";
    }
    $data .= "</table>";
    // Đặt header để tải xuống file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=Danh sách xuất chuồng.xls');
    echo $data;
    $sql_delete = "DELETE FROM table_out";
    $pdo->exec($sql_delete);
    Database::disconnect();
?>