# RFID_Prj
Đề tài tốt nghiệp: Thiết kế hệ thống truy xuất nguồn gốc gia súc bằng RFID
  - Môi trường lập trình: VScode PlatformIO, Nextion Editer.
  - Phần cứng: ESP32, DS3231, Simcom A7600, RC522 (Scan RFID), GM65 (Scan QRcode), MLX90614 (Đo thân nhiệt), màn hình Nextion.
  - Chức năng: Hệ thống bao gồm thiết bị cầm tay quản lý tại hiện trường để thu thập các thông số vật nuôi (mã định danh, chủng loại, giới tính, ngày sinh, lịch sử dịch tễ, lịch sử tiêm, loại thức ăn) và vật tư (Barcode, tên, loại vật tư vaccine hoặc thức ăn, hạn sử dụng), Server quản lý và hiển thị thông tin của vật nuôi, vật tư và danh sách nhân viên trên Website. Dưới đây là mô tả về một số chức năng nổi bật:
      - Đăng nhập: Hệ thống có thể đăng nhập từ cả máy tính và trên thiết bị cầm tay qua màn hình cảm ứng.
      - Đọc thông tin: Truy xuất tới dữ liệu trong Database để hiển thị dữ liệu về vật nuôi và vật tư (Chỉ thực hiện khi có kết nối WiFi).
      - Ghi thông tin mới: Cập nhập thông tin vào Database, nếu ở chế độ offline thì thông tin sẽ được lưu trong thẻ SD tới khi có kết nối lại.
      - Ghi lại lịch sử hoạt động của thiết bị cầm tay vào thẻ SD.
      - Tùy chỉnh một số thông số hệ thống: Thời gian ngủ MCU khi vào chế độ tiết kiệm năng lượng, độ sáng màn hình cảm ứng, lưu các thông số tùy chỉnh làm mặc định.
      - Chế độ tiết kiệm năng lượng: Khi bật thì MCU sẽ đọc dữ liệu rồi vào chế độ DeepSleep trong một khoảng thời gian, màn hình sẽ tự động tắt sau 15s không thao tác hoặc nhận dữ liệu UART từ MCU.
      - Kiểm tra lỗi: MCU đọc tín hiệu phản hồi từ các ngoại vi để người dùng xác nhận thiết bị cầm tay hoạt động ổn định hay không.
  - Link video demo: https://drive.google.com/file/d/1ZiixJsnOtGcgZS0ufAWgJc0BYCTxRc3-/view?usp=sharing
  - Link Website quản lý: https://doantotnghiep2k.000webhostapp.com
      - Tên đăng nhập: Thang2k
      - Mật khẩu: 1234
