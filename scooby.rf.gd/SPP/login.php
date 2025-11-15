<?php
ini_set('session.gc_maxlifetime', 2592000); // 30 days
// Start session
session_start();
// Check if the user is already logged in
if (isset($_SESSION['associate_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Include your database connection script
include '../conn.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Clear output buffer to avoid any extra output
    ob_clean();

    // Set content type to JSON
    header('Content-Type: application/json');

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the associate exists and fetch session_id and expiry
    $stmt = $conn->prepare("SELECT associate_id, fullname, session_id, session_expiry, password, isdisabled FROM Associate WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the email exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($associate_id, $fullname, $db_session_id, $db_session_expiry, $hashed_password, $isdisabled);
        $stmt->fetch();
        
        // Check if the session has expired
        if ($isdisabled) {
            echo json_encode(['status' => 'error', 'message' => 'Your id has been disabled, Please contact your administrator.']);
            exit();
        }
        // Check if the session has expired
        if ($db_session_expiry && strtotime($db_session_expiry) < time()) {
            // Session expired; clear session_id and allow login
            $clear_stmt = $conn->prepare("UPDATE Associate SET session_id = NULL, session_expiry = NULL WHERE associate_id = ?");
            $clear_stmt->bind_param("i", $associate_id);
            $clear_stmt->execute();
            $clear_stmt->close();
        }

        // Verify password (assuming passwords are stored in a secure format)
        if (password_verify($password, $hashed_password)) {
            // User is not logged in anywhere else, proceed with login

            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);
            $new_session_id = session_id();

            // Set session variables
            $_SESSION['associate_id'] = $associate_id;
            $_SESSION['fullname'] = $fullname;
            $_SESSION['session_id'] = $new_session_id;

            // Set secure cookie parameters
            $cookieParams = session_get_cookie_params();
            setcookie(session_name(), session_id(), time() + (30*86400), // Cookie expires in 30 x 1 day (86400 seconds)
                      $cookieParams["path"], $cookieParams["domain"],
                      true, // Secure - true for HTTPS
                      true  // HttpOnly - true to prevent JavaScript access
            );

            // Set session expiry time (1 day from now)
            $session_expiry = date('Y-m-d H:i:s', strtotime('+30 day'));

            // Update the session_id and expiry in the database
            $update_stmt = $conn->prepare("UPDATE Associate SET session_id = ?, session_expiry = ?, last_login = CURRENT_TIMESTAMP WHERE associate_id = ?");
            $update_stmt->bind_param("ssi", $new_session_id, $session_expiry, $associate_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Return success response
            echo json_encode(['status' => 'OK']);
        } else {
            // Invalid password
            echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
        }
    } else {
        // No matching email found
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    }

    $stmt->close();
    $conn->close();

    // Terminate script execution after sending JSON response
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="robots" content="noindex, nofollow">
  <title>Sign In</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/favicon.png" rel="icon">
  <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
    .btn-buy{
        background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%); 
        display: inline-block;
        padding: 10px 35px;
        border-radius: 4px;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-buy:hover{
        background: linear-gradient(to right, #ff416c, #ff4b2b);
        color: #fff;
    }
    .contact .my-email-form {
        /* box-shadow: 0 0 30px rgb(140 0 255 / 64%); */
        padding: 30px;
        background: #fff;
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
.modal-footer {
      --bs-modal-footer-border-color: none;
      
    }
 @media (min-width: 950px) {
  section {
    padding: 100px 450px;
  }
}
@media (max-width: 576px) {
  .signin_mobile{
      background: linear-gradient(90deg, #ff416c, #ff4b2b);
  }
}
  </style>
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="fixed-top ">
    <div class="container d-flex align-items-center justify-content-between">
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="../index.php">Home</a></li>
          <li><a class="nav-link scrollto" href="../index.php#movies">Movies</a></li>
          <li><a class="nav-link scrollto" href="../index.php#series">Series</a></li>
         <li><a class="nav-link scrollto" href="../index.php#faq">FAQ's</a></li>
          <li><a class="getstarted scrollto signin_mobile" href="signup.php">Sign up</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->
    </div>
  </header><!-- End Header -->

  <main id="main">
    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact section-bg" style="background: linear-gradient(45deg, rgba(59, 59, 59, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%);">
      <div class="container" data-aos="fade-up">
        <div class="section-title">
          <h2 style="margin-bottom:0;">ScoobyTV Associate Sign in</h2>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="my-email-form" id="loginForm" style="background:linear-gradient(to bottom right, #716d6d, #141414);color:#fff;border-radius:20px;">
              <div class="row">
                <div class="col-md-12 form-group mt-3">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Registered Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
              </div>
              
                <div class="my-3">
                   <div id="messageDiv" style="display: none;"></div>
                </div>
              
              <div class="btn-wrap text-center mt-3">
                <button type="submit" class="btn btn-buy">Sign in</button>
              </div>
              <div class="text-center mt-3">
                <a href="reset_password.php" class="text-blue">Forgot Password?</a>
              </div>
              <div class="text-center">
                <span class="">Don't have an account?</span><a href="signup.php" class="text-blue"> Sign Up</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section><!-- End Contact Section -->

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p style="color:#141414;">Fill your registered email id to get instructions for password reset.</p>
         <form id="forgotPasswordForm" method="POST" action="reset_password.php">
          <input type="hidden" name="action" value="forgot_password">
          <div class="col-md-12 form-group mt-3">
            <input type="email" class="form-control" name="email" id="email" placeholder="Registered Email" required>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="submit" class="btn btn-primary" id="ResetpassButton">Submit</button>
          </div>
        </form>
          </div>
          <div class="modal-footer">
            
          </div>
        </div>
      </div>
    </div>
 <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("forgotPasswordForm").addEventListener("submit", function(event) {
            var button = document.getElementById("ResetpassButton");
            button.disabled = true;
            button.classList.add("loading");
        });
    });
    </script>
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Get form data
            const formData = new FormData(this);
            const messageDiv = document.getElementById('messageDiv');

            // Clear previous messages
            messageDiv.style.display = 'none';
            messageDiv.textContent = '';
            messageDiv.className = ''; // Remove all previous classes

            // Send AJAX request
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'OK') {
                    
                    messageDiv.classList.add('sent-message');
                    messageDiv.classList.add('loading');
                    messageDiv.textContent = 'Logging in 🙂...';
                    messageDiv.style.display = 'block';
                    window.location.href = 'dashboard.php';
                } else {
                     messageDiv.classList.add('error-message');
                    messageDiv.textContent = data.message;
                    messageDiv.style.display = 'block';
                }
            })
            .catch(error => {
                messageDiv.classList.add('error-message');
                messageDiv.textContent = 'An error occurred. Please try again.';
                messageDiv.style.display = 'block';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
