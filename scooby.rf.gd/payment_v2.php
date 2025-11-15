
<?php
session_start();
require_once 'conn.php';

// Function to generate a unique UTR ID
function generateUniqueUTR($conn) {
    do {
        // Generate a random 12-digit number starting with '4'
        $utr = '4' . str_pad(rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
        
        // Check if this UTR already exists in the database
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE txn_id = ?");
        $stmt->bind_param("s", $utr);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
    } while ($count > 0);
    
    return $utr;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Retrieve the email from the database
$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email);
$stmt->fetch();
$stmt->close();

// Generate a unique UTR ID
$utr_id = generateUniqueUTR($conn);
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
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
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
    padding: 10px 35px;
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
        padding: 10px 15px;
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

<body>

 
  <main id="main">
  <!-- ======= paySection ======= -->
    
    <section class="contact" style="margin-top: -60px;background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);" >
      <div class="container" data-aos="fade-up">
        <div class="row">
            <div class="col-lg-12" style="padding-right: calc(var(--bs-gutter-x)* 0.1);
    padding-left: calc(var(--bs-gutter-x)* .1);">
              <form action="payment_process_v2.php" method="post" role="form" class="" style="padding: 25px 15px;" id="paymentutr">

                <div class="row">
                  <div class="" id="plan-selector">
                    <div class="section-title">
                      <h2>Confirm plan</h2>
                      <p>click to change</p>
                    </div>
                   
                    <div class="form-group mt-3">
                      <select id="plan" name="plan" class="form-control">
                           <option value="119">Premium - ₹119</option>
                            <option value="99">Starter - ₹99</option>
                            
                        </select>
                    </div> 
                    <div class="form-group mt-3">
                      <select id="month" name="month" class="form-control">
                          <option value="1">Subscription for 1 month</option>
                          <option value="2">Subscription for 2 month</option>
                          <option value="3">Subscription for 3 month</option>
                      </select>
                    </div>
                    <div class="btn-wrap text-center mt-3" >
                      <a href="#" class="btn-buy" id="pay-button">Pay</a>
                    </div>
                  </div>
                 

                  <div  id="contact" class="hidden" style="padding: 0;">
                    <div class="my-email-form" style="border-radius: 10px;" id="scanqrupi">
                      <div class="section-title" style="padding-bottom: 5px;">
                        <h2 style="margin-bottom: 0;padding-bottom: 3px;" id="payment-info" class="success-color"></h2>
                      </div>
                      <div class="text-center">
                        <p class="text-pay" style="font-weight: 500;">Pay to below Payment options-</p>
                      </div>
                    
                      <div class="form-group mt-3 text-center laptop-only">
                          <label for="plan">Scan QR and PAY</label>
                          <img src="assets/img/pay/paytmqr.webp" alt="logo" class="centered-img">
                          <h3 style="margin-bottom: 0; " class="laptop-only">OR</h3>
                        </div>
                        <div class="form-group mt-3 laptop-only">
                            <div class="input-group">
                          <input type="text" class="form-control" id="upi" value="scoobytv@upi" disabled>
                               
                            </div>
                        </div>


                        
                        <div class="form-group mt-3 text-center mb-3">

                          <!-- payment options -->
                        <div class="hide-on-laptop">
                            <div id="paytm" class="payment-option" onclick="selectPayment('paytm')">
                                <img src="assets/img/pay/paytm.png" alt="Paytm Logo" style="width:80px;height:40px;">
                                <span>Paytm</span>
                                <span class="tick">✔</span>
                            </div>
                            <div id="gpay" class="payment-option" onclick="selectPayment('gpay')">
                                <img src="assets/img/pay/google-pay.png" alt="Google Pay Logo" style="width:50px;height:50px;">
                                <span>GooglePay</span>
                                <span class="tick">✔</span>
                            </div>
                     <div id="phonepay" class="payment-option" onclick="selectPayment('phonepay')">
                                <img src="assets/img/pay/phonepe.png" alt="PhonePay Logo"  style="width:50px;height:50px;" >
                                <span>Phonepe</span>
                                <span class="tick">✔</span>
                            </div>
                             
                              <div id="otherupi" class="payment-option" onclick="selectPayment('otherupi')">
                                <img src="assets/img/pay/upi.png" alt="upi Pay Logo"  style="width:60px;height:30px;" >
                                <span>Other UPI </span>
                                <span class="tick">✔</span>
                            </div>
                    
                            <script>
                            function selectPayment(paymentId) {
                                document.querySelectorAll('.payment-option').forEach(option => {
                                    option.classList.remove('selected');
                                });
                                document.getElementById(paymentId).classList.add('selected');
                            }
                        </script>
                        </div>

                        <!--Apply coupon -->
                        <div class="input-group text-center " style="justify-content: center;">
                             <a href="#" class="" data-toggle="modal" data-target="#couponModal" id="couponLink" style="font-weight: 700;">Apply Coupon</a>
                       </div>
                              <!--Apply coupon end here -->
                       
                        <div class="laptop-only">

                            <div class="input-group text-center " style="justify-content: center;">
                            
                                <small style="color: #141414;margin-top: 10px;margin-bottom: 10px;font-weight: 100;    font-size: 12px;">After successfull payment, click Next.</small>
                        
                              </div>
                                 <div class="btn-wrap text-center">
                                <a class="btn btn-buy" onclick="utr()">Next</a>
                              </div>
                        </div>
                       
                           
                          <div class="hide-on-laptop">
                            
                                 <div class="btn-wrap text-center mt-3">
                                
                                <a class="btn btn-buy" onclick="EmailSubmit()">Pay Now</a>
                              </div>
                              <div class="input-group text-center" style="justify-content: center;">
                                <small style="color: #ff3d00;margin-top: 10px;margin-bottom: 10px;font-weight: 700; font-size: 12px;">*Note: After successfull payment, Please come back to this page and submit your email</small>
                              </div>
                          </div>
                          
                        </div>
                    </div>
                   <!-- Only laptop -->
              <div class="my-email-form hidden" id="fillutr">
                  <div class="form-group mt-3">
                  <p class="" style="color:#212529;font-weight: 600;text-align: center;margin-bottom: 5px;">Submit your UTR or txn ID</p> 
                  <div class="text-center"><a href="#" class="" data-toggle="modal" data-target="#upiModal">where to find utr/txn id?</a> </div>
                  <input type="tel" class="form-control mt-1" name="utrid" id="utrid" placeholder="last 5 digits only or all digits">
                <div class="btn-wrap text-center mt-3" >
                  <button type="submit" class="btn btn-buy" id="verifyutr">Submit</a>
                </div>
                </div>
            </div>
            <!--End here-->

            <!--Mobile-->
            <div class="my-email-form hidden" id="fillemail">
                <div class="form-group mt-3">
                    <p class="" style="color:#212529;font-weight: 600;text-align: center;margin-bottom: 5px;">If Your Payment is successfull</p> 
                <p class="" style="color:#212529;font-weight: 600;text-align: center;margin-bottom: 5px;">Submit your Email ID</p> 
           <input type="hidden" class="form-control mt-1" name="utridmobile" id="utridmobile" value="<?php echo htmlspecialchars($utr_id); ?>" required>
           <input type="email" class="form-control mt-1" name="emailid" id="emailid" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" class="form-control mt-1" name="FinalCouponCode" id="FinalCouponCode" value="NA">

              <div class="btn-wrap text-center mt-3" style="margin-bottom: 15px;">
                <button type="submit" class="btn btn-buy" id="verifyutr2">Submit</a>
              </div>
              <div class="text-center">
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
                var button2 = document.getElementById("verifyutr2");
                button.disabled = true;
                button.classList.add("loading");
                button2.disabled = true;
                button2.classList.add("loading");
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
                    <img src="assets/img/pay/image2.jpg" class="img-fluid" alt="UTR Example 2">
                   
                </div>
                <div class="mb-3">
                    <p class="text-center text-dark">GooglePay</p>
                    <img src="assets/img/pay/image1.jpg" class="img-fluid" alt="UTR Example 1">
                    
                </div>
                
                <div class="mb-3">
                    
                    <p class="text-center text-dark">Paytm</p>
                    <img src="assets/img/pay/image3.jpg" class="img-fluid" alt="TRN Example">
                    
                </div>
                <div class="mb-3">
                    
                    <p class="text-center text-dark">AmazonPay</p>
                    <img src="assets/img/pay/image4.jpg" class="img-fluid" alt="TRN Example">
                    
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Coupon modal-->
<div class="modal fade" id="couponModal" tabindex="-1" role="dialog" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          
            <div class="modal-header">
                <h5 class="modal-title text-dark" id="couponModalLabel">Apply Coupon</h5>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="margin-left: 20px;">Close</button>
            </div>
            <div class="modal-body">
                <form id="couponForm" style=" display: flex;align-content: center;justify-content: center;align-items: flex-end;">
                    <div class="form-group">
                       
                        <input type="text" class="form-control" id="couponCode" placeholder="Enter Coupon Code">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left:20px;" id="CouponApplyBtn">Apply</button>
                </form>
                <div id="couponMessage" class="mt-3 text-center"></div>
                <hr>
                <p class="text-dark">Trending Coupons:</p>
<?php
    // Fetch coupons where description is "For All Users"
    $stmt = $conn->prepare("SELECT code FROM Coupons WHERE description = 'For All Users'");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        echo '<div class="mb-1">';
        echo '<button class="btn btn-outline-primary w-100 mb-1" onclick="fillCoupon(\'' . htmlspecialchars($row['code']) . '\')">' . htmlspecialchars($row['code']) . '</button>';
        echo '</div>';
    }
    $stmt->close();
    // Close the database connection
