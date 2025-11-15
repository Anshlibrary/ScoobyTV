<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Start session
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php"); // Redirect to login page if not logged in
    exit;
}
// Include the database connection file
include 'conn.php';

// Include PHPMailer library
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the logged-in user ID from the session
    $user_id = $_SESSION['user_id'];

    // Get the form data
    $plan = $_POST['plan'];
    $month = $_POST['month'];
    $utrid = $_POST['utrid'] ?? '';
    $utridmobile = $_POST['utridmobile'] ?? '';
    $FinalCouponCode=$_POST['FinalCouponCode']?? 'NA';
    $emailid = $_POST['emailid'] ?? '';
    $purchase = 'Successfull'; // Add this line to get the purchase data
    $plan_expiry = date('Y-m-d H:i:s', strtotime("+$month months"));
    $no_of_devices = 0;

    if ($plan === '99') {
        $no_of_devices = 1; 
    } else {
        $no_of_devices = 2; 
    }

    $utr = ""; // Initialize $utr as an empty string

if (!empty($utrid)) {
    $utr = $utrid;
     $displayUTRText = "<p>UTR ID: $utrid</p>";
} elseif (!empty($utridmobile)) {
    $utr = $utridmobile;
     $displayUTRText = "<p>Remarks: $utridmobile</p>";
}


    // Fetch the email and full name of the user
    $sql_user = "SELECT email, full_name FROM users WHERE id = ?";
    if ($stmt_user = $conn->prepare($sql_user)) {
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $stmt_user->bind_result($email, $full_name);
        $stmt_user->fetch();
        $stmt_user->close();
    } else {
        echo "ERROR: Could not prepare query: $sql_user. " . $conn->error;
        exit;
    }

    // Validate the form data
    if (!empty($plan) && !empty($month) && !empty($utr) && !empty($purchase)) { // Include purchase in validation
        // Prepare the SQL statement to update the logged-in user's record
        $sql = "UPDATE users SET plan_valid_for = ?, plan_amount = ?, txn_id = ?, purchase = ?, plan_expiry = ?, no_of_devices = ? WHERE id = ?"; // Include purchase in SQL query

        if ($stmt = $conn->prepare($sql)) {
            // Bind the variables to the prepared statement as parameters
            $stmt->bind_param("issssii", $month, $plan, $utr, $purchase, $plan_expiry, $no_of_devices, $user_id); // Bind purchase parameter

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Send email notification
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'scoobytv49@gmail.com'; // Your Gmail address
                    $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV'); // Your email and name
                    $mail->addAddress('yoitv278@gmail.com');
                    //$mail->addAddress('manish.dwivedi.am99@gmail.com');
                    $mail->addAddress($email); // Send email to the user as well

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Plan Purchased at ScoobyTV';
                    $mail->Body = "
                        <h2>Plan Purchase</h2>
                        <p style='color:#1fff1f;'>Thank you for your payment. We're verifying it now. Once completed, your account will be activated. We'll notify you via email. You can also check the status in your profile.</p>
                        <p>Full Name: $full_name</p>
                        <p>Email: $email</p>
                        <p>Plan: $plan</p>
                        <p>Months: $month</p>
                        $displayUTRText
                        <p>Purchase Status: $purchase</p>
                        <p>Plan Expiry: $plan_expiry</p>
                 
                    ";

                    $mail->send();
                    echo 'Successfull...';

                    // Redirect to a success page or display a success message
                    header("Refresh: 0; url=success.html");
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                echo "ERROR: Could not execute query: $sql. " . $conn->error;
            }
        } else {
            echo "ERROR: Could not prepare query: $sql. " . $conn->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "ERROR: All fields are required.";
    }
}

// Close connection
$conn->close();
?>
