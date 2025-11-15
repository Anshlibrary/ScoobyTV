<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../conn.php';

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../conn.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
if (!isset($_SESSION['session_id'])) {
   // $_SESSION['status_messages'][] = "Error: You must log in to create users.";
    header('Location: signout.php');
    exit;
}
$associate_id = $_SESSION['associate_id'];
// Query to fetch associate's full name and credits
$associate_sql = "SELECT fullname, email, credits, username, coupon_code, paid_amt, session_id, isdisabled, user_type FROM Associate WHERE associate_id = ?";
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
$associate_db_session = htmlspecialchars($associate['session_id']);
if ($_SESSION['session_id'] !== $associate_db_session) {
    header('Location: signout.php');
    exit;
}
$status=htmlspecialchars($associate['isdisabled']);
if ($status) {
    // If user is disabled, sign out the user
    header('Location: signout.php');
    exit;
}
$associate_name = htmlspecialchars($associate['fullname']);
$assoc_email = htmlspecialchars($associate['email']);
$coupon_code = htmlspecialchars($associate['coupon_code']);
$assoc_credits = htmlspecialchars($associate['credits']);
$assoc_username = htmlspecialchars($associate['username']);
$assoc_user_type = htmlspecialchars($associate['user_type']);
$assoc_first_name = explode(' ', $associate_name)[0];
$paid_amt = htmlspecialchars($associate['paid_amt']);