$conn->close();
?>
               
            </div>
            
        </div>
    </div>
</div>
<script>
 function fillCoupon(couponCode) {
        document.getElementById('couponCode').value = couponCode;
    }
    </script>
    
<!-- coupOn end here-->

  </main><!-- End #main -->
  

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
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
   var couponCodeInput = document.getElementById('FinalCouponCode');
   
    document.getElementById('couponForm').addEventListener('submit', function(event) {
    CouponApplyBtn.disabled = true;
     CouponApplyBtn.textContent = "";
    CouponApplyBtn.classList.add("loading");
        event.preventDefault();
        var couponCode = document.getElementById('couponCode').value;
        applyCoupon(couponCode);
    });



    function applyCoupon(couponCode) {
        fetch('/apply-coupon.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ couponCode: couponCode }),
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
                couponCodeInput.value=couponCode;
                
 CouponApplyBtn.disabled = false;
     CouponApplyBtn.textContent = "Apply";
    CouponApplyBtn.classList.remove("loading");

         
                couponLink.textContent = 'Coupon applied! Saved ₹' + data.discount;
                couponLink.style.color = 'green';
                couponLink.style.pointerEvents = 'none';
            } else {
                console.error('Coupon not applied:', data.message);
                messageElement.textContent = data.message;
                messageElement.style.color = 'red';
                updatePayableAmount(0);
                couponCodeInput.value="NA";

                 couponLink.textContent = 'Apply Coupon';
                couponLink.style.color = 'green';
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
                couponLink.style.color = 'green';
                couponLink.style.pointerEvents = 'auto';

             CouponApplyBtn.disabled = false;
     CouponApplyBtn.textContent = "Apply";
    CouponApplyBtn.classList.remove("loading");
        });
    }

    
