<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}
include 'conn.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $plan = $_POST['plan'];
    $month = $_POST['month'];
    $paid_amt = $_POST['paid_amt'];
    $utrid = $_POST['utrid'] ?? '';
    $utridmobile = $_POST['utridmobile'] ?? '';
    $FinalCouponCode = $_POST['FinalCouponCode'];
    $emailid = $_POST['emailid'] ?? '';
    $purchase = 'Successfull';
    $plan_expiry = date('Y-m-d H:i:s', strtotime("+$month months"));
    $no_of_devices = ($plan === '99') ? 1 : 2;
    $utr = !empty($utrid) ? $utrid : $utridmobile;
    $displayUTRText = !empty($utrid) ? "<p>UTR ID: $utrid</p>" : "<p>Remarks: $utridmobile</p>";

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

    if (!empty($plan) && !empty($month) && !empty($utr) && !empty($purchase)) {
        $sql = "UPDATE users SET plan_valid_for = ?, plan_amount = ?, txn_id = ?, purchase = ?, plan_expiry = ?, no_of_devices = ?, paid_amt=? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("issssidi", $month, $plan, $utr, $purchase, $plan_expiry, $no_of_devices, $paid_amt, $user_id );
            if ($stmt->execute()) {
                if ($FinalCouponCode !== "NA") {
                    $sql_coupon = "INSERT INTO Coupon_Usage (user_id, coupon_code, used_at, order_id) VALUES (?, ?, NOW(), ?)";
                    if ($stmt_coupon = $conn->prepare($sql_coupon)) {
                        $stmt_coupon->bind_param("iss", $user_id, $FinalCouponCode, $utr);
                        $stmt_coupon->execute();
                        $stmt_coupon->close();
                    } else {
                        echo "ERROR: Could not prepare query: $sql_coupon. " . $conn->error;
                        exit;
                    }
                }

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'scoobytv49@gmail.com';
                    $mail->Password = 'pnrejfrmudnytlss'; // Your Gmail password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('scoobytv49@gmail.com', 'ScoobyTV');
                    $mail->addAddress('yoitv278@gmail.com');
                    $mail->addAddress($email);

                    $couponMessage = $FinalCouponCode !== "NA" ? $FinalCouponCode : "NA";
                    $mail->isHTML(true);
                    $mail->Subject = 'Plan Purchased at ScoobyTV';
                    $mail->Body = "
                        <h2>Plan Purchase</h2>
                        <p style='color:#1fff1f;'>Thank you for your payment. We're verifying it now. Once completed, your account will be activated. We'll notify you via email. You can also check the status in your profile.</p>
                        <p>Full Name: $full_name</p>
                        <p>Email: $email</p>
                        <p>Plan: $plan</p>
                        <p>Months: $month</p>
                        <p>Paid Amt: $paid_amt</p>
                        $displayUTRText
                        <p>Purchase Status: $purchase</p>
                        <p>Plan Expiry: $plan_expiry</p>
                        <p>Coupon Code Used: $couponMessage</p>
                    ";

                    $mail->send();
                    echo 'Successfull...';
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
        $stmt->close();
    } else {
        echo "ERROR: All fields are required.";
    }
}
$conn->close();
?>
