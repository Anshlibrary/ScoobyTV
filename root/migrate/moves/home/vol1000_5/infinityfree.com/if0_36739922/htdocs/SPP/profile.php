<?php
session_start();
require '../conn.php';

// Initialize status messages array if not set
if (!isset($_SESSION['status_messages'])) {
    $_SESSION['status_messages'] = [];
}

// Check if associate is logged in
if (!isset($_SESSION['associate_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: login.php');
    exit;
}

$associate_id = $_SESSION['associate_id'];

// Query to fetch associate's full name and credits

$associate_sql = "SELECT fullname, email, mobile, state, country, payment_option, bank_name, account_number, ifsc_code, coupon_code, upi_id, created_at, updated_at, credits, paid_amt, username, user_type FROM Associate WHERE associate_id = ?";
$associate_stmt = $conn->prepare($associate_sql);
if (!$associate_stmt) {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
    exit;
}
$associate_stmt->bind_param('i', $associate_id);
if (!$associate_stmt->execute()) {
    $_SESSION['status_messages'][] = "Database error: (" . $associate_stmt->errno . ") " . $associate_stmt->error;
    exit;
}
$associate_result = $associate_stmt->get_result();

if ($associate_result->num_rows === 0) {
    $_SESSION['status_messages'][] = "Associate not found.";
    exit;
}

$associate = $associate_result->fetch_assoc(); 
$associate_name = htmlspecialchars($associate['fullname']);
$user_type = htmlspecialchars($associate['user_type']);
$coupon_code = htmlspecialchars($associate['coupon_code']);
$first_name = explode(' ', $associate_name)[0];
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

  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
.loading:before {
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
 .process-container {
            margin: 0 auto;
            padding: 20px;
            background-color: #dfbddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: linear-gradient(to bottom right, #716d6d, #141414);
        }
          .status-message {
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid green;
            background-color: #337333;
        }
        
.process-step h3, .process-step p {
            color: #141414;
        }
        .process-step {
            margin-bottom: 20px;
        }
        .process-step i {
            font-size: 24px;
            color: #007bff;
            margin-right: 15px;
        }
        .process-step h3 {
            margin: 0;
            font-size: 20px;
            color: #007bff;
        }
        .process-step p {
            margin: 5px 0 0 39px;
        }
        /* Add this style for responsive table */
        /* Table styles */      
 table {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        border-collapse: separate;
    }
  table th, .profile-details table td {
        padding: 10px;
        text-align: left;
    }
    table th {
        background-color: #222;
        color: #fff;
    }
     table td {
        background-color: #343a40;
    }

  .profile-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .profile-details {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .password-container {
        display: flex;
        align-items: center;
    }
    
    .password-container input {
        background: none;
        border: none;
        color: #fff;
        padding: 0;
        margin: 0;
    }
    
    .password-container i {
        cursor: pointer;
        margin-left: 10px;
    }
     .dataTables_wrapper .dataTables_paginate .paginate_button {
    color: #fff !important; /* Change text color for pagination buttons */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.next {
    color: #fff !important; /* Ensure the "Next" button text is white */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.previous {
    color: #fff !important; /* Ensure the "Previous" button text is white */
}

.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label {
    color: #fff; /* Change text color for search and length labels */
}

.dataTables_wrapper .dataTables_info {
    color: #fff; /* Change text color for info text */
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #007bff; /* Example background color for active and hover states */
    color: #fff; /* Ensure text is white in these states */
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
  
@media (max-width: 975px) {
  .signin_mobile{
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
  }
}
 @media (max-width: 768px) {
        .profile-details table th, .profile-details table td {
            display: block;
            width: 100%;
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
      
      <a href="dashboard.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="dashboard.php">Home</a></li>
          <li><a href="profile.php">Profile</a></li>
          <li><a href="signout.php" class="btn btn-sign" style="">Sign out</a></li>
        </ol>
        
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <!-- ======= Header ======= -->
  <header id="header" style="padding:0;">
    <div class="container d-flex align-items-center justify-content-between">
      <!--<h1 class="logo"><a href="index.html"></a></h1> Text logo -->
      <h2 style="font-size: 18px;"><a href="dashboard.php" class="logo"><i class="fa fa-arrow-left"></i></a></h2>
    </div>
  </header><!-- End Header -->


  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;">Associate Profile</h2>
        <div class="my-3">
<?php
if (isset($_SESSION['status_messages']) && count($_SESSION['status_messages']) > 0) {
    foreach ($_SESSION['status_messages'] as $message) {
        echo "<div class='status-message'>$message</div>";
    }
    // Clear messages after displaying
    $_SESSION['status_messages'] = [];
}
 ?>
 </div>
      </div>
         
<div class="row">
      <div class="col-lg-12 mb-3">
      <div class="process-container">
    <div class="process-step">
       <div>
        <div class="profile-container" data-aos="fade-up">
        
        <div class="profile-header mb-1">
          <img src="../assets/img/profile-user.png" alt="User Picture" class="profile-picture">
       
          <div class="section-title pb-1">
          <h2 style="margin-bottom:0;padding-bottom:2px;"><?php echo htmlspecialchars($associate['fullname']); ?></h2>
        </div>
        </div>
        

        <div class="profile-details mb-3">
          <table>
            <tr >
            <th>Credits:</th>
             <td style="background-color:#868e96;display:flex;align-items: center;" >
            <p style="margin: 0px 0 0 36px;    font-size: 20px;    font-weight: 800;"><?php echo htmlspecialchars($associate['credits']); ?></p> <?php if ($user_type === 'client_user'): ?>
    <a href="#" class="btn btn-buy" onclick="alert('Please contact your administrator to recharge your wallet.'); return false;">Buy Credits</a>
<?php else: ?>
    <a href="pricing.php" class="btn btn-buy">Buy Credits</a>
<?php endif; ?>

            </td>
</tr>
<?php if ($user_type !== 'client_user'): ?>
    <tr class="">
              <th>Credit Value </th>
              <td><b style="margin-left:35px;"> 1 credit - ₹<?php echo htmlspecialchars($associate['paid_amt']); ?></b>
              </td>
            </tr>
            
<?php endif; ?>
               <tr>
              <th>Email</th>
              <td style="background-color:#868e96;"><?php echo htmlspecialchars($associate['email']); ?></td>
            </tr>
            
              <tr>
              <th>Full Name</th>
              <td><?php echo htmlspecialchars($associate['fullname']); ?></td>
            </tr>
            <tr class="">
              <th>Phone</th>
              <td><?php echo htmlspecialchars($associate['mobile']); ?></td>
            </tr>
             <tr class="">
              <th>Region</th>
              <td><?php $state = htmlspecialchars($associate['state']);
if ($state !== 'NA') {
    echo $state . ", ";
}?> <?php echo htmlspecialchars($associate['country']); ?></td>
            </tr>
<?php if ($user_type !== 'client_user'): ?>
             <tr class="">
              <th>Payment Methods</th>
              <td>
              Bank:<br>
             <?php
// Initialize flags for availability
$bank_details_available = !empty($associate['bank_name']) && !empty($associate['account_number']) && !empty($associate['ifsc_code']);
$upi_available = !empty($associate['upi_id']);
?>

<!-- Display Bank Details -->
<?php if ($bank_details_available): ?>
    Bank name - <?php echo $associate['bank_name']; ?> <br>
    Acc. no - <?php echo htmlspecialchars($associate['account_number']); ?> <br>
    IFSC - <?php echo htmlspecialchars($associate['ifsc_code']); ?><br>
<?php endif; ?>

<!-- Display UPI ID -->
<?php if ($upi_available): ?>
    UPI ID - <?php echo htmlspecialchars($associate['upi_id']); ?>
<?php endif; ?>

              </td>
            </tr>
<?php endif; ?>
             <tr class="">
              <th>Created at</th>
              <td><?php echo htmlspecialchars($associate['created_at']); ?></td>
            </tr>

<?php if ($user_type === 'super_user'): ?>
<tr>
              <th>Invite code</th>
              <td style="background-color:#868e96;display:flex;" >
              
              <input type="text" class="form-control" id="invitecopy" placeholder="" value="<?php echo htmlspecialchars($coupon_code); ?>" disabled style="width:80%;margin-right:20px;">
                         <a type="button" class="btn btn-outline-secondary" id="copyButton" onclick="copyInvite()" style="background: linear-gradient(42deg, #4a47619c 0%, #241f29 100%);">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-copy" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M4 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1v-1h1v1a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1v1z"/>
</svg>
                        </a>
                       
              </td>
            </tr>
             <script>
    function copyInvite() {
      var copyText = document.getElementById("invitecopy");
      copyText.disabled = false; // Enable the input field to select the text
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices
      document.execCommand("copy");
      copyText.disabled = true; // Disable the input field again
      alert("Copied: " + copyText.value);
    }
    </script>
<?php endif; ?>

             <tr>
              <th>Default Months</th>
              <td>
  <div class="form-group d-flex" style="gap:10px;">
    <select id="subscription" name="subscription" class="form-control">
      <option value="1">1 month</option>
      <option value="2">2 month</option>
    </select>
    <a href="#" id="saveBtn" class="btn btn-sign">Save</a>
  </div>
</td>

            </tr>

          </table>
        </div>
         <div class="profile-details">
          <table>
            <tr>
            <th>Request Content:</th>
             <td style="background-color:#868e96;display:flex;align-items: center;">
               <p style="color:#fff;margin:0;">You will need a Jellyfin ID to Request content.</p>
               <a href="http://stv1.xyz:53575" class="btn btn-buy">open</a>
            </td>
</tr>
<tr>
              <th>Jellyfin ID</th>
              <td style="background-color:#868e96;display:flex;justify-content: center;" >
                         <a href="https://scoobytv.com/trial.php" class="btn btn-outline-secondary" style="background: linear-gradient(42deg, #4a47619c 0%, #241f29 100%);color:#fff;" target="_blank">
                          Get Jellyfin ID
                        </a>
                       
              </td>
            </tr>
            
          </table>
          </div>
      
  <div class="btn-wrap text-center mt-3 cust_feedback" ><a href="../feedback.php">Submit you feedback</a></div> 
      </div>
</div>
</div>
</div>
</div>
</div>

 <script>
  // On page load, check if a value is saved in localStorage and set it as the default selected option
  document.addEventListener("DOMContentLoaded", function() {
    var savedValue = localStorage.getItem('subscription');
    
    if (savedValue) {
      document.getElementById('subscription').value = savedValue;
    }
  });

  // Save the selected value in localStorage when the "Save" button is clicked
  document.getElementById('saveBtn').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default action (if any)

    var selectedValue = document.getElementById('subscription').value;
    localStorage.setItem('subscription', selectedValue);

    alert('Subscription choice saved!');
  });
</script>
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('fa-eye-slash');
    });
  </script>

  <script>
    function copyUPI() {
      var copyText = document.getElementById("usernamecopy");
      copyText.disabled = false; // Enable the input field to select the text
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices
      document.execCommand("copy");
      copyText.disabled = true; // Disable the input field again
      alert("Copied: " + copyText.value);
    }
    </script>


      <div class="row" style="background: linear-gradient(to bottom right, #716d6d, #141414); border-radius:20px;padding:10px;">
<div class="col-lg-12 mb-3 table-responsive" >                     
                
    </div>
</div>

</div>
    </div>
    
  </section>
</section>
  </main><!-- End #main -->
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

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="../assets/vendor/aos/aos.js"></script>
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="../assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>