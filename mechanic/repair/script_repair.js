document.addEventListener('DOMContentLoaded', () => {
    loadRepairs();

    const filterDate = document.getElementById("filter-date");
    const filterStatus = document.getElementById("filter-status");
    const filterLicense = document.getElementById("filter-license");

    filterDate.addEventListener('input', filterTable);
    filterStatus.addEventListener('change', filterTable);
    filterLicense.addEventListener('change', filterTable);
});

function loadRepairs() {
    const repairs = JSON.parse(localStorage.getItem('repairs')) || [];
    const repairTableBody = document.getElementById('repair-table-body');

    // ล้างตารางก่อนที่จะโหลดข้อมูลใหม่
    repairTableBody.innerHTML = '';

    repairs.forEach((repair, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${repair.date}</td>
            <td>${repair.license}
                
            </td>
            <td>${repair.category}</td>
            <td>${repair.device}</td>
            <td>${repair.reason}</td>
            <td><input type="date" value="${repair.dueDate || ''}"></td>
            <td>${repair.driver}</td>
            <td>
                <select>
                    <option ${repair.status === 'รออะไหล่' ? 'selected' : ''}>รออะไหล่</option>
                    <option ${repair.status === 'กำลังซ่อม' ? 'selected' : ''}>กำลังซ่อม</option>
                    <option ${repair.status === 'พร้อมใช้งาน' ? 'selected' : ''}>พร้อมใช้งาน</option>
                </select>
            </td>
            <td><button onclick="deleteRepair(${index})">ลบ</button></td>
        `;
        repairTableBody.appendChild(row);
    });
}

function filterTable() {
    const filterDate = document.getElementById("filter-date").value;
    const filterStatus = document.getElementById("filter-status").value;
    const filterLicense = document.getElementById("filter-license").value;

    const rows = document.querySelectorAll("tbody tr");
    rows.forEach(row => {
        const rowDate = row.cells[0].textContent.trim();
        const rowStatus = row.cells[7].querySelector("select").value.trim();
        const rowLicense = row.cells[1].textContent.trim();

        console.log("Row values:", { rowDate, rowStatus, rowLicense });

        // ตรวจสอบเงื่อนไขการกรอง
        const matchDate = !filterDate || rowDate === filterDate;
        const matchStatus = !filterStatus || rowStatus === filterStatus;
        const matchLicense = !filterLicense || rowLicense === filterLicense;

        console.log("Match conditions:", { matchDate, matchStatus, matchLicense });

        // แสดงหรือซ่อนแถวตามเงื่อนไข
        row.style.display = matchDate && matchStatus && matchLicense ? "" : "none";
    });
}


function addRepair() {
    window.location.href = 'from_repair.html';
}

function deleteRepair(index) {
    let repairs = JSON.parse(localStorage.getItem('repairs')) || [];
    repairs.splice(index, 1);
    localStorage.setItem('repairs', JSON.stringify(repairs));
    loadRepairs(); // Reload the table
}

// ฟังก์ชันบันทึกข้อมูล
function saveRepair() {
    const rows = document.querySelectorAll("tbody tr");
    const updatedData = [];

    rows.forEach(row => {
        const rowData = {
            dateReceived: row.cells[0].textContent.trim(),
            license: row.cells[1].querySelector("input").value,
            repairType: row.cells[2].textContent.trim(),
            equipment: row.cells[3].textContent.trim(),
            reason: row.cells[4].textContent.trim(),
            dueDate: row.cells[5].querySelector("input").value,
            reporter: row.cells[6].textContent.trim(),
            status: row.cells[7].querySelector("select").value
        };
        updatedData.push(rowData);
    });

    console.log("ข้อมูลที่บันทึก:", updatedData);
    alert("ข้อมูลถูกบันทึกเรียบร้อยแล้ว!");

    // คุณสามารถส่ง `updatedData` ไปยัง Backend ด้วย Fetch API ได้ เช่น:
    // fetch('/api/save', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify(updatedData)
    // }).then(response => response.json())
    //   .then(result => console.log(result))
    //   .catch(error => console.error('Error:', error));
}