if ($assoc_user_type !== 'super_user'){
 header('Location: dashboard.php');
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email1']) && isset($_POST['password1'])) {
        // Handle email and password submission
        $email = filter_var($_POST['email1'], FILTER_VALIDATE_EMAIL);
if (!$email) {
    $_SESSION['status_messages'][] = "Invalid email format.";
    exit;
}
        $password = password_hash($_POST['password1'], PASSWORD_BCRYPT);
        $mobileno = isset($_POST['mobile1']) && !empty(trim($_POST['mobile1'])) ? trim($_POST['mobile1']) : 'NA';
        $credits =0;
        $full_name = $_POST['fullname1'];
        // Extract first and last name from full name
        $name_parts = explode(' ', $full_name);
        $first_name = $name_parts[0];
        $coupon_code_user = $coupon_code;
        $paid_amt_user=$paid_amt;
        $first_username = ucfirst(strtolower($first_name));
        $username = "Assoc" . $first_username;

// Check if the username exists in the Associate table
$checkUsernameSql = "SELECT COUNT(*) FROM Associate WHERE username = ?";
$stmt = $conn->prepare($checkUsernameSql);
$counter = 1; // Start counter for uniqueness

// Loop until a unique username is found
while (true) {
    // Prepare the current username with the counter if necessary
    $finalUsername = ($counter > 1) ? $username . $counter : $username;
    
    $stmt->bind_param('s', $finalUsername);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    
    if ($count == 0) {
        $username=$finalUsername;
        // Username is unique, break the loop
        break;
    } else {
        // Username exists, increment the counter and try again
        $counter++;
    }
}

// Now $finalUsername holds the unique username
$stmt->close();

// You can now use $finalUsername for further operations

        $user_type="client_user";
        // Check if email already exists in the database
        $stmt = $conn->prepare("SELECT associate_id FROM Associate WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['status_messages'][] = "<span style='color:#ff4b2b;'>Email already exists.</span> ";
        } 
        else 
        {
   $stmt = $conn->prepare("INSERT INTO Associate (email, password, fullname, mobile, coupon_code, credits, paid_amt, username, user_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
   $stmt->bind_param("sssssddss", $email, $password, $full_name, $mobileno, $coupon_code_user, $credits, $paid_amt_user, $username, $user_type);
   if ($stmt->execute()) {
                 $_SESSION['status_messages'][] = "$email created successfully";
                 header('Location: clients.php');
                exit;
   } 
   else { $_SESSION['status_messages'][] = "<span style='color:red;font-weight:bold;'>Error creating account: </span> " . $stmt->error; 
}
        }
    } 
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailDisable']) && isset($_POST['currentStatus'])) {
$emailDisable = filter_var($_POST['emailDisable'], FILTER_SANITIZE_EMAIL);
 $currentStatus = filter_var($_POST['currentStatus'], FILTER_SANITIZE_NUMBER_INT);

 if (empty($coupon_code)) {
    $_SESSION['status_messages'][] = "Error fetching clients";
    exit;
}
                $sql = "SELECT associate_id FROM Associate 
                        WHERE coupon_code = ? and username != ? and email = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
                    exit;
                }
                // Bind the txn_id pattern parameter
                $stmt->bind_param('sss', $coupon_code, $assoc_username , $emailDisable );
                if (!$stmt->execute()) {
                    $_SESSION['status_messages'][] = "Database error: (" . $stmt->errno . ") " . $stmt->error;
                    exit;}
                $result = $stmt->get_result();
                $srno=1;
                if ($result->num_rows > 0) {
                     $newUserStatus = ($currentStatus == 1) ? 0 : 1;
    // SQL query to disable the user based on email
    $sql = "UPDATE Associate SET isdisabled = ? WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('is', $newUserStatus, $emailDisable);
        if ($stmt->execute()) {
             $statusMessage = ($newUserStatus == 1) ? "$emailDisable - disabled successfully." : "$emailDisable - enabled successfully.";
            $_SESSION['status_messages'][] = $statusMessage;
        } else {
            $_SESSION['status_messages'][] = "Error updating user status.";
        }
        $stmt->close();
    }
    $conn->close();
    // Redirect back to the user management page
    header("Location: clients.php");
    exit();
    }
    else{
           $_SESSION['status_messages'][] = "Failed to disable unknown user";
           header("Location: clients.php");
    exit;
    }
}


 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['emailTransfer']) && isset($_POST['TransferCredits'])) {
     
$emailTransfer = filter_var($_POST['emailTransfer'], FILTER_SANITIZE_EMAIL);
 $TransferCredits = filter_var($_POST['TransferCredits'], FILTER_SANITIZE_NUMBER_INT);

  if($TransferCredits<=0){
 $_SESSION['status_messages'][] = "Transfer credits must be greator than 0";
           header("Location: clients.php");
    exit();
 }
 if($assoc_credits < $TransferCredits){
 $_SESSION['status_messages'][] = "Not enough credits to transfer. Please recharge your account.";
           header("Location: clients.php");
    exit();
 }
 if (empty($coupon_code)) {
    $_SESSION['status_messages'][] = "Error fetching clients";
    exit;
}
                $sql = "SELECT associate_id FROM Associate 
                        WHERE coupon_code = ? and username != ? and email = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
                    exit;
                }
                // Bind the txn_id pattern parameter
                $stmt->bind_param('sss', $coupon_code, $assoc_username, $emailTransfer );
                if (!$stmt->execute()) {
                    $_SESSION['status_messages'][] = "Database error: (" . $stmt->errno . ") " . $stmt->error;
                    exit;}
                $result = $stmt->get_result();
                $srno=1;
                if ($result->num_rows > 0) {

    // First, retrieve the current credits for the user based on their email
$sql = "SELECT credits, isdisabled FROM Associate WHERE email = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param('s', $emailTransfer);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentCredits = $row['credits'];  // Get the current credits
            $user_isdisabled = $row['isdisabled'];
            if($user_isdisabled){
 $_SESSION['status_messages'][] = "Cannot Transfer credits to Locked/Disabled User. Please Unlock $emailTransfer to transfer credits ";
           header("Location: clients.php");
    exit();
 }
            // Calculate the new credits by adding TransferCredits to the currentCredits
            $newCredits = $currentCredits + $TransferCredits;
            // Now, update the credits in the database
            $updateSql = "UPDATE Associate SET credits = ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            if ($updateStmt) {
                $updateStmt->bind_param('ds', $newCredits, $emailTransfer);
                if ($updateStmt->execute()) {
                     
                      // Start a transaction
$conn->begin_transaction();

try {
    // Deduct associate credits
    $NewAssocCredits = $assoc_credits - $TransferCredits;

    $deductSql = "UPDATE Associate SET credits = ? WHERE username = ?";
    $deductStmt = $conn->prepare($deductSql);
    
    if ($deductStmt) {
        // Assuming credits is an integer, using 'is' for integer and string
        $deductStmt->bind_param('is', $NewAssocCredits, $assoc_username);

        if ($deductStmt->execute()) {
            // Success message
            $_SESSION['status_messages'][] = "$TransferCredits credits deducted successfully from your account and transferred to: $emailTransfer";
            $currentDate = date('F j, Y');
            // Send OTP via email using PHPMailer
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
                $mail->addAddress($assoc_email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Credit Transfer Successfull';
                $mail->Body = "
 
   <!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333333;
        }
        .container {
            width: 85%;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #dddddd;
        }
        .header {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .msg {
            font-size: 15px;
            color: #555555;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            font-size: 14px;
            color: #777777;
            margin-top: 30px;
        }
        
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
        </div>
        <p style='color: #222;'>Hi $assoc_first_name, <br>$TransferCredits credits has been transferred from your ScoobyTV Associate account to $emailTransfer. Please find the detailed description below:</p>
        <div class='msg'>
            From: {$associate_name}'s Account <br>
            To: $emailTransfer <br>
            On Date: $currentDate <br>
            Deducted Credits: $TransferCredits <br>
            Available Credits: $NewAssocCredits <br>
            
        </div>
        <div> If it is not done by you. Please change your associate account password as soon as possible and contact us at scoobytv49@gmail.com with details.
        </div>
        <div class='footer'>
            Best regards,<br>
            The ScoobyTV Team
        </div>
    </div>
</body>
</html>
      
";
                $mail->send();
                $message = "<span style='color:#5E62DE;font-weight:bold;'>Transaction has been sent to Email id -</span> <br>" . $email;
                
            } catch (Exception $e) {
                $message = "<span style='color:red;font-weight:bold;'>Email Message could not be sent.</span> Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            throw new Exception("Error deducting credits: " . $deductStmt->error);
        }

        $deductStmt->close();
    } else {
        throw new Exception("Error preparing deduction statement: " . $conn->error);
    }

    // If no error, commit the transaction
    $conn->commit();

} catch (Exception $e) {
    // An error occurred, rollback the transaction
    $conn->rollback();
    $_SESSION['status_messages'][] = "Transaction failed: " . $e->getMessage();
}
// deduction end here
    
                } else {
                    $_SESSION['status_messages'][] = "Error updating credits.";
                }
                $updateStmt->close();
            }
        } else {
            $_SESSION['status_messages'][] = "User not found.";
        }
    } else {
        $_SESSION['status_messages'][] = "Error retrieving current credits.";
    }
    $stmt->close();
} else {
    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
}

