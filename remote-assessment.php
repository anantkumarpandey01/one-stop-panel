<?php include 'inc/config.php'; ?>
<?php include 'inc/header.php'; ?>
<div class="ak-height-100 ak-height-lg-40"></div>
<div class="container my-5">
    <h2 class="text-center mb-4 text-white">Remote Assessment Request</h2>
    <p class="text-center text-light">
        Please fill out the form below and an assessment will be processed for your vehicle. 
        Please note damage to your vehicle will be further inspected once it arrives at our facilities 
        and all estimates are for visible damage only.
    </p>

    <form method="post" enctype="multipart/form-data" class="assessment-form">
        
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

button
{
	padding: 10px 20px;
    background: #ff4500;
    border: none;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
}
</style>

<?php include 'inc/footer.php'; ?>
