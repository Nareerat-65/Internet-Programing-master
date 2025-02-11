<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // ชื่อเซิร์ฟเวอร์ฐานข้อมูล (localhost คือเครื่องเดียวกัน)
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = "1234"; // รหัสผ่าน MySQL
$dbname = "car_report"; // ชื่อฐานข้อมูลที่ใช้

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // แสดงข้อผิดพลาดหากเชื่อมต่อไม่ได้
}
echo "Connected successfully <br>";

// รับค่าที่ส่งมาผ่านแบบฟอร์ม (POST) และกำหนดค่าเริ่มต้นหากไม่มีข้อมูล
$clean_status = $_POST['status']['ความสะอาด'] ?? '';
$clean_reason = $_POST['reason']['ความสะอาด'] ?? '';
$engine_status = $_POST['status']['เครื่องยนต์'] ?? '';
$engine_reason = $_POST['reason']['เครื่องยนต์'] ?? '';
$wheel_status = $_POST['status']['ล้อรถ'] ?? '';
$wheel_reason = $_POST['reason']['ล้อรถ'] ?? '';
$door_status = $_POST['status']['ประตูรถ'] ?? '';
$door_reason = $_POST['reason']['ประตูรถ'] ?? '';
$brake_status = $_POST['status']['เบรก'] ?? '';
$brake_reason = $_POST['reason']['เบรก'] ?? '';
$light_status = $_POST['status']['ไฟรถ'] ?? '';
$light_reason = $_POST['reason']['ไฟรถ'] ?? '';

// รับค่าที่เกี่ยวข้องกับอุปกรณ์ทางการแพทย์
$AED_status = $_POST['status']['เครื่องAED'] ?? '';
$AED_reason = $_POST['reason']['เครื่องAED'] ?? '';
$ven_status = $_POST['status']['เครื่องช่วยหายใจ'] ?? '';
$ven_reason = $_POST['reason']['เครื่องช่วยหายใจ'] ?? '';
$O2_status = $_POST['status']['ถังออกซิเจน'] ?? '';
$O2_reason = $_POST['reason']['ถังออกซิเจน'] ?? '';
$pressure_status = $_POST['status']['เครื่องวัดความดัน'] ?? '';
$pressure_reason = $_POST['reason']['เครื่องวัดความดัน'] ?? '';
$heart_rate_status = $_POST['status']['เครื่องวัดชีพจร'] ?? '';
$heart_rate_reason = $_POST['reason']['เครื่องวัดชีพจร'] ?? '';
$bed_status = $_POST['status']['เตียงพยาบาล'] ?? '';
$bed_reason = $_POST['reason']['เตียงพยาบาล'] ?? '';
$stretcher_status = $_POST['status']['เปลสนาม'] ?? '';
$stretcher_reason = $_POST['reason']['เปลสนาม'] ?? '';
$firstaid_status = $_POST['status']['อุปกรณ์ปฐมพยาบาล'] ?? '';
$firstaid_reason = $_POST['reason']['อุปกรณ์ปฐมพยาบาล'] ?? '';
$splint_status = $_POST['status']['อุปกรณ์การดาม'] ?? '';
$splint_reason = $_POST['reason']['อุปกรณ์การดาม'] ?? '';

// กำหนดค่าคงที่
$current_date = date('Y-m-d'); // วันที่ปัจจุบัน
$registration_car = $_POST['registration_car'] ?? ''; // หมายเลขทะเบียนรถ
$id_steff = 2; // รหัสพนักงานซ่อม (ค่าเริ่มต้น)
$status_re = 'รอดำเนินการ'; // สถานะของรายการซ่อม

// เพิ่มข้อมูลลงในตาราง car_report
$stmt1 = $conn->prepare("INSERT INTO car_report (date_car_report,ambulance_id,repair_staff_id,clean_status, clean_reason, engine_status, engine_reason, wheel_status, wheel_reason, door_status, door_reason, brake_status, brake_reason, light_status, light_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt1->bind_param("siissssssssssss",$current_date,$registration_car,$id_steff,$clean_status, $clean_reason, $engine_status, $engine_reason, $wheel_status, $wheel_reason, $door_status, $door_reason, $brake_status, $brake_reason, $light_status, $light_reason);
$stmt1->execute();


// เพิ่มข้อมูลลงในตาราง equipment_report
$stmt2 = $conn->prepare("INSERT INTO equipment_report (date_equipment_report,ambulance_id,repair_staff_id,aed_status, aed_reason, ven_status, ven_reason, o2_status, o2_reason, pressure_status, pressure_reason, heart_rate_status, heart_rate_reason, bed_status, bed_reason, stretcher_status, stretcher_reason, firstaid_status, firstaid_reason, splint_status, splint_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("siissssssssssssssssss", $current_date,$registration_car,$id_steff,$AED_status, $AED_reason, $ven_status, $ven_reason, $O2_status, $O2_reason, $pressure_status, $pressure_reason, $heart_rate_status, $heart_rate_reason, $bed_status, $bed_reason, $stretcher_status, $stretcher_reason, $firstaid_status, $firstaid_reason, $splint_status, $splint_reason);
$stmt2->execute();


// เพิ่มข้อมูลลงในตาราง repair หากพบรายการที่ "ไม่พร้อม"
foreach ($_POST["status"] as $section_title => $status) {
    if ($status == "ไม่พร้อม") {
        $repair_reason = $_POST["reason"][$section_title] ?? '';
        $type = in_array($section_title, ['เครื่องAED', 'เครื่องช่วยหายใจ', 'ถังออกซิเจน', 'เครื่องวัดความดัน', 'เครื่องวัดชีพจร', 'เตียงพยาบาล', 'เปลสนาม', 'อุปกรณ์ปฐมพยาบาล', 'อุปกรณ์การดาม']) ? 'อุปกรณ์ทางการแพทย์' : 'รถพยาบาล';
        $stmt3 = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repairing, repair_reason, repair_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->bind_param("iisssss", $registration_car, $id_steff, $current_date, $type, $section_title, $repair_reason, $status_re);
        $stmt3->execute();
        $stmt3->close();
    }
}

header("Location: car_report_success.php");
exit();

// ปิด statement และการเชื่อมต่อฐานข้อมูล
$stmt1->close();
$stmt2->close();
$conn->close();
