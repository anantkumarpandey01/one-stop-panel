<?php
require 'vendor/autoload.php';
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
include 'inc/config.php';
include 'inc/header.php';
 

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify reCAPTCHA
    $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY'; // Replace with your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_response = json_decode($verify);
    $captcha_response = 1;

    if ($captcha_response == 1) {
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $vehicle_reg = filter_var($_POST['vehicle_reg'], FILTER_SANITIZE_STRING);
        $insurer = filter_var($_POST['insurer'], FILTER_SANITIZE_STRING);
        $claim_num = filter_var($_POST['claim_num'], FILTER_SANITIZE_STRING);
        $notes = filter_var($_POST['notes'], FILTER_SANITIZE_STRING);

        // Send email using MailerSend
        try {
            var_dump($_ENV['MAILERSEND_API_KEY']); // Debug: Check if API key is loaded
            $mailersend = new MailerSend(['api_key' => $_ENV['MAILERSEND_API_KEY']]);
            $recipients = [new Recipient($email, $name)];
            $emailParams = (new EmailParams())
                ->setFrom('onestoppanel2023@gmail.com') // Ensure this is verified
                ->setTo($recipients)
                ->setSubject('Appointment Confirmation')
                ->setHtml("
                    <h3>Appointment Details</h3>
                    <p><strong>Date:</strong> $date</p>
                    <p><strong>Time:</strong> $time</p>
                    <p><strong>Name:</strong> $name</p>
                    <p><strong>Phone:</strong> $phone</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Address:</strong> $address</p>
                    <p><strong>Vehicle Registration:</strong> $vehicle_reg</p>
                    <p><strong>Insurer:</strong> $insurer</p>
                    <p><strong>Claim Number:</strong> $claim_num</p>
                    <p><strong>Notes:</strong> $notes</p>
                ")
                ->setText("Appointment Details\nDate: $date\nTime: $time\nName: $name\nPhone: $phone\nEmail: $email\nAddress: $address\nVehicle Registration: $vehicle_reg\nInsurer: $insurer\nClaim Number: $claim_num\nNotes: $notes");

            $mailersend->email->send($emailParams);
            $success = 'Appointment booked successfully! Confirmation email sent.';
        } catch (Exception $e) {
            $error = 'Failed to send email: ' . $e->getMessage();
        }
    } else {
        $error = 'reCAPTCHA verification failed. Please try again.';
    }
}
?>

