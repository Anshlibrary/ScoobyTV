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
        background-color: #141414;
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
        padding: 30px;
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
  max-width: 250px;
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
  color: #0c7900;
  font-weight: bold;
  margin-bottom: 0;
}
.success-color{
  color: #00ff00ad;
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
    <section id="plan-selector" class="contact section-bg" style="background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);">
      <div class="container" data-aos="fade-up">
      <div class="section-title">
          <h2>Choose plan</h2>
          <p>click to change</p>
        </div>
        <div class="row">
        <div class="col-lg-12">
            <form action="" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="form-group mt-3">
                <select id="plan" name="plan" class="form-control">
                     <option value="69">Premium - ₹69</option>
                      <option value="49">Starter - ₹49</option>
                      
                  </select>
              </div> 
              </div>
               <div class="btn-wrap text-center mt-3" >
                <a href="#" class="btn-buy" id="pay-button">Pay</a>
              </div>
            </form>
            <br >
            <br>
            <br>       
 </div>
 </div>
</div>
    </section>
    <div class="text-center mt-3" id="payment-gateway" style="color: rgb(128, 128, 128);"><p>ScoobyTV Payment Gateway</p></div>

    <!-- ======= paySection ======= -->
    
    <section id="contact" class="contact section-bg hidden" style="background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);margin-top: -50px;" >
      <div class="container" data-aos="fade-up">

        <div class="section-title">
          <h2 style="margin-bottom: 0;" id="payment-info" class="success-color"></h2>
        </div>
        <div class="row">

          <div class="col-lg-12">
            <form action="" method="post" role="form" class="my-email-form" style="padding: 25px 15px;">
              <div class="row">
                <p class="text-pay">1. Pay to below QR code or UPI ID-</p>
                <div class="form-group mt-3 text-center">
                    <label for="plan">Scan QR and PAY</label>
                    <img src="assets/img/qr.webp" alt="logo" class="centered-img">
                  </div>
                  <h3 style="margin-bottom: 0;">OR</h3>
                  <div class="form-group mt-3 text-center">
                    <!-- <label for="upi">Pay with UPI ID</label> -->
                    <div class="input-group">
                      <input type="text" class="form-control" name="upi" id="upi" placeholder="" value="scoobytv@upi" disabled>
                      <button type="button" class="btn btn-outline-secondary" id="copyButton" onclick="copyUPI()" style="background: linear-gradient(42deg, #5846f9d9 0%, #7b27d8 100%);">
                       <?xml version="1.0" encoding="utf-8"?><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" stroke="currentColor" stroke-width="2" class="bi bi-clipboard" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 115.77 122.88" style="enable-background:new 0 0 115.77 122.88" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path class="st0" d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02 v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02 c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1 c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7 h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02 c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65 v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02 h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01 c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02 v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/></g></svg>
                      </button>
                    </div>
                  </div>
                <div class=" form-group mt-3">
                  <input type="hidden" class="form-control" name="email" id="email" value="" disabled>
                </div>
              </div>
              <div class="form-group mt-3">
                <p class="text-pay">2. After payment Submit your UTR or txn ID</p> 
                <div class="text-center"><a href="#" class="" data-toggle="modal" data-target="#upiModal">where to find utr/txn id?</a> </div>
                <input type="tel" class="form-control mt-1" name="phone" id="phone" placeholder="last 6 digits only" required>
              </div>
              <div class="btn-wrap text-center mt-3" >
                <a href="#" class="btn-buy">Submit</a>
              </div>
            </form>
 </div>
</div>

      </div>
    </section>


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
                    <img src="image1.jpg" class="img-fluid" alt="UTR Example 1">
                    <p class="text-center">UTR Example 1</p>
                </div>
                <div class="mb-3">
                    <img src="image2.jpg" class="img-fluid" alt="UTR Example 2">
                    <p class="text-center">UTR Example 2</p>
                </div>
                <div class="mb-3">
                    <img src="image3.jpg" class="img-fluid" alt="TRN Example">
                    <p class="text-center">TRN Example</p>
                </div>
            </div>
            
        </div>
    </div>
</div>


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
    

      document.getElementById('pay-button').addEventListener('click', function() {
        var planSelector = document.getElementById('plan');
            var selectedValue = planSelector.value;
            var paymentInfo = document.getElementById('payment-info');
            
            // Update the payment information
            paymentInfo.textContent = 'Pay ₹' + selectedValue;
          var planSelectorDiv = document.getElementById('plan-selector');
          var paymentConfirmationDiv = document.getElementById('contact');
          var paymentgateway = document.getElementById('payment-gateway');
          // Hide the current div
          planSelectorDiv.classList.add('hidden');
          paymentgateway.classList.add('hidden');
          // Show the confirmation div
          paymentConfirmationDiv.classList.remove('hidden');

          planSelectorDiv.classList.add('fade-out');
            
            // After the fade-out animation completes, hide the current div and show the confirmation div
            setTimeout(function() {
                planSelectorDiv.classList.add('hidden');
                planSelectorDiv.classList.remove('fade-out');

                // Apply fade-in animation to the confirmation div
                paymentConfirmationDiv.classList.remove('hidden');
                paymentConfirmationDiv.classList.add('fade-in', 'visible');
            }, 1500); // Match the transition duration
      });
  </script>
    
</body>
</html>