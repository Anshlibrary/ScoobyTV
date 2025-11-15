<?php
session_start();
require_once 'conn.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$couponCode = $data['couponCode'];

// Rate limiting settings
$maxAttempts = 20;
$attemptWindow = 3600; // in seconds (1 hour)

if (!isset($_SESSION['coupon_attempts'])) {
    $_SESSION['coupon_attempts'] = 0;
    $_SESSION['first_attempt_time'] = time();
}

$currentTime = time();
$timeSinceFirstAttempt = $currentTime - $_SESSION['first_attempt_time'];

if ($timeSinceFirstAttempt > $attemptWindow) {
    // Reset the attempts counter and first attempt time if the window has passed
    $_SESSION['coupon_attempts'] = 0;
    $_SESSION['first_attempt_time'] = $currentTime;
}

if ($_SESSION['coupon_attempts'] >= $maxAttempts) {
    echo json_encode(['success' => false, 'message' => 'Maximum attempts reached. Please try again later.']);
    exit();
}

// Check if coupon exists and is valid
$stmt = $conn->prepare("SELECT * FROM Coupons WHERE code = ? AND expiry_date >= CURDATE()");
$stmt->bind_param("s", $couponCode);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $coupon = $result->fetch_assoc();
    echo json_encode(['success' => true, 'discount' => $coupon['discount'], 'message' => 'Coupon applied successfully.']);
    $_SESSION['coupon_attempts'] = 0; // Reset attempts on successful application
} else {
    $_SESSION['coupon_attempts'] += 1;
    echo json_encode(['success' => false, 'message' => 'Invalid or expired coupon code.']);
}

$stmt->close();
$conn->close();
?>

