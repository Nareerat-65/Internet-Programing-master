<?php
include 'con_repair.php';

$query_result = mysqli_query($conn, "SELECT * FROM repair");
$repair_data = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

$ambulance_query = mysqli_query($conn, "SELECT DISTINCT ambulance_id FROM repair ORDER BY ambulance_id");
$ambulance_data = mysqli_fetch_all($ambulance_query, MYSQLI_ASSOC);

$status_query = mysqli_query($conn, "SELECT DISTINCT repair_status FROM repair WHERE repair_status IS NOT NULL ORDER BY repair_status");
$status_data = mysqli_fetch_all($status_query, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_repair.css">
    <script src="script_repair.js?ts=<?php echo time(); ?>" defer></script>
    <title>การซ่อมอุปกรณ์และรถพยาบาล</title>
</head>

<body>
    <nav>
        <ul class="menu">
            <li><a href="..\car_report\car_report.php">รายงานสภาพรถพยาบาล</a></li>
            <li><a href="repair.php">การซ่อมอุปกรณ์และรถพยาบาล</a></li>
        </ul>
    </nav>
    <div class="header">
        <h1 class="title">การซ่อมอุปกรณ์และรถพยาบาล</h1>
    </div>
    <div class="table-container">
        <form action="repair.php" method="post">
            <div>
                <div class="filter-section">
                    <div>
                        <label for="filter-date">วันที่รับซ่อม:</label>
                        <input type="date" id="filter-date">

                        <label for="filter-ambulance-ID">ID รถพยาบาล:</label>
                        <select id="filter-ambulance-ID">
                            <option value="">-- ID รถพยาบาล --</option>
                            <?php foreach ($ambulance_data as $row) { ?>
                                <option value="<?php echo $row["ambulance_id"]; ?>">
                                    <?php echo $row["ambulance_id"]; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <label for="filter-status">สถานะการซ่อม:</label>
                        <select id="filter-status">
                            <option value="">-- เลือกสถานะ --</option>
                            <?php foreach ($status_data as $row) { ?>
                                <option value="<?php echo $row["repair_status"]; ?>">
                                    <?php echo $row["repair_status"]; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div>
                        <div class="add-button">
                            <button onclick="addRepair()">เพิ่มการแจ้งซ่อม +</button>
                        </div>
                        <!-- <div class="edit-button">
                            <button onclick="saveRepair()">แก้ไขข้อมูล</button>
                        </div> -->
                    </div>
                </div>
            </div>
        </form>

        <div>
            <table id="my-list">
                <!-- my-list เป็น id ที่จะให้แสดงผลลัพธ์การกรองข้อมูล -->
                <thead>
                    <tr>
                        <th>วันที่รับซ่อม</th>
                        <th>ID รถพยาบาล</th>
                        <th>ประเภทการซ่อม</th>
                        <th>อุปกรณ์/อะไหล่</th>
                        <th>สาเหตุ</th>
                        <th>วันที่เสร็จสิ้น</th>
                        <th>ID ผู้รายงาน</th>
                        <th>สถานะการซ่อม</th>
                    </tr>
                </thead>
                <tbody id="repair-table-body">

                    <!-- เมื่อเข้ามาครั้งแรก จะแสดงข้อมูลทั้งหมดจากตาราง repair -->

                    <?php foreach ($repair_data as $rs_result) { ?>
                        <tr>
                            <td><?php echo $rs_result['repair_date']; ?></td>
                            <td><?php echo $rs_result['ambulance_id']; ?></td>
                            <td><?php echo $rs_result['repair_type']; ?></td>
                            <td><?php echo $rs_result['repairing']; ?></td>
                            <td><?php echo $rs_result['repair_reason']; ?></td>
                            <td>
                                <!-- ถ้าข้อมูล repair_success_datetime ในตาราง repair ไม่ใช่ค่าว่าง (ทำดำเนินการเสร็จแล้ว)-->
                                <!-- ให้แสดงวันที่ซ่อมรายการนั้น ๆ เสร็จ -->
                                <!-- ถ้าไม่ตรงตามเงื่อนไขด้านบน ให้สามารถเลือกวันเวลาเพื่ออัพเดตในฐานข้อมูลได้ -->
                                <?php if ($rs_result['repair_success_datetime'] !== null) { ?>
                                    <?php echo $rs_result['repair_success_datetime']; ?>
                                <?php } else { ?>
                                    <input type="datetime-local"
                                    value="<?php echo $rs_result['repair_success_datetime']; ?>"
                                    onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'date')">
                                <?php } ?>
                            </td>
                            <td><?php echo $rs_result['repair_staff_id']; ?></td>
                            <td>
                                <!-- เมื่อถูกอัพเดตว่า "เสร็จสิ้น" จะ diable select -->
                                <!-- เมื่อถูกอัพเดตว่า "กำลังดำเนินการ" จะ diable "กำลังดำเนินการ" -->
                                <!-- เมื่อถูกอัพเดตว่า "รอดำเนินการ" จะแสดงตัวเลือกทุกอัน -->
                                <?php if ($rs_result['repair_status'] == 'เสร็จสิ้น') { ?>
                                    <select disabled ="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                                        <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                    </select>
                                <?php } elseif ($rs_result['repair_status'] == 'กำลังดำเนินการ') { ?>
                                    <select onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                                        <option disabled value="กำลังดำเนินการ" <?php echo ($rs_result['repair_status'] == 'กำลังดำเนินการ') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                        <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                    </select>
                                <?php } else { ?>
                                    <select onchange="updateRepair(<?php echo $rs_result['repair_id']; ?>, this.value, 'status')">
                                        <option value="รอดำเนินการ" <?php echo ($rs_result['repair_status'] == 'รอดำเนินการ') ? 'selected' : ''; ?>>รอดำเนินการ</option>
                                        <option value="กำลังดำเนินการ" <?php echo ($rs_result['repair_status'] == 'กำลังดำเนินการ') ? 'selected' : ''; ?>>กำลังดำเนินการ</option>
                                        <option value="เสร็จสิ้น" <?php echo ($rs_result['repair_status'] == 'เสร็จสิ้น') ? 'selected' : ''; ?>>เสร็จสิ้น</option>
                                    </select>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>


    </div>

</body>

</html>