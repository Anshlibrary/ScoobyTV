
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../conn.php';

 $price=0;

// Check if the 'price' parameter exists in the URL
if (isset($_GET['price'])) {
    // Sanitize and store the 'price' input in a variable
    $price = filter_input(INPUT_GET, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $credits = filter_input(INPUT_GET, 'credits', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Convert both to integers
    $price = intval($price);
    $credits = intval($credits);

    // Check if credits is valid to avoid division by zero
    if ($credits === 10 || $credits === 20 || $credits === 50) {
        $valid_calc = $price / $credits; // Perform division

        // Optional: Check if the price or credits value is invalid
        if ($price <= 690 || $credits <= 9 || $valid_calc <= 69) {
            die("Invalid price value or credits value:");
        }
    } else {
        die("Invalid price value or credits value :(");
    }
}


// Get the user ID from the session
$associate_id = $_SESSION['associate_id'];

// Retrieve the email from the database
$stmt = $conn->prepare("SELECT email FROM Associate WHERE associate_id = ?");
$stmt->bind_param("i", $associate_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

if (!isset($_SESSION['associate_id'])) {
    header("Location: login.php");
    exit;
}
include '../conn.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $utrid = $_POST['utrid'];
    $credits = $_POST['credits'];
    $paid_amt = $_POST['paid_amt'];
    
    if (!empty($utrid) && !empty($credits) && !empty($paid_amt)) {
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
                    $mail->addAddress('scoobytv49@gmail.com');
                    $mail->addAddress($email);

                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Credits Purchased Status';
                    $mail->Body = "
                        <h2>Credits Purchase</h2>
                        <p style='color:#1fff1f;'>Thank you for your payment. We're verifying your payemnt now. Once completed, your wallet will be updated. We'll notify you via email. You can also check the status in your profile.</p>
                       
                        <p>Email: $email</p>
                        <p>Paid Amt: ₹ $paid_amt</p>
                        <p>Credits Purchased: $credits</p>
                        <p>Transaction/Utrid:  $utrid</p>
                    ";

                    $mail->send();
                    echo 'Successfull...';
                     header("Location: success.html");
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
                 
    } else {
        echo "ERROR: All fields are required.";
    }
}
$conn->close();
?>

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
        background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);
        margin: 0;
        padding: 0;
        height: 100vh;
        color: #fff;
        /* overflow: hidden; */
    }
  
