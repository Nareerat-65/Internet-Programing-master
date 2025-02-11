// รอให้เอกสารโหลดเสร็จก่อนที่จะเริ่มทำงาน
document.addEventListener('DOMContentLoaded', function () {

    // กำหนด DOM element สำหรับปฏิทิน
    var calendarEl = document.getElementById('calendar');
    // สร้างอินสแตนซ์ FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',  // ตั้งค่าการแสดงผลเริ่มต้นเป็นแบบรายสัปดาห์
        editable: true, // อนุญาตให้แก้ไขเหตุการณ์บนปฏิทินได้
        selectable: true, // อนุญาตให้เลือกช่วงเวลาบนปฏิทินได้
        timeZone: 'Asia/Bangkok', // ตั้งค่าเขตเวลาให้ตรงกับ 'Asia/Bangkok'

        // ตั้งค่าการแสดงผลส่วนหัวของปฏิทิน
        headerToolbar: {
            left: 'prev,next today',  // ปุ่มเลื่อนสัปดาห์และปุ่มกลับมาวันปัจจุบัน
            center: 'title', // ตำแหน่งแสดงชื่อเดือน/ช่วงเวลา
            right: 'timeGridWeek,timeGridDay' // ปุ่มเปลี่ยนมุมมองรายวันหรือรายสัปดาห์
        },

        // แสดงข้อมูลของ event แบบบล็อกเต็มเวลา
        eventDisplay: 'block',
        // ตั้งค่าการแสดงเวลาแบบ 24 ชั่วโมง
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },

        // ดึงข้อมูล events จากไฟล์ `fetch_events.php`
        events: {
            url: 'fetch_events.php',

            // กรณีดึงข้อมูลไม่สำเร็จจะแสดงการแจ้งเตือน
            failure: function () {
                alert('There was an error while fetching events!');
            },

            // เมื่อดึงข้อมูลสำเร็จ
            success: function (data) {
                console.log('Fetched events:', data);
                data.forEach(event => {
                    if (event.type === 'ambulance') {
                        event.color = '#3498DB'; // ตั้งสีฟ้าสำหรับ ambulance
                    } else if (event.type === 'event') {
                        event.color = '#9B59B6'; // ตั้งสีม่วงสำหรับ event
                    }
                });
            },

            // กรณีเกิดข้อผิดพลาดในการดึงข้อมูล
            error: function (xhr, status, error) {
                console.error('Error fetching events:', error);
                console.error('Response:', xhr.responseText);
            }
        },

        // กำหนดพฤติกรรมเมื่อเลือกช่วงเวลาบนปฏิทิน
        select: function (info) {
            var title = prompt('ใส่ชื่อรายการของคุณ:'); // รับข้อมูลชื่อรายการจากผู้ใช้
            var type = prompt('ใส่ประเภทงาน (ambulance หรือ event):'); // รับประเภทของงานจากผู้ใช้ (ambulance หรือ event)

            // ตรวจสอบว่ามีการกรอกข้อมูลทั้งชื่อรายการและประเภทงานหรือไม่
            if (title && type) {
                var eventData = {
                    title: title,
                    start: info.start.toISOString(),
                    end: info.end.toISOString(),
                    type: type
                };
                console.log('Event Data:', eventData);

                // เพิ่มเหตุการณ์ใหม่ลงในปฏิทิน
                calendar.addEvent(eventData);

                // ส่งข้อมูลเหตุการณ์ไปยัง `save_event.php` เพื่อบันทึกลงฐานข้อมูล
                // ใช้ AJAX ที่เป็น method ของ JQuery ในการส่งข้อมูลไปยังเซิร์ฟเวอร์โดยไม่ต้องโหลดหน้าใหม่
                $.ajax({
                    url: 'save_event.php',
                    method: 'POST',
                    data: {
                        title: eventData.title,
                        type: eventData.type,
                        start: eventData.start,
                        end: eventData.end
                    },
                    success: function (response) {
                        console.log(response);
                        alert('Event saved!');
                    },
                    error: function (xhr, status, error) {
                        console.error('Error saving event:', error);
                        console.error('Response:', xhr.responseText);
                        alert('There was an error while saving the event!');
                    }
                });
            }
            // ยกเลิกการเลือกช่วงเวลาหลังดำเนินการเสร็จ
            calendar.unselect();
        }
    });
    // แสดงปฏิทินบนหน้าเว็บ
    calendar.render();
});