$conn->close();

// Redirect back to the user management page
header("Location: clients.php");
exit();

    }
    else{
           $_SESSION['status_messages'][] = "Failed to update unknown user";
           header("Location: clients.php");
    exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['passwordInput']) && isset($_POST['emailEdit'])) {
    // Sanitize and hash the input
    $emailEdit = filter_var($_POST['emailEdit'], FILTER_SANITIZE_EMAIL);
    $passwordInput = password_hash($_POST['passwordInput'], PASSWORD_BCRYPT);
 if (empty($coupon_code)) {
    $_SESSION['status_messages'][] = "Error fetching clients";
    exit;
}
                $sql = "SELECT associate_id FROM Associate 
                        WHERE coupon_code = ? and username != ? and email = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
                    exit;
                }
                // Bind the txn_id pattern parameter
                $stmt->bind_param('sss', $coupon_code, $assoc_username , $emailEdit );
                if (!$stmt->execute()) {
                    $_SESSION['status_messages'][] = "Database error: (" . $stmt->errno . ") " . $stmt->error;
                    exit;}
                $result = $stmt->get_result();
                $srno=1;
                if ($result->num_rows > 0) {
                        // Prepare the SQL update statement
    $updateSql = "UPDATE Associate SET password = ? WHERE email = ?";
    $updateStmt = $conn->prepare($updateSql);

    if ($updateStmt) {
        // Bind the parameters
        $updateStmt->bind_param('ss', $passwordInput, $emailEdit);

        // Execute the statement
        if ($updateStmt->execute()) {
            // Check if any row was affected
            if ($updateStmt->affected_rows > 0) {
                // Success message
                $_SESSION['status_messages'][] = "Password updated successfully for $emailEdit.";
            } else {
                // No rows affected (possibly email does not exist)
                $_SESSION['status_messages'][] = "Failed to update: No account found with the provided email.";
            }
        } else {
            // Error executing the statement
            $_SESSION['status_messages'][] = "Error updating password: " . $conn->error;
        }

        // Close the statement
        $updateStmt->close();
    } else {
        // Error preparing the statement
        $_SESSION['status_messages'][] = "Error preparing statement: " . $conn->error;
    }

    // Redirect to clients.php
    header("Location: clients.php");
    exit();
    }
    else{
           $_SESSION['status_messages'][] = "Failed to update unknown user";
           header("Location: clients.php");
    exit();
    }

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
    display:none !important;
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
            border-collapse: collapse;
            margin: 20px 0;
        }
    table th {
        background-color: #222;
        color: #fff;
text-align: center;
    }
    table td {
        background-color: #343a40;
       text-align: center;

    }
        table th, table td {
            /* border: 1px solid #ddd; */
            padding: 5px 10px;
 
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

  .clickable-icon {
    cursor: pointer;
  }
.bg-credits{
    background: linear-gradient(250deg, yellow, green);border-radius: 20px;
}
/* Custom scrollbar for WebKit browsers (Chrome, Safari, etc.) */
#showUserDetail::-webkit-scrollbar {
    width: 5px; /* Set the scrollbar width (you can make it thinner if needed) */
}

