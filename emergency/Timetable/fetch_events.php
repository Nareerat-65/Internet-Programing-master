<?php
date_default_timezone_set('Asia/Bangkok'); // กำหนดเขตเวลาเป็น Asia/Bangkok  

// เชื่อมต่อฐานข้อมูล
$con = new mysqli('localhost', 'root', '1234', 'car_report');
if ($con->connect_error) {
    die(json_encode(['error' => 'Connection Failed: ' . $con->connect_error]));
}

// ดึงข้อมูลจากตาราง ambulance_booking, event_booking และ time_table โดยใช้ UNION
// ใช้ UNION รวมผลลัพธ์จากตารางที่มีโครงสร้างคล้ายกันหรือเหมือนกัน
$sql = "
    SELECT 
        ambulance_booking_location AS title,
        'ambulance' as type,
        CONCAT(ambulance_booking_date, 'T', ambulance_booking_start_time) AS start, 
        CONCAT(ambulance_booking_date, 'T', ambulance_booking_fisnish_time) AS end
    FROM ambulance_booking

    UNION

    SELECT 
        event_booking_location AS title, 
        'event' as type,
        CONCAT(event_booking_date, 'T', event_booking_start_time) AS start, 
        CONCAT(event_booking_date, 'T', event_booking_finish_time) AS end
    FROM event_booking

    UNION

    SELECT title, 
           type,
           start_datetime AS start, 
           end_datetime AS end
    FROM time_table
";

// รันคำสั่ง SQL และตรวจสอบข้อผิดพลาด
$result = $con->query($sql);
if (!$result) {
    die(json_encode(['error' => 'Query Failed: ' . $con->error]));
}

$events = [];
if ($result->num_rows > 0) { // ตรวจสอบว่ามีข้อมูลในผลลัพธ์หรือไม่
    while ($row = $result->fetch_assoc()) { // วนลูปดึงข้อมูลแต่ละแถว และจัดเก็บในรูปแบบ array  
        $events[] = [
            'title' => $row['title'],
            'start' => $row['start'],
            'end' => $row['end'],
            'type' => $row['type'],
            'allDay' => false
        ];
    }
}

$con->close();

// ตั้งค่าหัวข้อของ HTTP Response เป็น JSON  และแปลงข้อมูลในตัวแปร $events ให้เป็น JSON และแสดงผลออกทางหน้าจอ
header('Content-Type: application/json');
// ใช้ JSON_UNESCAPED_UNICODE เพื่อแสดงภาษาไทยใน JSON อย่างถูกต้อง และใช้ JSON_PRETTY_PRINT เพื่อทำให้ข้อมูล JSON อ่านง่ายขึ้น
echo json_encode($events, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