.btn-buy{
background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
     
    display: inline-block;
    padding: 10px 70px;
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

  background: linear-gradient(to right, #ff416c, #ff4b2b);
  color: #fff;
}
.btn-coupon{
background: linear-gradient(42deg, #f94646 0%, #7b27d8 100%); 
     
    display: inline-block;
    padding: 0px 12px !important;
    border-radius: 20px !important;
    color: #fff;
    transition: none;
    font-size: 12px;
    font-weight: 400;
    font-family: "Roboto", sans-serif;
    font-weight: 600;
    transition: 0.3s;
    border:none;
}
.btn-coupon:hover{
  background: linear-gradient(to right, #ff416c, #ff4b2b);
  color: #fff;
}
.hidden{
  display: none;
}
.qr-img{
max-width: 100px;
}
.contact .my-email-form {
        /* box-shadow: 0 0 30px rgba(214, 215, 216, 0.6); */
        padding: 30px 20px;
        background: #fff;
    }
    .contact .my-email-form input {
        padding: 7.6px 15px;
    }
    .contact .my-email-form input, .contact .php-email-form textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
    }
    .centered-img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  max-width: 60%;
  height: auto;
}
form label{
color:#141414;
text-align: center;
margin-bottom: 10px;;
}
form h3{
color:#141414;
text-align: center;
}

.input-group {
  display: flex;
  align-items: center;
}

.input-group .form-control {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.input-group .btn {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  padding: 0.375rem 0.75rem;
}

.bi-clipboard {
  margin: 0;
}
.text-pay{
  color: #5846f9f7;
  font-weight: bold;
  margin-bottom: 0;
}
.success-color{
  color: #126312;
}

   .payment-option {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
                justify-content: space-around;
        }
        .payment-option img {
            width: 100px;
            height: 30px;
            margin-right: 10px;
        }
        .payment-option span {
            font-size: 1.2em;
            margin-right: 10px;
            color:#212529;
        }
        .tick {
            display: none;
            color: green;
            font-size: 1.5em;

        }
        .selected {
            background-color: #e6ffe6;
            border-color: green;
        }
        .selected .tick {
            display: inline;
        }
/* Default to hidden */
.laptop-only {
  display: none;
}

/* Show only on laptop screens (e.g., min-width: 1024px and up) */
@media (min-width: 1024px) {
  .laptop-only {
    display: block;
  }
}
@media (min-width: 1024px) and (max-width: 1440px) {
  .hide-on-laptop {
    display: none;
  }
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
.fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
        }
        .fade-in {
            opacity: 0;
            transition: opacity 0.5s ease-in;
        }
        .visible {
            opacity: 1;
        }
  @media (min-width: 950px) {
  section {
    padding: 100px 450px;
  }
}
</style>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-R8VL44KX1F"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-R8VL44KX1F');
</script>
<!--    Analytics End here-->
<body>

 
  <main id="main">
  <!-- ======= paySection ======= -->
    
    <section class="contact" style="margin-top: -60px;background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);" >
      <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="col-lg-12" style="padding-right: calc(var(--bs-gutter-x)* 0.1);
    padding-left: calc(var(--bs-gutter-x)* .1);">
              <form action="payment_v3.php" method="post" role="form" class="" style="padding: 25px 15px;" id="paymentutr">

                <div class="row">
                  <div  id="contact" class="" style="padding: 0;">
                    <div class="my-email-form" style="border-radius: 10px;" id="scanqrupi">
                      <div class="section-title" style="padding-bottom: 5px;">
                        <h2 style="margin-bottom: 0;padding-bottom: 3px;" id="payment-info" class="success-color hidden"></h2>
                         <h2 style="margin-bottom: 0;padding-bottom: 3px;" id="payment-info" class="success-color">Pay ₹<?php echo $price;?></h2>
                         <input type="hidden" class="form-control mt-1" name="paid_amt" id="paid_amt" value="<?php echo $price;?>" required>
                      </div>
                     
                    
                      <div class="form-group mt-3 text-center">
                          <label for="plan">Scan QR and PAY</label>
                          <img src="../assets/img/pay/phonepeqr.webp" alt="logo" class="centered-img">
                          <h3 style="margin-bottom: 0;">OR</h3>
                        </div>
                        <div class="form-group mt-3">
                            <div class="input-group">
                          <input type="text" class="form-control" id="upi" value="Q008853812@ybl" disabled>
                                <button type="button" class="btn btn-outline-secondary" id="copyButton" onclick="copyUPI()" style="background: linear-gradient(42deg, #5846f9d9 0%, #7b27d8 100%);">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" stroke="currentColor" stroke-width="2" class="bi bi-clipboard" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 115.77 122.88" style="enable-background:new 0 0 115.77 122.88" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02 v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02 c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1 c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7 h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02 c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65 v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02 h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02 v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/></g></svg>
                            </button>

                            </div>
                        </div>


                        
                        <div class="form-group mt-3 text-center mb-3">

                         
                        <div class="">

                            <div class="input-group text-center " style="justify-content: center;">
                                <small style="color: #141414;margin-top: 10px;margin-bottom: 10px;font-weight: 500;    font-size: 12px;">After successfull payment, click Next.</small>
                        
                              </div>
                                 <div class="btn-wrap text-center">
                                <a class="btn btn-buy" onclick="utr()">Next</a>
                              </div>
                        </div>
                       
                           
                          
                          
                        </div>
                    </div>
                   <!-- Only utr -->
              <div class="my-email-form hidden" id="fillutr">
                  <div class="form-group mt-3">
                  <p style="color:#212529;font-weight: 600;text-align: center;margin-bottom: 5px;">Submit your UTR or txn ID</p> 
                  <div class="text-center"><a href="#" class="" data-toggle="modal" data-target="#upiModal">where to find utr/txn id?</a> </div>
                  <input type="tel" class="form-control mt-1" name="utrid" id="utrid" placeholder="last 5 digits only or all digits">
                   <input type="hidden" class="form-control mt-1" name="credits" id="credits" value="<?php echo $credits; ?>" required>
                <div class="btn-wrap text-center mt-3" >
                  <button type="submit" class="btn btn-buy" id="verifyutr">Submit</a>
                </div>
                 <div class="text-center mt-3">
              <small style="color: #141414;margin-top: 10px;margin-bottom: 10px;font-weight: 500;font-size: 12px;">If payment failed for any reason, <a href="">click here</a> to pay again.</small>
             </div>
               </div>
                
            </div>
            <!--End here-->

           
            </div>
              </form>
   </div>
  </div>
        </div>
        

      </div>
    </section>
  

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("paymentutr").addEventListener("submit", function(event) {
                var button = document.getElementById("verifyutr");
                button.disabled = true;
                button.classList.add("loading");
         
            });
        });
        </script>


  <div class="modal fade" id="upiModal" tabindex="-1" role="dialog" aria-labelledby="upiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="upiModalLabel" >How to Find UTR and TXN ID</h5>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 20px;">Close</button>
            </div>
            <div class="modal-body">
                <p class="text-dark">Here are some examples of UPI payments:-</p>
                <div class="mb-3">
                     <p class="text-center text-dark">PhonePay</p>
                    <img src="../assets/img/pay/image2.jpg" class="img-fluid" alt="UTR Example 2">
                   
                </div>
                <div class="mb-3">
                    <p class="text-center text-dark">GooglePay</p>
                    <img src="../assets/img/pay/image1.jpg" class="img-fluid" alt="UTR Example 1">
                    
                </div>
                
                <div class="mb-3">
                    
                    <p class="text-center text-dark">Paytm</p>
                    <img src="../assets/img/pay/image3.jpg" class="img-fluid" alt="TRN Example">
                    
                </div>
                <div class="mb-3">
                    
                    <p class="text-center text-dark">AmazonPay</p>
                    <img src="../assets/img/pay/image4.jpg" class="img-fluid" alt="TRN Example">
                    
                </div>
            </div>
            
        </div>
    </div>