#showUserDetail::-webkit-scrollbar-thumb {
  background-color: #f79489;
  border-radius: 2px;
  border: 2px solid #f79489;
}

#showUserDetail::-webkit-scrollbar-thumb:hover {
    background-color: #f79489; /* Color when hovering over the scrollbar */
}

#showUserDetail::-webkit-scrollbar-track {
   background: #ebcac7;
}

/* For Firefox */
#showUserDetail {
    scrollbar-width: thin; /* Thin scrollbar */
    scrollbar-color: #888 #f1f1f1; /* Scrollbar color (thumb and track) */
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
 @media (min-width: 975px) {
        .custom-width {
            width: 20%;
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
      
      <a href="" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="dashboard.php">Home</a></li>
          <li><a href="clients.php">Clients</a></li>
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
      <h2 style="font-size: 18px;">Admin- <?php echo $assoc_first_name; ?></h2>
      <a href="#" class="logo"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto " href="dashboard.php">Dashboard</a></li>
          <li><a class="nav-link scrollto active" href="">Clients</a></li>
          <li><a class="nav-link scrollto" href="renew_users.php">Renew users</a></li>
           <li><a class="nav-link scrollto" href="cancel_users.php">Refund</a></li>
          <li><a class="nav-link scrollto" href="tutorial.php">Tutorials</a></li>
          <li><a class="nav-link scrollto " href="pricing.php">Pricing</a></li>
          <li><a class="nav-link scrollto" href="faq.php">FAQ's</a></li>
          <li>
           <a class="getstarted scrollto signin_mobile" href="profile.php">Credits :&nbsp;&nbsp; <b style="color:#09f109;"> <?php echo htmlspecialchars($associate['credits']); ?></b></a>     
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav>
      <!-- .navbar -->
    </div>
  </header>
  <!-- End Header -->

  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;">Associate Clients</h2>
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
       
       <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" role="form" class="my-email-form" id="loginForm">
         <div class="col-lg-3 col-md-3 col-12">
        <input type="hidden" class="form-control" name="updatedby" id="updatedby" value="<?php echo htmlspecialchars($associate_id); ?>">
      </div>
  <div id="inputContainer">
    <!-- Initial input fields with serial number -->
    <div class="form-group mt-3 row d-flex align-items-center flex-wrap justify-content-center" style="gap: 0px; flex-wrap: nowrap;">
      <div class="srno-circle d-flex justify-content-center align-items-center" style="width:40px;height:40px;border-radius:50%;background-color:#007bff;color:white;">
        1
      </div>
     <div class="col-lg-3 col-md-3 col-12 mb-1">
    <input type="text" class="form-control" name="fullname1" id="fullname1" placeholder="Full name" required oninput="this.value = this.value.trimStart()" autocomplete="new-password">
</div>
    <div class="col-lg-3 col-md-3 col-12 mb-1">
    <input type="email" class="form-control" name="email1" id="email1" placeholder="Email" required oninput="this.value = this.value.trimStart()" autocomplete="nope">
</div>
<div class="col-lg-3 col-md-3 col-12 mb-1">
    <input type="password" class="form-control" name="password1" id="password1" placeholder="Password" minlength="8" required oninput="this.value = this.value.trimStart()" autocomplete="new-password">
</div>
<script>
document.getElementById("email1").addEventListener("blur", function() {
    this.value = this.value.trim();
});

document.getElementById("password1").addEventListener("blur", function() {
    this.value = this.value.trim();
});
</script>

      <div class="col-lg-3 col-md-3 col-12 mb-1 custom-width">
         <input type="tel" class="form-control" name="mobile1" id="mobile1" placeholder="Mobile no (optional)"  minlength="10" maxlength="10" 
           pattern="[6-9]{1}[0-9]{9}" 
           title="Please enter a valid 10 digits indian number">
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-buy hidden" id="hiddenSubmit">Add Clients</button>
  
<p class="text-center" style=""></p>

</form>


 <div class="btn-wrap text-center mt-3">
    <button class="btn btn-buy" id="SubmitBtn" onclick="CreateUser()">Add Client</button>
  </div>
</div>
</div>
</div>
</div>
</div>

<script>
let userCount = 1;
   
  function CreateUser() {

       var form = document.querySelector('form'); // Select the form
    if (!form.reportValidity()) { // Trigger validation and check if form is valid
        var button = document.getElementById("SubmitBtn");
        button.disabled = false; // Re-enable the button if validation fails
        button.classList.remove("loading"); // Remove loading state
        button.innerText = "Add Client"; // Restore original text
        return; // Stop if form is invalid
    }
    
    var button = document.getElementById("SubmitBtn");
            button.disabled = true;
            button.classList.add("loading");
          button.innerText = "Please wait...";
             // Disable the input fields (username, password, mobile, email)
  
    // Trigger the click event of the hidden submit button
    document.getElementById('hiddenSubmit').click();
  }
</script>

<h3 style="margin-bottom:0;" class="mt-3">All Created Clients:</h3>
      <div class="row" style="background: linear-gradient(to bottom right, #716d6d, #141414); border-radius:20px;padding:10px;">
<div class="col-lg-12 mb-3 table-responsive" >
        <table id="userTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Owner</th>
                     <th>Status</th>
                     <th>Credits</th>
                     <th>Users</th>
                     <th>Last Login</th>
                      <th>Actions</th>
                </tr>
            </thead>
        <tbody>    
<?php
 if (empty($coupon_code)) {
    $_SESSION['status_messages'][] = "Error fetching clients";
    exit;
}
                // Query to fetch user details based on txn_id pattern
                $sql = "SELECT associate_id, username, email, isdisabled, credits, last_login FROM Associate 
                        WHERE coupon_code = ? and username != ? ORDER BY associate_id DESC";

                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
                    exit;
                }

                // Bind the txn_id pattern parameter
                $stmt->bind_param('ss', $coupon_code,$assoc_username );
                if (!$stmt->execute()) {
                    $_SESSION['status_messages'][] = "Database error: (" . $stmt->errno . ") " . $stmt->error;
                    exit;
                }

                $result = $stmt->get_result();
                $srno=1;

                if ($result->num_rows > 0) {
                    while ($user = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td class="user-id-cell"><?php echo htmlspecialchars($srno); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($associate_name); ?></td>
                            <td style="color: <?php echo (strpos($user['isdisabled'], '0') !== false) ? '#00ff00' : '#ff4b2b'; ?>"><?php
    echo ($user['isdisabled'] == 0) ? "Active" : "Disabled";
?></td>
                            <td> <div class="bg-credits"><?php echo htmlspecialchars($user['credits']); ?></div> </td>
                             <td class="d-flex justify-content-center align-items-center">
                              <div class="srno-circle d-flex justify-content-center align-items-center clickable-icon" style="width:40px;height:40px;border-radius:50%;background-color:#007bff;color:white;" data-toggle="modal" data-target="#SeeUserModal"  data-email="<?php echo htmlspecialchars($user['email']); ?>">
                              <?php 
         $assoc_user = $user['username'];
        $countQuery = "SELECT COUNT(*) AS user_count FROM users WHERE txn_id = ?";
        
        $countStmt = $conn->prepare($countQuery);
        $countStmt->bind_param('s', $assoc_user);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
        $userCount = $countRow['user_count']; // Get the count of users with the same email
        echo $userCount;
        ?>
      </div>  </td>
                            <td> <?php 
$last_login = $user['last_login']; // Fetch last_login from the user data
echo htmlspecialchars($last_login !== null ? $last_login : "Never"); 
?></td>
                            <td>
                            <div class="d-flex justify-content-center align-items-center" style="gap:20px;"> 
<i class="fa-solid fa-share-from-square clickable-icon" title="Transfer Credits" data-toggle="modal" data-target="#TransferModal"  data-email="<?php echo htmlspecialchars($user['email']); ?>"></i>
<!-- Edit (pencil) icon -->
<i class="fas fa-edit clickable-icon"  title="Edit user" data-toggle="modal" data-target="#EditModal"  data-email="<?php echo htmlspecialchars($user['email']); ?>"></i>
<i class="fas fa-lock clickable-icon" style="color: <?php echo (strpos($user['isdisabled'], '0') !== false) ? '#00ff00' : '#ff4b2b'; ?>"  title="Lock/disable User" data-toggle="modal" data-target="#LockModal"  data-email="<?php echo htmlspecialchars($user['email']); ?>" data-isdisabled="<?php echo htmlspecialchars($user['isdisabled']); ?>"></i>
                            <!-- Close (cross) icon
<i class="fas fa-times clickable-icon"  title="Delete user"></i> -->
</div>
     </td>
                 </tr>
                        
                        <?php
                        $srno++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No results found.</td></tr>";
                }

                // Close statement and connection
                $stmt->close();
                $conn->close();
                ?>

   </tbody>
        </table>
    </div>
</div>

</div>
    </div>
    
  </section>
</section>


<!-- Modal 1 -->

<div class="modal fade" id="TransferModal" tabindex="-1" role="dialog" aria-labelledby="TransferModalLabel" aria-hidden="true" style="top: 30%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #343a40;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="text-align: end;">Close</button>
            <div class="modal-body text-center">
                <p class=""> </p>
                <div class="mb-3 d-flex justify-content-center">
                  
                    <form id="TransferForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" id="emailTransferInput" name="emailTransfer" value="">
                        <input type="number" class="form-control" name="TransferCredits" id="TransferCredits" placeholder="Credits" min="1" max="100" step="1" value="10" required>
                        <button type="submit" id="TransferBtnhidden" class="btn btn-sign" style="margin-left: 20px;display:none;"></button>
                    </form>
                    <button type="submit" id="TransferBtn" class="btn btn-sign" style="margin-left: 20px;" onclick="TransferCredits()">Transfer Credit</button></br>
                      
                </div>
                </div>
            
        </div>
    </div>
</div>

<!-- Modal -->

<!-- Modal 2 -->

<div class="modal fade" id="LockModal" tabindex="-1" role="dialog" aria-labelledby="LockModalLabel" aria-hidden="true" style="top: 30%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #343a40;">
            <div class="modal-body text-center">
                <p class=""> </p>
                <div class="mb-3 d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 20px;">Close</button>
                    <form id="disableForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" id="emailDisableInput" name="emailDisable" value="">
                        <input type="hidden" id="statusInput" name="currentStatus" value="">
                        <button type="submit" id="disableBtn" class="btn btn-sign" style="margin-left: 20px;display:none;">Disable</button>
                    </form>
                    <button type="submit" id="LockBtn" class="btn btn-sign" style="margin-left: 20px;" onclick="lockuser()">Disable</button>
                </div>
                </div>
            
        </div>
    </div>
</div>

<!-- Modal -->

<!-- Modal 3 -->

<div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="EditModalLabel" aria-hidden="true" style="top: 30%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #343a40;">
            <div class="modal-body text-center">
                <p class=""> </p>
                <div class="mb-3">
                    
                    <form id="Editform" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" role="form" class="" style="margin-bottom: 20px;">
                    <div class="col-lg-12 col-md-12 col-12 mb-1">
    <input type="password" class="form-control" name="passwordInput" id="passwordInput" placeholder="New Password" minlength="8" required oninput="checkPasswordLength()" autocomplete="new-password">
</div>
                        <input type="hidden" id="emailEditInput" name="emailEdit" value="">
                       
                        <button type="submit" id="SaveBtnhidden" class="btn btn-sign" style="margin-left: 20px;display:none;"></button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 20px;">Close</button>
                    <button type="submit" id="SaveBtn" class="btn btn-sign" style="margin-left: 20px;padding: 8px 10px;" onclick="saveuser()">Save</button>
                </div>
                </div>
            
        </div>
    </div>
</div>

<!-- Modal -->

 <!-- Modal 4 -->

<div class="modal fade" id="SeeUserModal" tabindex="-1" role="dialog" aria-labelledby="SeeUserModalLabel" aria-hidden="true" style="top: 20%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="background-color: #343a40;">
         <button type="button" class="btn btn-secondary" data-dismiss="modal" style="text-align: right;">Close</button>
            <div class="modal-body text-center">
                <div class="d-flex" style="flex-wrap: nowrap;justify-content: center;align-items: flex-start;">
                    <form id="SeeUserform" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" role="form" class="" style="margin-bottom: 5px;">
                         <div class="col-lg-12 col-md-12 col-12 mb-1">
                                 <input type="email" class="form-control" name="SeeUserEmail" id="SeeUserEmail" required>
                            </div>
                            <button type="submit" id="SeeUserBtnhidden" class="btn btn-sign" style="display:none;"></button>
                    </form>
                    <button type="submit" id="SeeUserBtn" class="btn btn-primary" style="margin-left: 20px;" onclick="">See Users</button>
                </div>
                <div class="loading hidden" id="loadingdiv"></div>
                <div class="mb-3 table-responsive hidden" id="showUserDetail" style="max-height: 300px; overflow-y: auto;">

                 <table id="SeeUserTable">
            <thead>
                <tr>
                    <th>Sr.</th>
                    <th>Username</th>
                    <th>Created</th>
                    <th>Plan Expiry</th>
                     <th>Validity</th>
                </tr>
            </thead>
        <tbody>    

   </tbody>
        </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal -->

  </main><!-- End #main -->
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
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
  function lockuser(){
 var button = document.getElementById("LockBtn");
            button.disabled = true;
            button.classList.add("loading");
          button.innerText = "Please wait...";
             // Disable the input fields (username, password, mobile, email)
  
    // Trigger the click event of the hidden submit button
    document.getElementById('disableBtn').click();
  }
   function TransferCredits(){

          var Editform = document.querySelector('#TransferForm'); // Select the form
    if (!Editform.reportValidity()) { // Trigger validation and check if form is valid
        var button = document.getElementById("TransferBtn");
        button.disabled = false; // Re-enable the button if validation fails
        button.classList.remove("loading"); // Remove loading state
        button.innerText = "Transfer Credits"; // Restore original text
        return; // Stop if form is invalid
    }
 var button = document.getElementById("TransferBtn");
            button.disabled = true;
            button.classList.add("loading");
          button.innerText = "Please wait...";
             // Disable the input fields (username, password, mobile, email)
  
    // Trigger the click event of the hidden submit button
    document.getElementById('TransferBtnhidden').click();
  }
function checkPasswordLength() {
    var passwordInput = document.getElementById('passwordInput');
    var saveButton = document.getElementById('SaveBtn');
    
    // Trim leading and trailing spaces
    var trimmedPassword = passwordInput.value.trim();
    
    // Set the input field's value to the trimmed version
    passwordInput.value = trimmedPassword;
    
    // Check if password is at least 8 characters long (after trimming spaces)
    if (trimmedPassword.length >= 8) {
        saveButton.disabled = false; // Enable Save button
    } else {
        saveButton.disabled = true;  // Disable Save button
    }
}
   function saveuser(){
       var Editform = document.querySelector('#Editform'); // Select the form
    if (!Editform.reportValidity()) { // Trigger validation and check if form is valid
        var button = document.getElementById("SaveBtn");
        button.disabled = false; // Re-enable the button if validation fails
        button.classList.remove("loading"); // Remove loading state
        button.innerText = "Save"; // Restore original text
        return; // Stop if form is invalid
    }
 var button = document.getElementById("SaveBtn");
            button.disabled = true;
            button.classList.add("loading");
          button.innerText = "Please wait...";
             // Disable the input fields (username, password, mobile, email)
  
    // Trigger the click event of the hidden submit button
    document.getElementById('SaveBtnhidden').click();
  }
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
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>
<script>
$(document).ready(function() {
    $('#SeeUserBtn').click(function(e) {
        e.preventDefault(); // Prevent default form submission
        var email = $('#SeeUserEmail').val(); // Get the email value
        if (email === "") {
            alert("Please enter an email!");
            return false;
        }
         // Show the loading spinner and hide the user details table
        $('#loadingdiv').removeClass('hidden');

        $.ajax({
            url: 'fetch_users.php', // The PHP file where the query is processed
            type: 'POST',
            data: {email: email}, // Pass the email as data
            dataType: 'json', // Expect JSON response from the server
            success: function(response) {
                // Clear any existing rows in the table
                $('#SeeUserTable tbody').empty();
                 $('#loadingdiv').addClass('hidden'); 
                if (response.length > 0) {
                    // Show the table when users are found
                    $('#showUserDetail').removeClass('hidden'); 
                    // Loop through each user and append a row to the table
                    $.each(response, function(index, user) {
                        var newRow = `<tr>
                            <td>${index + 1}</td>
                            <td>${user.username}</td>
                            <td>${user.created}</td>
                            <td>${user.plan_expiry}</td>
                            <td>${user.plan_valid_for} month</td>
                        </tr>`;
                        $('#SeeUserTable tbody').append(newRow);
                    });
                } else {
                   // Show the table with a message if no users are found
                    $('#showUserDetail').removeClass('hidden'); 
                    $('#SeeUserTable tbody').append('<tr><td colspan="6">No users found for this email.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error: " + status + error);
                 $('#loadingdiv').addClass('hidden'); // Hide the spinner in case of error
                alert("An error occurred while fetching the users.");
            }
        });
    });
});


$(document).ready(function() {
    // When the modal is triggered by the lock icon
    $('#TransferModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var email = button.data('email'); // Extract email from data-* attributes
        var modal = $(this);
            modal.find('.modal-body p').html('Transfer Credits to<br><b>Email: ' + email + '</b><br>'); 
        // Set the hidden input values in the form
        modal.find('#emailTransferInput').val(email); // Set email
    });
});
</script>

<script>
$(document).ready(function() {
    // When the modal is triggered by the lock icon
    $('#LockModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var email = button.data('email'); // Extract email from data-* attributes
        var isDisabled = button.data('isdisabled'); // Extract isdisabled status
        var modal = $(this);
         var disableBtn = modal.find('#LockBtn'); // Button element inside the modal

        // Update modal content based on the user's current status
        if (isDisabled == 1) {
            modal.find('.modal-body p').html('Enable user?<br><b>Email: ' + email + '</b><br>This user will be able to log in and transact.');
            disableBtn.text('Enable'); // Change button text to 'Enable'
             disableBtn.removeClass('btn-sign').addClass('btn-buy'); 
        } else {
            modal.find('.modal-body p').html('Disable user?<br><b>Email: ' + email + '</b><br>This user won\'t be able to log in or transact.');
            disableBtn.text('Disable'); // Change button text to 'Disable'
            disableBtn.removeClass('btn-buy').addClass('btn-sign'); 
        }
        // Set the hidden input values in the form
        modal.find('#emailDisableInput').val(email); // Set email
        modal.find('#statusInput').val(isDisabled); // Set current isdisabled status
    });
});
</script>

<script>
$(document).ready(function() {
    // When the modal is triggered by the lock icon
    $('#EditModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var email = button.data('email'); // Extract email from data-* attributes
        var modal = $(this);
            modal.find('.modal-body p').html('Email: ' + email + '</b><br>');
        // Set the hidden input values in the form
        modal.find('#emailEditInput').val(email);
    });
});
</script>

<script>
$(document).ready(function() {
    // When the modal is triggered by the lock icon
    $('#SeeUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var email = button.data('email'); // Extract email from data-* attributes
        var modal = $(this);
        // Set the hidden input values in the form
        modal.find('#SeeUserEmail').val(email);
    });
});
</script>
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>