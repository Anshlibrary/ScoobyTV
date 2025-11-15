<?php
include('../conn.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

// Get the form data
$username=$_POST['username'];
$password=$_POST['password'];
$user_id = $_POST['user_id'];
$updated_by = $_POST['updated_by'];
$purchase = $_POST['purchase'];
$allocated_ip = $_POST['allocated_ip'];
$plan_amount = $_POST['plan_amount'];
$plan_valid_for = $_POST['plan_valid_for'];
$reason = $_POST['reason'];
$email = $_POST['useremail'];

// Check if reason is empty and set purchase status accordingly
if (empty($reason)) {
    $purchase = "Successfull & Verified";
} else {
    $purchase = $reason;
}

// Calculate the plan_expiry date
$current_date = new DateTime();
$plan_expiry = $current_date->add(new DateInterval('P' . $plan_valid_for . 'M'))->format('Y-m-d');

// Set no_of_devices based on plan_amount
if ($plan_amount == 69) {
    $no_of_devices = 2;
} else {
    $no_of_devices = 1;
}

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
            $mail->Body = "Dear User,<br><br>Your plan has been activated successfully.<br><br>To see the details, please visit ScoobyTv -> Profile.<br><br>Regards,<br>The ScoobyTV Team";
        } else {
            $mail->Body = "Dear User,<br><br>Your plan activation failed. Reason: $purchase.<br><br>To see the details, please visit ScoobyTv -> Profile.<br><br>Regards,<br>The ScoobyTV Team";
        }

        $mail->send();
        echo 'and Email has been sent';
        header("Refresh: 1; url=BabuaPanel.php"); 
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    echo "Error updating record: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
