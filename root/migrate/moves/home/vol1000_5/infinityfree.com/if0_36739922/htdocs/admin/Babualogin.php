<?php
// Start session
session_start();
// Check if the user is already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: services.php");
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
    $email = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the user exists
    $stmt = $conn->prepare("SELECT id, first_name FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user details
        $stmt->bind_result($id, $first_name);
        $stmt->fetch();

        // Set session variables
        $_SESSION['admin_id'] = $id;

        // Return success response
        echo json_encode(['status' => 'OK']);
         
    } else {
        // Return error response
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
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
  <title></title>
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
.contact .my-email-form .loading:before {
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
          <li><a class="nav-link scrollto" href="../index.php#pricing">Pricing</a></li>
          <li><a class="nav-link scrollto" href="../index.php#faq">FAQ's</a></li>
        
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
          <h2 style="margin-bottom:0;">Admin Sign in</h2>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="my-email-form" id="loginForm">
              <div class="row">
                <div class="col-md-12 form-group mt-3">
                  <input type="text" class="form-control" name="username" id="email" placeholder="Username" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
              </div>
              
                <div class="my-3">
                   <div id="messageDiv" style="display: none;"></div>
                </div>
              
              <div class="btn-wrap text-center mt-3">
                <button type="submit" class="btn btn-buy">Admin in</button>
              </div>
              
              <div class="text-center mt-3">
                <span class="text-dark">For Admins login only. if you're user sign in here - </span><a href="../signin.php" class="text-blue"> Sign in</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section><!-- End Contact Section -->

    <!-- Forgot Password Modal -->
    

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
                    window.location.href = 'services.php';
                } else {
                     messageDiv.classList.add('error-message');
                    messageDiv.textContent = data.message;
                    messageDiv.style.display = 'block';
                }
            })
            .catch(error => {
                messageDiv.classList.add('error-message');
                messageDiv.textContent = 'An error occurred. Please try again.';
                console.log(error);
                messageDiv.style.display = 'block';
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