</div>


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
  <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


  <script>
    function copyUPI() {
      var copyText = document.getElementById("upi");
      copyText.disabled = false; // Enable the input field to select the text
      copyText.select();
      copyText.setSelectionRange(0, 99999); // For mobile devices
      document.execCommand("copy");
      copyText.disabled = true; // Disable the input field again
      alert("Copied: " + copyText.value);
    }
    </script>
  
<script>
   var CouponApplyBtn = document.getElementById("CouponApplyBtn");
   var couponLink = document.getElementById('couponLink');
     var paid_amt = document.getElementById('paid_amt');
   var couponCodeInput = document.getElementById('FinalCouponCode');
   var RealcouponCode = document.getElementById('RealCouponCode');
   
    document.getElementById('couponForm').addEventListener('submit', function(event) {
    CouponApplyBtn.disabled = true;
     CouponApplyBtn.textContent = "";
    CouponApplyBtn.classList.add("loading");
        event.preventDefault();
        applyCoupon(RealcouponCode.value);
    });



    function applyCoupon(couponCodeReceived) {
        fetch('/apply-coupon.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ couponCode: couponCodeReceived }),
        })
        .then(response => response.json())
        .then(data => {
            var messageElement = document.getElementById('couponMessage');
            if (data.success) {
                console.log('Coupon applied:', data);
                updatePayableAmount(data.discount);
                $('#couponModal').modal('hide');
                messageElement.textContent = 'Coupon applied! Saved ₹' + data.discount;
                messageElement.style.color = 'green';
                couponCodeInput.value=couponCodeReceived;
                
 CouponApplyBtn.disabled = false;
     CouponApplyBtn.textContent = "Apply";
    CouponApplyBtn.classList.remove("loading");

         
                couponLink.textContent = 'Coupon applied! Saved ₹' + data.discount;
                 couponLink.classList.remove("btn");
                couponLink.classList.remove("btn-coupon");
                couponLink.style.color = 'green';
                couponLink.style.pointerEvents = 'none';
            } else {
                console.error('Coupon not applied:', data.message);
                messageElement.textContent = data.message;
                messageElement.style.color = 'red';
                updatePayableAmount(0);
                couponCodeInput.value="NA";

                 couponLink.textContent = 'Apply Coupon';
                 couponLink.style.color = 'white';
                 couponLink.classList.add("btn");
                couponLink.classList.add("btn-coupon");
                couponLink.style.pointerEvents = 'auto';

                 CouponApplyBtn.disabled = false;
     CouponApplyBtn.textContent = "Apply";
    CouponApplyBtn.classList.remove("loading");

            }
        })
        .catch((error) => {
            console.error('Error:', error);
            var messageElement = document.getElementById('couponMessage');
            messageElement.textContent = 'An error occurred. Please try again.';
            messageElement.style.color = 'red';
            updatePayableAmount(0);
            couponCodeInput.value="NA";
             couponLink.textContent = 'Apply Coupon';
             couponLink.style.color = 'white';
                 couponLink.classList.add("btn");
                couponLink.classList.add("btn-coupon");
                couponLink.style.pointerEvents = 'auto';

             CouponApplyBtn.disabled = false;
     CouponApplyBtn.textContent = "Apply";
    CouponApplyBtn.classList.remove("loading");
        });
    }

    
