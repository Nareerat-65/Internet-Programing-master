<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "car_report";

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบว่าการเชื่อมต่อสำเร็จหรือไม่
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully <br>";

// รับค่าที่ส่งมาผ่านแบบฟอร์ม (POST) โดยใช้ค่าเริ่มต้นเป็นค่าว่างหากไม่ได้รับข้อมูล
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
$status_re = 'รออะไหล่'; // สถานะของรายการซ่อม
$section_title = $_POST['section_title'] ?? ''; // หัวข้อส่วนต่าง ๆ

// เพิ่มข้อมูลลงในตาราง car_report
$stmt1 = $conn->prepare("INSERT INTO car_report (clean_status, clean_reason, engine_status, engine_reason, wheel_status, wheel_reason, door_status, door_reason, brake_status, brake_reason, light_status, light_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt1->bind_param("ssssssssssss", $clean_status, $clean_reason, $engine_status, $engine_reason, $wheel_status, $wheel_reason, $door_status, $door_reason, $brake_status, $brake_reason, $light_status, $light_reason);

if ($stmt1->execute()) {
    $id_car_report = $conn->insert_id; // เก็บ ID ของ car_report ที่เพิ่มเข้าไป
    echo "Inserted car_report ID: " . $id_car_report . "<br>";
} else {
    die("Error inserting car_report: " . $stmt1->error);
}

// เพิ่มข้อมูลลงในตาราง equipment_report
$stmt2 = $conn->prepare("INSERT INTO equipment_report (aed_status, aed_reason, ven_status, ven_reason, o2_status, o2_reason, pressure_status, pressure_reason, heart_rate_status, heart_rate_reason, bed_status, bed_reason, stretcher_status, stretcher_reason, firstaid_status, firstaid_reason, splint_status, splint_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt2->bind_param("ssssssssssssssssss", $AED_status, $AED_reason, $ven_status, $ven_reason, $O2_status, $O2_reason, $pressure_status, $pressure_reason, $heart_rate_status, $heart_rate_reason, $bed_status, $bed_reason, $stretcher_status, $stretcher_reason, $firstaid_status, $firstaid_reason, $splint_status, $splint_reason);

if ($stmt2->execute()) {
    $id_equipment_report = $conn->insert_id; // เก็บ ID ของ equipment_report ที่เพิ่มเข้าไป
    echo "Inserted equipment_report ID: " . $id_equipment_report . "<br>";
} else {
    die("Error inserting equipment_report: " . $stmt2->error);
}

// ตรวจสอบว่ามีค่าของ ID ที่ต้องใช้หรือไม่
if (!isset($id_car_report) || !isset($id_equipment_report)) {
    die("Error: Missing ID values for ambulance_report");
}

// เพิ่มข้อมูลรายการซ่อมสำหรับอุปกรณ์หรือรถพยาบาลที่ไม่พร้อมใช้งาน
foreach ($_POST["status"] as $section_title => $status) {
    if ($status == "ไม่พร้อม") {
        $repair_reason = $_POST["reason"][$section_title] ?? '';
        // แยกประเภทของรายการซ่อม
        $type = in_array($section_title, ['เครื่องAED', 'เครื่องช่วยหายใจ', 'ถังออกซิเจน', 'เครื่องวัดความดัน', 'เครื่องวัดชีพจร', 'เตียงพยาบาล', 'เปลสนาม', 'อุปกรณ์ปฐมพยาบาล', 'อุปกรณ์การดาม']) ? 'อุปกรณ์ทางการแพทย์' : 'รถพยาบาล';

        $stmt3 = $conn->prepare("INSERT INTO repair (ambulance_id, repair_staff_id, repair_date, repair_type, repair_item, repair_reason, repair_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt3->bind_param("iisssss", $registration_car, $id_steff, $current_date, $type, $section_title, $repair_reason, $status_re);

        if (!$stmt3->execute()) {
            echo "Error inserting repair record for " . $section_title . ": " . $stmt3->error . "<br>";
        }
        $stmt3->close();
    }
}

// เพิ่มข้อมูลลงในตาราง ambulance_report
$stmt = $conn->prepare("INSERT INTO ambulance_report (date_ambulance_report, ambulance_id, repair_staff_id, id_car_report, id_equipment_report) VALUES (?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("siiii", $current_date, $registration_car, $id_steff, $id_car_report, $id_equipment_report);

if ($stmt->execute()) {
    header("Location: car_report_success.php"); // ส่งไปยังหน้าสำเร็จ
    exit();
} else {
    die("Error inserting ambulance_report: " . $stmt->error);
}

// ปิด statement และการเชื่อมต่อฐานข้อมูล
$stmt1->close();
$stmt2->close();
$stmt->close();
$conn->close();
?>
