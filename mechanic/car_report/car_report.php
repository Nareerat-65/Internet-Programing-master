<?php
include 'connect.php';

$query_result = mysqli_query($conn, "SELECT * FROM repair");
$repair_data = mysqli_fetch_all($query_result, MYSQLI_ASSOC);

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
            <li><a href="..\car_report\car_report.html">รายงานสภาพรถพยาบาล</a></li>
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
                            <?php foreach ($repair_data as $row) { ?>
                                <option value="<?php echo $row["ambulance_id"]; ?>">
                                    <?php echo $row["ambulance_id"]; ?>
                                </option>
                            <?php } ?>
                        </select>

                        <label for="filter-status">สถานะการซ่อม:</label>
                        <select id="filter-status">
                            <option value="">-- เลือกสถานะ --</option>
                            <?php foreach ($repair_data as $row) { ?>
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
                        <!-- <div class="save-button">
                            <button onclick="saveRepair()">บันทึกข้อมูล</button>
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
                            <td><?php echo $rs_result['repair_success_datetime']; ?></td>
                            <td><?php echo $rs_result['repair_staff_id']; ?></td>
                            <td><?php echo $rs_result['repair_status']; ?></td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
        </div>


    </div>
    
</body>

</html>