</script>

   <script>
    var amt = 99;
    var showamt = 99;
    var paymentInfo = document.getElementById('payment-info');
   
     var paymentInfo2 = document.getElementById('payment-info2');
    var planSelector = document.getElementById('plan');
    var monthSelector = document.getElementById('month');
    var paymentConfirmationDiv = document.getElementById('contact');


    function updatePayableAmount(discount) {
        var selectedValue = parseInt(planSelector.value);
        var selectedMonth = parseInt(monthSelector.value);
        amt = selectedValue * selectedMonth;
        var payableAmount = amt - discount;
        paymentInfo.textContent = 'Pay ₹' + payableAmount;
        paymentInfo2.textContent = 'Payable - ₹' + payableAmount;
         paid_amt.value=payableAmount;
        showamt = payableAmount;
    }
updatePayableAmount(0);
    function handlePaymentClick() {
        var planSelectorDiv = document.getElementById('plan-selector');
        
        // Hide the current div with a fade-out effect
        planSelectorDiv.classList.add('fade-out');
            planSelectorDiv.classList.add('hidden');
            planSelectorDiv.classList.remove('fade-out');
            paymentConfirmationDiv.classList.remove('hidden');
            paymentConfirmationDiv.classList.add('fade-in', 'visible');
    }

    document.getElementById('pay-button').addEventListener('click', handlePaymentClick);
 planSelector.addEventListener('change', function() {
        if (couponLink.innerHTML !== '<img src="assets/img/coupon.png" style="height:25px;width:25px;"> Apply Coupon') {
    couponCodeInput.value = "NA";
    couponLink.innerHTML = '<img src="assets/img/coupon.png" style="height:25px;width:25px;"> Apply Coupon';
    couponLink.style.color = 'white';
    couponLink.classList.add("btn");
    couponLink.classList.add("btn-coupon");
    couponLink.style.pointerEvents = 'auto';
}
    updatePayableAmount(0);
});

monthSelector.addEventListener('change', function() {
   if (couponLink.innerHTML !== '<img src="assets/img/coupon.png" style="height:25px;width:25px;"> Apply Coupon') {
    couponCodeInput.value = "NA";
    couponLink.innerHTML = '<img src="assets/img/coupon.png" style="height:25px;width:25px;"> Apply Coupon';
    couponLink.style.color = 'white';
    couponLink.classList.add("btn");
    couponLink.classList.add("btn-coupon");
    couponLink.style.pointerEvents = 'auto';
}
    updatePayableAmount(0);
});


function utr(){
      var fillutr=document.getElementById('fillutr');
      document.getElementById('scanqrupi').classList.add('hidden');
      fillutr.classList.remove('hidden');
    }
  </script> 

 

  
</body>
</html>