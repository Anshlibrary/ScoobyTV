<?php
session_start();
require '../conn.php';
// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {    header('Location: Babualogin.php');   exit;}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
// Get the form data
$user_id = $_POST['user_id'];
$updated_by = $_POST['updated_by'];
$purchase = $_POST['purchase'];
$allocated_ip = $_POST['allocated_ip'];
$plan_amount = 0;
$plan_valid_for = 0;
$reason = $_POST['reason'];
$email = $_POST['useremail'];

// Check if reason is empty and set purchase status accordingly
if (empty($reason)) {
    $purchase = "Successfull & Verified";
} else {
    $purchase = $reason;
}

// Function to calculate date after a specified number of days
function calculateNextDays($days) {
    $current_date = new DateTime(); // Current date
    $current_date->add(new DateInterval('P' . $days . 'D')); // Add specified number of days
    return $current_date->format('Y-m-d'); // Format date as 'Y-m-d'
}
// Example: Calculate next 4 days date
$days = 1;
$plan_expiry = calculateNextDays($days);

// Set no_of_devices based on plan_amount
$no_of_devices = 1;


// Prepare the SQL query
$sql = "UPDATE users SET 
    updated_by = ?, 
    purchase = ?, 
    allocated_ip = ?, 
    plan_amount = ?, 
    plan_valid_for = ?, 
    plan_expiry = ?, 
    no_of_devices = ? 
    WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssdssi", $updated_by, $purchase, $allocated_ip, $plan_amount, $plan_valid_for, $plan_expiry, $no_of_devices, $user_id);

// Execute the query
if ($stmt->execute()) {
    echo "updated successfully";

    // Send email notification to the user
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'scoobytv49@gmail.com'; // Your Gmail address
        $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password or App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Plan Status';
        if ($purchase == "Successfull & Verified") {
            $mail->Body = "Dear User,<br><br>Your plan has been activated successfully.<br><br>Regards,<br>The ScoobyTV Team";
        } else {
            $mail->Body = "Dear User,<br><br>Your plan activation failed. Reason: $purchase.<br><br>To see the details, please visit <a href='https://scoobytv.com/profile.php'>Profile</a>.<br><br>Regards,<br>The ScoobyTV Team";
        }

        $mail->send();
        echo 'and Email has been sent';
        header("Refresh: 1; url=Babua_trial.php"); 
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    echo "Error updating record: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
 
}
$admin_id = $_SESSION['admin_id'];
// Query to fetch admin's first name
$admin_sql = "SELECT first_name, username FROM admins WHERE id = ?";
$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->bind_param('i', $admin_id);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();

if ($admin_result->num_rows === 0) {
    echo "<h2 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>Admin not found</h2>";
    exit;
}

$admin = $admin_result->fetch_assoc();
$admin_name = htmlspecialchars($admin['username']);

// Modify the SQL query to filter for successful purchases
$sql = "SELECT id, full_name, email, password, modified, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username,txn_id
        FROM users 
        WHERE purchase = 'Successfull' AND txn_id = '3_day_trial'";

$stmt = $conn->prepare($sql);

// Execute the query
$stmt->execute();
$result = $stmt->get_result();
$payrequests='';
// Check if any user found
if ($result->num_rows === 0) {
    $payrequests= "<h4 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>No new Requests</h4>";
    //exit;
}
?>


