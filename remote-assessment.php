<?php
require 'vendor/autoload.php';
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Attachment;
include 'inc/config.php';
include 'inc/header.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify reCAPTCHA
    $recaptcha_secret = $_ENV['RECAPTCHA_SECRET'];
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_result = json_decode($verify);
  
    if ($captcha_result->success == 1) {
        $repair_centre = filter_var($_POST['repair_centre'], FILTER_SANITIZE_STRING);
        $first_name = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
        $last_name = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_STRING);
        $alt_phone = filter_var($_POST['alt_phone'], FILTER_SANITIZE_STRING);
        $company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
        $registration = filter_var($_POST['registration'], FILTER_SANITIZE_STRING);
        $make = filter_var($_POST['make'], FILTER_SANITIZE_STRING);
        $model = filter_var($_POST['model'], FILTER_SANITIZE_STRING);
        $year = filter_var($_POST['year'], FILTER_SANITIZE_STRING);
        $insurance_company = filter_var($_POST['insurance_company'], FILTER_SANITIZE_STRING);
        $accident_desc = filter_var($_POST['accident_desc'], FILTER_SANITIZE_STRING);
        $claim_number = filter_var($_POST['claim_number'], FILTER_SANITIZE_STRING);

        // Handle file attachments with label names
        $attachments = [];
        $upload_fields = [
            'upload1' => 'Overall image of vehicle and damage',
            'upload2' => 'Overall image of vehicle front (including rego)',
            'upload3' => 'Images of vehicle from rear (including rego)',
            'upload4' => 'Image of undamaged side',
            'upload5' => 'Image of damaged area',
            'upload6' => 'Close up of internal damage',
            'upload7' => 'Registration sticker',
            'upload8' => 'Road user charges (RUC) label (For diesel vehicles only)',
            'upload9' => 'Warrant of fitness sticker',
            'upload10' => 'Image showing current mileage',
            'upload11' => 'Overall image of the interior',
            'upload12' => 'Any old unrelated damage'
        ];
        foreach ($upload_fields as $field => $label) {
            if (!empty($_FILES[$field]['name']) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $fileContent = file_get_contents($_FILES[$field]['tmp_name']);
                $fileExtension = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
                $fileName = $label . '.' . $fileExtension;
                $attachments[] = new Attachment($fileContent, $fileName, 'attachment');
            }
        }

        // Send email using MailerSend
        try {
            $mailersend = new MailerSend(['api_key' => $_ENV['MAILERSEND_API_KEY']]);
            $recipients = [new Recipient("onestoppanel2023@gmail.com", "onestoppanel")];
            $emailParams = (new EmailParams())
                ->setFrom('no-reply@onestoppanelandpaint.co.nz')
                ->setFromName("onestoppanel")
                ->setRecipients($recipients)
                ->setReplyTo($email)
                ->setSubject('Remote Assessment Request')
                ->setHtml("
                    <h3>Remote Assessment Request</h3>
                    <p><strong>Repair Centre:</strong> $repair_centre</p>
                    <h4>Driver's Info</h4>
                    <p><strong>First Name:</strong> $first_name</p>
                    <p><strong>Last Name:</strong> $last_name</p>
                    <p><strong>Email:</strong> $email</p>
                    <p><strong>Mobile:</strong> $mobile</p>
                    <p><strong>Alternate Phone:</strong> $alt_phone</p>
                    <p><strong>Company:</strong> $company</p>
                    <p><strong>Address:</strong> $address</p>
                    <h4>Vehicle Info</h4>
                    <p><strong>Registration:</strong> $registration</p>
                    <p><strong>Make:</strong> $make</p>
                    <p><strong>Model:</strong> $model</p>
                    <p><strong>Year:</strong> $year</p>
                    <h4>Insurance Info</h4>
                    <p><strong>Insurance Company:</strong> $insurance_company</p>
                    <p><strong>Accident Description:</strong> $accident_desc</p>
                    <p><strong>Claim Number:</strong> $claim_number</p>
                ")
                ->setText("Remote Assessment Request\nRepair Centre: $repair_centre\n\nDriver's Info\nFirst Name: $first_name\nLast Name: $last_name\nEmail: $email\nMobile: $mobile\nAlternate Phone: $alt_phone\nCompany: $company\nAddress: $address\n\nVehicle Info\nRegistration: $registration\nMake: $make\nModel: $model\nYear: $year\n\nInsurance Info\nInsurance Company: $insurance_company\nAccident Description: $accident_desc\nClaim Number: $claim_number")
                ->setAttachments($attachments);

            $mailersend->email->send($emailParams);
            $success = 'Assessment request submitted successfully!';
        } catch (Exception $e) {
            $error = 'Failed to submit request: ' . $e->getMessage();
        }
    } else {
        $error = 'reCAPTCHA verification failed. Please try again.';
    }
}
?>