<div class="ak-height-100 ak-height-lg-40"></div>
<div class="container mt-5">
    <div class="contact-content">
        <div class="contact-title-section" data-aos="fade-up" data-aos-delay="700">
            <h5 class="contact-form-title text-uppercase">Request for a Appointment</h5>
            <p>Complete the multi-step form to book your appointment.</p>
        </div>
        <div class="ak-height-25 ak-height-lg-20"></div>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="multi-step-form" class="multi-step-form" method="POST" action="">
            <!-- Step 1: Select Date and Time -->
            <div class="step active" data-step="1">
                <h3>Pick a Date and Time</h3>
                <p>Duration: 15 minutes</p>
                <div class="calendar-section" data-aos="fade-up" data-aos-delay="750">
                    <div class="calendar-header">
                        <button type="button" class="nav-month" data-dir="-1">&lt;</button>
                        <span id="month-year">September 2025</span>
                        <button type="button" class="nav-month" data-dir="1">&gt;</button>
                    </div>
                    <div class="calendar-days">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>
                    <div class="calendar-dates" id="calendar-dates"></div>
                    <div class="time-slots">
                        <p>Available starting times for <span id="selected-date"></span>:</p>
                        <div class="time-options" id="time-options">
                            <div class="time-column" id="before-noon"></div>
                            <div class="time-column" id="after-noon"></div>
                        </div>
                    </div>
                    <input type="hidden" name="date" id="selected-date-value">
                </div>
            </div>

            <!-- Step 2: Provide Information -->
            <div class="step" data-step="2">
                <h3>Provide Information</h3>
                <p>Time: <span id="info-date"></span>, <span id="info-time"></span> - <span id="info-end-time"></span></p>
                <div class="form-inputs" data-aos="fade-up" data-aos-delay="750">
                    <label for="name">Your name*</label>
                    <input type="text" name="name" id="name" placeholder="Prefix, First and Last Name" required>
                    
                    <label for="phone">Your mobile phone*</label>
                    <select name="country_code">
                        <option>New Zealand (+64)</option>
                    </select>
                    <input type="text" name="phone" id="phone" placeholder="Include area code (+64 21123456)" required>
                    
                    <label hidden for="sms" style="display:flex;"><input type="checkbox" id="sms" name="sms" checked> OK to send me booking notifications via SMS</label>
                    
                    <label for="email">Your email*</label>
                    <input type="email" name="email" id="email" placeholder="Booking notifications will be sent to this email" required>
                    
                    <label for="address">Full Street Address</label>
                    <input type="text" name="address" id="address" placeholder="eg 123 Street Name, Suburb, City">
                    
                    <label for="vehicle_reg">Vehicle Registration*</label>
                    <input type="text" name="vehicle_reg" id="vehicle_reg" placeholder="Registration number of vehicle" required>
                    
                    <label for="insurer">Insurer (If Applicable)</label>
                    <input type="text" name="insurer" id="insurer" placeholder="Name of Insurance Company or Private if none">
                    
                    <label for="claim_num">Insurance Claim Number (If Known)</label>
                    <input type="text" name="claim_num" id="claim_num" placeholder="Claim Number provided by Insurance Company or None">
                    
                    <label for="notes">Any extra notes that will assist?</label>
                    <input type="text" name="notes" id="notes">
                    
                    <!-- reCAPTCHA -->
                    <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                </div>
            </div>

            <div class="form-navigation">
                <button type="button" class="nav-btn prev">Previous</button>
                <button type="button" class="nav-btn next">Next</button>
                <button type="submit" class="nav-btn submit" style="display:none;">Submit</button>
            </div>
        </form>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('multi-step-form');
    const steps = form.querySelectorAll('.step');
    const prevBtn = form.querySelector('.prev');
    const nextBtn = form.querySelector('.next');
    const submitBtn = form.querySelector('.submit');
    let currentStep = 1;
    let currentDate = new Date('2025-09-18');

    const nzTime = new Date(new Date().toLocaleString('en-US', { timeZone: 'Pacific/Auckland' }));
    const currentHour = nzTime.getHours();
    const currentDateStr = `${nzTime.getFullYear()}-${String(nzTime.getMonth() + 1).padStart(2, '0')}-${String(nzTime.getDate()).padStart(2, '0')}`;

    function generateTimeSlots(selectedDate) {
        const beforeNoon = document.getElementById('before-noon');
        const afterNoon = document.getElementById('after-noon');
        beforeNoon.innerHTML = '<h4>Before 12:00</h4>';
        afterNoon.innerHTML = '<h4>After 12:00</h4>';

        const isToday = selectedDate === currentDateStr;
        const startHour = isToday ? Math.max(currentHour + 1, 8) : 8;
        const endHour = 17;
        let hasBeforeNoon = false;
        let hasAfterNoon = false;

        for (let hour = startHour; hour <= endHour; hour++) {
            const times = [`${String(hour).padStart(2, '0')}:00`, `${String(hour).padStart(2, '0')}:30`];
            times.forEach(time => {
                if (hour >= endHour && time === `${hour}:30`) return;
                const label = document.createElement('label');
                const input = document.createElement('input');
                input.type = 'radio';
                input.name = 'time';
                input.value = time;
                label.appendChild(input);
                label.appendChild(document.createTextNode(time));
                if (parseInt(time.split(':')[0]) < 12 && hour < 12) {
                    beforeNoon.appendChild(label);
                    hasBeforeNoon = true;
                } else if (parseInt(time.split(':')[0]) >= 12) {
                    afterNoon.appendChild(label);
                    hasAfterNoon = true;
                }
            });
        }

        if (!hasBeforeNoon) beforeNoon.innerHTML += '<p>No times available</p>';
        if (!hasAfterNoon) afterNoon.innerHTML += '<p>No times available</p>';
    }

    function showStep(step) {
        steps.forEach((s, index) => s.classList.toggle('active', index + 1 === step));
        prevBtn.style.display = step > 1 ? 'inline-block' : 'none';
        nextBtn.style.display = step < steps.length ? 'inline-block' : 'none';
        submitBtn.style.display = step === steps.length ? 'inline-block' : 'none';

        if (step === 1) {
            renderCalendar();
            const selectedDate = document.getElementById('selected-date-value').value || currentDateStr;
            generateTimeSlots(selectedDate);
        }
        if (step === 2) {
            const time = form.querySelector('input[name="time"]:checked')?.value;
            const date = document.getElementById('selected-date-value').value;
            const endTime = new Date(`${date} ${time}`).getTime() + 15 * 60000;
            document.getElementById('info-date').textContent = date ? new Date(date).toLocaleDateString() : 'Unknown';
            document.getElementById('info-time').textContent = time || 'Unknown';
            document.getElementById('info-end-time').textContent = new Date(endTime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }

    function renderCalendar() {
        const datesContainer = document.getElementById('calendar-dates');
        datesContainer.innerHTML = '';
        const monthYear = document.getElementById('month-year');
        monthYear.textContent = currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const startDay = firstDay.getDay();
        const totalDays = lastDay.getDate();

        for (let i = 0; i < startDay; i++) {
            const emptyDiv = document.createElement('div');
            datesContainer.appendChild(emptyDiv);
        }

        for (let day = 1; day <= totalDays; day++) {
            const dateDiv = document.createElement('div');
            dateDiv.textContent = day;
            const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const currentDateObj = new Date(dateStr);
            dateDiv.dataset.date = dateStr;

            if (currentDateObj >= nzTime) {
                dateDiv.addEventListener('click', () => {
                    if (currentDateObj > nzTime || (currentDateObj.toDateString() === nzTime.toDateString() && currentHour < 17)) {
                        datesContainer.querySelectorAll('div').forEach(d => d.classList.remove('selected-date'));
                        dateDiv.classList.add('selected-date');
                        document.getElementById('selected-date').textContent = currentDateObj.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
                        document.getElementById('selected-date-value').value = dateStr;
                        generateTimeSlots(dateStr);
                    }
                });
            } else {
                dateDiv.style.opacity = '0.5';
                dateDiv.style.cursor = 'not-allowed';
            }
            datesContainer.appendChild(dateDiv);
        }
    }

    showStep(currentStep);

    document.querySelectorAll('.nav-month').forEach(button => {
        button.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + parseInt(button.dataset.dir));
            renderCalendar();
        });
    });

    nextBtn.addEventListener('click', () => {
        if (currentStep === 1 && (!form.querySelector('input[name="time"]:checked') || !document.getElementById('selected-date-value').value)) return;
        if (currentStep < steps.length) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    form.querySelectorAll('#time-options').forEach(container => {
        container.addEventListener('change', (e) => {
            if (e.target.name === 'time' && currentStep === 1) {
                const time = e.target.value;
                const date = document.getElementById('selected-date-value').value;
                if (date && time) alert(`Your selected date: ${new Date(date).toLocaleDateString()} and time: ${time}`);
            }
        });
    });

    renderCalendar();
});
</script>

<?php include 'inc/footer.php'; ?>