<!-- HTML or further processing of $user data -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
     body {
        font-family: Arial, sans-serif;
        background-color: #141414;
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
    }
    /*Custom slider css*/
    .blog-header{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 5px 0;}
    .blog-body{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 20px 0;    }
    .breadcrumbs{margin-top:0;    }

    .logo-img{    max-height: 40px;    }
    .section-bg{
        background: linear-gradient(85deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);
    }
    .btn-sign{
    background: linear-gradient(to right, #ff416c, #ff4b2b);
    display: inline-block;
    padding: 5px 5px;
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}
.btn-sign:hover{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
color: #fff;
}
.btn-buy{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
    display: inline-block;
    padding: 5px 10px;
    margin-left:20px;
    border-radius: 4px;
    color: #fff;
    transition: none;
    font-size: 15px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
}

  .btn-buy:hover{
        background: linear-gradient(42deg, #1e1289 0%, #2c0656 100%);color: #fff;
}
.hidden{
    display:none;
}
.my-email-form {
    box-shadow: 0 0 30px rgba(214, 215, 216, 0.1);
    padding: 5px 10px;
    background: #fff;
    border-radius: 10px;
}
section{
  padding: 10px 0;
}
.contact .my-email-form input {
        padding: 10px 15px;
    }
    .contact .my-email-form input, .contact .my-email-form textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
    }
.contact .my-email-form .sent-message {
    display: none;
    color: #fff;
    background: #18d26e;
    text-align: center;
    padding: 15px;
    font-weight: 600;
}
.contact .my-email-form .error-message {
    display: none;
    color: #fff;
    background: #ed3c0d;
    text-align: left;
    padding: 15px;
    font-weight: 600;
}
.contact .my-email-form .loading:before {
    content: "";
    display: inline-block;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    margin: 0 10px -6px 0;
    border: 3px solid #18d26e;
    border-top-color: #eee;
    animation: animate-loading 1s linear infinite;
}
@media (min-width: 992px) {
  .box {
    max-width: 400px; /* Adjust the maximum width of the boxes */
    margin: 0 auto; /* Center align the boxes */
  }
}
@media (max-width: 768px) {
    .my-breadcrumb-list {
      display: flex !important;
      justify-content: flex-end;
      align-items: center;
      gap:20px;
    }
  }
</style>
</head>

<body> 
  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="../index.php">Home</a></li>
           <li><a href="services.php">Services</a></li>
          <li>AdminPanel</li>
        </ol>
        <a href="../signout.php" class="btn btn-sign" style="">Sign out</a>
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <div class="container">
   <h2 style="margin-left:auto;margin-right: auto;padding-left:10px ;font-size: 18px;">Admin-<?php echo "$admin_name"; ?> </h2>
   
  </div>
  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;">Free Trial requests</h2>
         <?php echo $payrequests; ?>
      </div>
      <div class="row">
      <?php 
// Print all user data in a loop
while ($user = $result->fetch_assoc()) { ?>
        <div class="col-lg-4 mb-3">
          <form action="" method="post" role="form" class="my-email-form">
            <div class="row">
            
              <div class="col-md-12 form-group mt-1 text-center">
                <p style="color: #1fff1f;margin-top: 10px;font-weight: bold;background-color: var(--bs-secondary-bg);
    opacity: 1;border-radius: 5px;margin-bottom: 0;">Userid - <?php echo htmlspecialchars($user['id']); ?></p>
              
              </div>
              <div class="col-md-12 form-group mt-1">
                <input type="email" class="form-control" name="" id="" value="<?php echo htmlspecialchars($user['full_name']); ?>" disabled>
              </div>
               <div class="col-md-12 form-group mt-1">
                <input type="email" class="form-control" name="" id="" value="username - <?php echo htmlspecialchars($user['username']); ?>" disabled>
              </div>
              
              <div class="col-md-12 form-group mt-1" style="display:flex;">
    <input type="password" class="form-control password-input" name="" id="" value="<?php echo htmlspecialchars($user['password']); ?>" disabled>
    <button class="btn btn-sm btn-outline-primary toggle-password" type="button"><i class="fa fa-eye" id="togglePassword"></i></button>
</div>
              <div class="col-md-12 form-group mt-1">
                <input type="email" class="form-control" name="" id="" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
              </div>
              <div class="col-md-12 form-group mt-1 d-flex">
                <input type="email" class="form-control" name="" id="" value="₹<?php echo htmlspecialchars($user['plan_amount']); ?>" disabled>
                <input type="email" class="form-control" name="" id="" value="<?php echo htmlspecialchars($user['plan_valid_for']); ?> months" disabled>
 

              </div>
              <div class="col-md-12 form-group mt-1">
                  </div>
              <div class="col-md-12 form-group mt-1">
                <input type="email" class="form-control" name="" id="" value="<?php echo htmlspecialchars($user['plan_expiry']); ?>" disabled>
              </div>
              <div class="col-md-12 form-group mt-1 text-center">
                <p style="color: #1fff1f;margin-top: 0px;font-weight: bold;background-color:  #3b3b3b;
    opacity: 1;border-radius: 5px;">UTR - <?php echo htmlspecialchars($user['txn_id']); ?></p>
              </div>
              
            </div>
            
         </form>

          <form action="Babua_trial.php" method="post" role="form" class="my-email-form">
          <div class="form-group mt-3">
              <input type="hidden" class="form-control" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>" required>
            </div>
            <div class="form-group mt-3">
              <input type="hidden" class="form-control" name="updated_by" value="<?php echo "$admin_name"; ?>" required>
            </div>
            <div class="form-group mt-3">
              <input type="hidden" class="form-control" name="purchase" value="Successfull & Verified" required>
            </div>
            <div class="col-md-12 form-group mt-1">
                <input type="hidden" class="form-control" name="useremail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
             <div class="form-group mt-3">
              <input type="hidden" class="form-control" name="allocated_ip" value="http://stv1.xyz:53575/" required>
            </div>
            <div class="form-group mt-3">
              <input type="text" class="form-control" name="reason" id="reject" placeholder="Reject Reason">
            </div>
            <div class="btn-wrap text-center mt-3 mb-3">
              <button type="submit" class="btn btn-buy" id="approveButton" >Approve</button>
              <button type="submit" class="btn btn-sign disabled" style="margin-left: 20px;" id="rejectButton">Reject</button>
            </div>
          </form>
</div><?php } ?>

</div>
    </div>
    
  </section>
</section>
  </main><!-- End #main -->
 

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        const rejectInput = document.getElementById('reject');
        const rejectButton = document.getElementById('rejectButton');
        const approveButton = document.getElementById('approveButton');
        rejectInput.addEventListener('input', function () {
            if (rejectInput.value.trim() !== '') {
                rejectButton.classList.remove('disabled');
                approveButton.classList.add('disabled');
            } else {
                rejectButton.classList.add('disabled');
                 approveButton.classList.remove('disabled');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordBtns = document.querySelectorAll('.toggle-password');

        togglePasswordBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const passwordInput = this.parentNode.querySelector('.password-input');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                   this.innerHTML = '<i class="fa fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    this.innerHTML = '<i class="fa fa-eye"></i>';
                }
            });
        });
    });
</script>

</body>
</html>