<div class="ak-height-100 ak-height-lg-40"></div>
<div class="container my-5">
    <h2 class="text-center mb-4 text-white">Remote Assessment Request</h2>
    <p class="text-center text-light">
        Please fill out the form below and an assessment will be processed for your vehicle. 
        Please note damage to your vehicle will be further inspected once it arrives at our facilities 
        and all estimates are for visible damage only.
    </p>

    <form method="post" enctype="multipart/form-data" class="assessment-form">
        <?php if ($success): ?>
            <div class="alert alert-success" style="height:fit-content"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger" style="height:fit-content"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Closest Repair Centre -->
        <div class="mb-3">
            <label class="form-label">Please indicate your closest repair centre</label>
            <select name="repair_centre" class="form-control" required>
                <option value="">Choose an option</option>
                <option value="centre1">Centre 1</option>
                <option value="centre2">Centre 2</option>
            </select>
        </div>

        <!-- Driver Info -->
        <h5 class="section-title">Driver's Info</h5>
        <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="first_name" class="form-control" placeholder="First Name" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="last_name" class="form-control" placeholder="Last Name" required></div>
            <div class="col-md-4 mb-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="mobile" class="form-control" placeholder="Mobile" required></div>
            <div class="col-md-4 mb-3"><input type="text" name="alt_phone" class="form-control" placeholder="Alternate Phone"></div>
            <div class="col-md-4 mb-3"><input type="text" name="company" class="form-control" placeholder="Company"></div>
            <div class="col-md-12 mb-3"><input type="text" name="address" class="form-control" placeholder="Full Address"></div>
        </div>

        <!-- Vehicle Info -->
        <h5 class="section-title">Vehicle Info</h5>
        <div class="row">
            <div class="col-md-3 mb-3"><input type="text" name="registration" class="form-control" placeholder="Registration"></div>
            <div class="col-md-3 mb-3"><input type="text" name="make" class="form-control" placeholder="Vehicle Make"></div>
            <div class="col-md-3 mb-3"><input type="text" name="model" class="form-control" placeholder="Model"></div>
            <div class="col-md-3 mb-3"><input type="text" name="year" class="form-control" placeholder="Year"></div>
        </div>

        <!-- Insurance Info -->
        <h5 class="section-title">Insurance Info</h5>
        <div class="row">
            <div class="col-md-4 mb-3"><input type="text" name="insurance_company" class="form-control" placeholder="Insurance Company"></div>
            <div class="col-md-4 mb-3"><input type="text" name="accident_desc" class="form-control" placeholder="Description of Accident"></div>
            <div class="col-md-4 mb-3"><input type="text" name="claim_number" class="form-control" placeholder="Insurance Claim number"></div>
        </div>

        <!-- Image Uploads -->
        <h5 class="section-title mt-4">Digital Image Requirements for Remote Assessments</h5>
        <p class="text-light">We require an overall view of the vehicle including damaged and undamaged areas. Max size 10 MB per attachment.</p>
        <div class="row">
            <?php
            $uploads = [
                "Overall image of vehicle and damage",
                "Overall image of vehicle front (including rego)",
                "Images of vehicle from rear (including rego)",
                "Image of undamaged side",
                "Image of damaged area",
                "Close up of internal damage",
                "Registration sticker",
                "Road user charges (RUC) label (For diesel vehicles only)",
                "Warrant of fitness sticker",
                "Image showing current mileage",
                "Overall image of the interior",
                "Any old unrelated damage"
            ];
            foreach($uploads as $i => $label) {
                echo '<div class="col-md-6 mb-3">
                        <label class="form-label text-light">'.($i+1).'. '.$label.'</label>
                        <input type="file" name="upload'.($i+1).'" class="form-control" accept="image/*">
                      </div>';
            }
            ?>
        </div>

        <!-- reCAPTCHA -->
        <div class="g-recaptcha" data-sitekey="<?php echo $_ENV['RECAPTCHA_PUBLIC']; ?>"></div>

        <button type="submit" class="btn-submit w-100 mt-3">Submit</button>
    </form>
</div>

<style>
input, select{
    width: 100%;
    padding: 10px;
    margin-bottom: 10px !important;
    background-color: #444 !important;
    border: none !important;
    border-radius: 5px;
    color: #fff !important;
}

h5 {
    font-size: 20px;
    color: #d3d3d3;
    margin-bottom: 10px;
}

button {
    padding: 10px 20px;
    background: #ff4500;
    border: none;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
</style>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php include 'inc/footer.php'; ?>