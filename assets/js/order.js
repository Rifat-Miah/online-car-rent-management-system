document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const submitBtn = document.getElementById('submitBtn');
    const previewDiv = document.getElementById('costPreview');
    const carIdInput = document.getElementById('car_id');


    if (!startInput || !endInput) return;

    function validateAndCalculate() {
        const startDateVal = startInput.value;
        const endDateVal = endInput.value;

        if (startDateVal && endDateVal) {
            let sDate = new Date(startDateVal);
            let eDate = new Date(endDateVal);
            let today = new Date();
            today.setHours(0, 0, 0, 0); 

            
            if (sDate < today) {
                previewDiv.innerHTML = "<span style='color:red;'>Start date cannot be in the past.</span>";
                submitBtn.disabled = true; 
                return;
            }
            if (eDate <= sDate) {
                previewDiv.innerHTML = "<span style='color:red;'>End date must be after the start date.</span>";
                submitBtn.disabled = true; 
                return;
            }

            
            let formData = new FormData();
            formData.append('car_id', carIdInput.value);
            formData.append('start_date', startDateVal);
            formData.append('end_date', endDateVal);

           
            fetch('index.php?controller=order&action=calculateCost', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.error) {
                    previewDiv.innerHTML = "<span style='color:red;'> " + data.error + "</span>";
                    submitBtn.disabled = true;
                } else {
                   
                    previewDiv.innerHTML = "Total Cost (" + data.days + " days): BDT " + data.total;
                    submitBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error("AJAX Error: ", err);
            });
        }
    }

   
    startInput.addEventListener('change', validateAndCalculate);
    endInput.addEventListener('change', validateAndCalculate);
});