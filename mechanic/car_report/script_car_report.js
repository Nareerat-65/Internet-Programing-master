const results = {};

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('carReportForm');
    const levelField = form.querySelector('#level');
    const numberField = form.querySelector('#number');
   
    
    //form.addEventListener('submit', submitForm);

    levelField.addEventListener('change', () => {
        const level = levelField.value;
        let numberOptions = '';

        if (level === 'ระดับ 1') {
            numberOptions = `
                <option value="2">ขค5678</option>
                <option value="4">ตฎ1142</option>
            `;
        } else if (level === 'ระดับ 2') {
            numberOptions = `
                <option value="1">กข1234</option>
                <option value="5">ลนณ886</option>
            `;    
        } else if (level === 'ระดับ 3') {
            numberOptions = `
                <option value="3">ฉช378</option>
            `;
        }
        numberField.innerHTML = `<option value="" disabled selected>ระบุทะเบียนรถ</option>${numberOptions}`;
    });
    
    // Check if carTopic exists before adding event listener
});

//function submitForm(event) {
    //event.preventDefault(); 
    //location.href = 'car_report_success.html'; 
//}

function toggleDropdown(name) {
    const radioNo = document.querySelector(`input[name="${name}"][value="ไม่พร้อม"]`);
    const dropdown = document.getElementById(`${name}-dropdown`);
    const otherField = document.getElementById(`${name}-other`);

    dropdown.disabled = !radioNo.checked;

    if (!radioNo.checked) {
        otherField.disabled = true; 
        otherField.value = "";     
    }

    const selectedValue = document.querySelector(`input[name="${name}"]:checked`).value;
    results[name] = { status: selectedValue, dropdown: dropdown.value, other: otherField.value };
    console.log(results);
}

function updateDropdown(name) {
    const dropdown = document.getElementById(`${name}-dropdown`);
    const otherField = document.getElementById(`${name}-other`);

    if (dropdown.value === "other") {
        otherField.disabled = false;
    } else {
        otherField.disabled = true;
        otherField.value = ""; 
    }

    results[name].dropdown = dropdown.value;
    results[name].other = otherField.value;
    console.log(results);
}

function resetForm() {
    document.getElementById('carReportForm').reset();
}