</script>

     <script>
      var amt = 99;
      var showamt=99;
    var paymentInfo = document.getElementById('payment-info');
    var planSelector = document.getElementById('plan');
    var monthSelector = document.getElementById('month');

    function updatePayableAmount(discount) {
        var payableAmount = amt - discount;
        paymentInfo.textContent = 'Pay ₹' + payableAmount;
        showamt = payableAmount;
    }

    document.getElementById('pay-button').addEventListener('click', function() {
        var selectedValue = planSelector.value;
        var selectedMonth = monthSelector.value;
        amt = selectedValue * selectedMonth;

        // Update the payment information
        paymentInfo.textContent = 'Pay ₹' + amt;

        var planSelectorDiv = document.getElementById('plan-selector');
        var paymentConfirmationDiv = document.getElementById('contact');

        // Hide the current div with a fade-out effect
        planSelectorDiv.classList.add('fade-out');
        
            
            planSelectorDiv.classList.add('hidden');
            planSelectorDiv.classList.remove('fade-out');
            
            paymentConfirmationDiv.classList.remove('hidden');
            paymentConfirmationDiv.classList.add('fade-in', 'visible');
        
    });
    
  </script>
  
    <script>
        function launchUPIPayment() {
            const upiID = "Q008853812@ybl";
            const amount = showamt;
            const vendor="TechCorp";
            const utridmobile = document.getElementById("utridmobile").value;
            const remarks = encodeURIComponent(utridmobile);
            const upiLink = `upi://pay?pa=${upiID}&pn=${vendor}&am=${amount}&cu=INR&tn=${remarks}`;
            //const upiLink = `upi://pay?pa=${upiID}&pn=${vendor}&tn=${remarks}&cu=INR`;
            window.location.href = upiLink;
            
        }
    </script>
  <script>
    function utr(){
      var scanqr=document.getElementById('scanqrupi');
      var fillutr=document.getElementById('fillutr');
      var utrInput = document.getElementById('utrid');
      utrInput.setAttribute('required', 'required');
      scanqr.classList.add('hidden');
      fillutr.classList.remove('hidden');

    }

    function EmailSubmit(){
      var scanqr=document.getElementById('scanqrupi');
      var fillemail=document.getElementById('fillemail');
      var emailInput = document.getElementById('emailid');
      emailInput.setAttribute('required', 'required');
      launchUPIPayment();
    //   scanqr.classList.add('hidden');
    //   fillemail.classList.remove('hidden');

     // Delay execution of the following code by 5 seconds (5000 milliseconds)
    setTimeout(function() {
        scanqr.classList.add('hidden');
        fillemail.classList.remove('hidden');
    }, 10000); // 5000 milliseconds = 5 seconds
    }
  </script>    

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    var utrid = document.getElementById('utrid');
    var utridMobile = document.getElementById('utridmobile');

    utrid.addEventListener('input', function () {
        if (utrid.value) {
            utridMobile.value = '';
        }
    });

    utridMobile.addEventListener('input', function () {
        if (utridMobile.value) {
            utrid.value = '';
        }
    });
});
</script>
</body>
</html>