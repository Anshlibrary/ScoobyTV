<?php

session_start();
require '../conn.php';

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
$associate_sql = "SELECT fullname, credits, username, session_id, user_type, isdisabled FROM Associate WHERE associate_id = ?";
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
$associate_name = htmlspecialchars($associate['fullname']);
$username = htmlspecialchars($associate['username']);
$user_type = htmlspecialchars($associate['user_type']);
$isdisabled = htmlspecialchars($associate['isdisabled']);
$first_name = explode(' ', $associate_name)[0];

// Check if the
        if ($isdisabled) {
            header('Location: signout.php');
            exit();
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
    display:none;
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
    }
    table td {
        background-color: #343a40;
    }

        table th, table td {
            border: 1px solid #ddd;
            padding: 5px 10px;
            text-align: left;
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
.copy-icon {
        margin-left: 8px;
        cursor: pointer;
        color: #007bff;
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
          <li><a href="">AdminPanel</a></li>
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
      <h2 style="font-size: 18px;">Admin- <?php echo $first_name; ?></h2>
      <a href="#" class="logo"></a>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto active" href="dashboard.php">Dashboard</a></li>
           <?php if ($user_type === 'super_user'): ?>
        <li><a class="nav-link scrollto" href="clients.php">Clients</a></li>
    <?php endif; ?>
      <?php if ($user_type === 'client_user'): ?>
        <li><a class="nav-link scrollto" href="http://stv1.xyz:53575">Request Content</a></li>
    <?php endif; ?>
          <li><a class="nav-link scrollto" href="renew_users.php">Renew users</a></li>
          <?php if ($user_type !== 'client_user'): ?>
           <li><a class="nav-link scrollto" href="cancel_users.php">Refund</a></li>
            <?php endif; ?>
          <li><a class="nav-link scrollto" href="tutorial.php">Tutorials</a></li>
          <li><a class="nav-link scrollto" href="../support.php">Support</a></li>
           <?php if ($user_type !== 'client_user'): ?>
        <li><a class="nav-link scrollto " href="pricing.php">Pricing</a></li>
    <?php endif; ?>
          
          <li><a class="nav-link scrollto" href="faq.php">FAQ's</a></li>
          <li>
     
                        <a class="getstarted scrollto signin_mobile" href="profile.php">Credits :&nbsp;&nbsp; <b style="color:#09f109;"> <?php echo htmlspecialchars($associate['credits']); ?></b></a>
                   
          </li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->


  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;">Associate Dashboard</h2>
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
       
       <form action="create_user.php" method="post" role="form" class="my-email-form" id="loginForm">
         <div class="col-lg-3 col-md-3 col-12">
        <input type="hidden" class="form-control" name="updatedby" id="updatedby" value="<?php echo htmlspecialchars($associate_id); ?>">
      </div>
  <div id="inputContainer">
    <!-- Initial input fields with serial number -->
    <div class="form-group mt-3 row d-flex align-items-center flex-wrap" style="gap: 0px; flex-wrap: nowrap;">
      <div class="srno-circle d-flex justify-content-center align-items-center" style="width:40px;height:40px;border-radius:50%;background-color:#007bff;color:white;">
        1
      </div>
      <div class="col-lg-3 col-md-3 col-12 mb-1">
        <input type="text" class="form-control" name="username1" id="username1" placeholder="Username" minlength="5" required>
      </div>
      <div class="col-lg-3 col-md-3 col-12 mb-1">
        <input type="password" class="form-control" name="password1" id="password1" placeholder="Password" minlength="8" required>
      </div>

      <div class="col-lg-3 col-md-3 col-12 mb-1">
        <div class="form-group" style="">
                      <select id="month" name="month1" class="form-control">
                          <option value="1">Subscription for 1 month</option>
                          <option value="2">Subscription for 2 month</option>
                      </select>
                    </div>

      </div>
    </div>
  </div>

  <!-- Button to add new input fields -->
  <div class="btn-wrap text-center mt-3">
    <a href="javascript:void(0)" class="btn btn-buy" style="border-radius:50%;padding: 8px 15px;" onclick="addInputFields()">+</a>
  </div>
  <button type="submit" class="btn btn-buy hidden" id="hiddenSubmit">Create User</button>
  
<p class="text-center" style="">1 credit = 1 user per 1 month.</p>

</form>


 <div class="btn-wrap text-center mt-3">
    <button class="btn btn-buy" id="SubmitBtn" onclick="CreateUser()">Create User</button>
  </div>
</div>
</div>
</div>
</div>
</div>

 <script>
  // On page load, check if a value is saved in localStorage and set it as the default selected option
  document.addEventListener("DOMContentLoaded", function() {
    var savedValue = localStorage.getItem('subscription');
    
    if (savedValue) {
      document.getElementById('month').value = savedValue;
    }
  });
</script>

<script>
let userCount = 1;
    function validateCredits() {
    let creditBalance = <?= $associate['credits'] ?>; // Fetch available credits
    if (userCount > creditBalance) {
        alert('Not enough credits to create user.');
        return false; // Stop form submission
    }
    return true; // Proceed with form submission
}
  function CreateUser() {

       var form = document.querySelector('form'); // Select the form
    if (!form.reportValidity()) { // Trigger validation and check if form is valid
        var button = document.getElementById("SubmitBtn");
        button.disabled = false; // Re-enable the button if validation fails
        button.classList.remove("loading"); // Remove loading state
        button.innerText = "Create User"; // Restore original text
        return; // Stop if form is invalid
    }

         if (!validateCredits()) {
        return; // Stop if not enough credits
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

<script>
  let inputCount = 1; // To keep track of the number of input rows
  const maxInputs = 10; // Maximum number of input rows allowed

  function addInputFields() {
    // Prevent adding more than 10 input rows
    if (inputCount >= maxInputs) {
      alert("Maximum 10 users can be added.");
      return;
    }

    inputCount++; // Increment the input counter
    userCount++;

    // Create a new set of input fields with incremented names, IDs, and serial number
    const newInputGroup = `
      <div class="form-group mt-3 row d-flex align-items-center flex-wrap" style="gap: 0px; flex-wrap: nowrap;">
        <div class="srno-circle d-flex justify-content-center align-items-center" style="width:40px;height:40px;border-radius:50%;background-color:#007bff;color:white;">
          ${inputCount}
        </div>
        <div class="col-lg-3 col-md-3 col-12">
          <input type="text" class="form-control" name="username${inputCount}" id="username${inputCount}" placeholder="Username" minlength="5" required>
        </div>
        <div class="col-lg-3 col-md-3 col-12">
          <input type="password" class="form-control" name="password${inputCount}" id="password${inputCount}" placeholder="Password" minlength="8" required>
        </div>
         <div class="col-lg-3 col-md-3 col-12 mb-1">
        <div class="form-group" style="">
                      <select id="month${inputCount}" name="month${inputCount}" class="form-control">
                          <option value="1">Subscription for 1 month</option>
                          <option value="2">Subscription for 2 month</option>
                      </select>
                    </div>

      </div>

      </div>
      </div>
       ` ;

    // Append the new input fields to the input container
    document.getElementById('inputContainer').insertAdjacentHTML('beforeend', newInputGroup);
       const savedValue = localStorage.getItem('subscription');
    if (savedValue) {
       document.getElementById(`month${inputCount}`).value = savedValue;
    }
  }
</script>

<h3 style="margin-bottom:0;" class="mt-3">All Created Users:</h3>
      <div class="row" style="background: linear-gradient(to bottom right, #716d6d, #141414); border-radius:20px;padding:10px;">
<div class="col-lg-12 mb-3 table-responsive" >
        <table id="userTable">
            <thead>
                <tr>
                    <th>Sr. No.</th>
                    <th>Username</th>
                     <th>Password</th>
                     <th>Validity</th>
                      <th>Plan Expiry</th>
                     <th>No of Devices</th>
                     <th>Jellyfin_status</th>
                  
                </tr>
            </thead>
        <tbody>
            <?php
                // Define the pattern for txn_id, assuming it follows the format "reseller_<associate_id>"
                $txn_id_pattern = $username; // Add wildcard for LIKE

                // Query to fetch user details based on txn_id pattern
                $sql = "SELECT id, username, password, plan_valid_for, plan_expiry, no_of_devices, jellyfin_status 
                        FROM users 
                        WHERE txn_id = ? ORDER BY id DESC";

                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    $_SESSION['status_messages'][] = "Database error: (" . $conn->errno . ") " . $conn->error;
                    exit;
                }

                // Bind the txn_id pattern parameter
                $stmt->bind_param('s', $txn_id_pattern);
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
                            <td class="user-id-cell"><?php echo htmlspecialchars($srno); ?>   <i class="fa-regular fa-clipboard copy-icon" onclick="copyCredentials('<?php echo htmlspecialchars($user['username']); ?>', '<?php echo htmlspecialchars($user['password']); ?>')" title="Copy Login Details"></i></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                            <div class="d-flex" style="gap:5px;">
                                <input type="password" class="form-control password-input" value="<?php echo htmlspecialchars($user['password']); ?>" disabled>
                                <button class="btn btn-sm btn-outline-primary toggle-password" type="button" onclick="togglePasswordVisibility(this)">
                                    <i class="fa fa-eye" id="togglePassword"></i>
                                </button>
                            </div>
                            </td>
                            <td><?php echo htmlspecialchars($user['plan_valid_for']) . ' mon'; ?></td>
                            <td style="<?php echo (strtotime($user['plan_expiry']) < time()) ? 'color: #ff4b2b;' : ''; ?>"><?php echo htmlspecialchars($user['plan_expiry']); ?></td>
                            <td><?php echo htmlspecialchars($user['no_of_devices']); ?></td>
                            <td><div style="color: <?php echo (strpos($user['jellyfin_status'], 'active') !== false) ? '#00ff00' : '#ff4b2b'; ?>"><?php echo htmlspecialchars($user['jellyfin_status']); ?></td>
                        </tr>
                        <?php
                        $srno++;
                    }
                } else {
                    echo "<tr><td colspan='7'>No results found.</td></tr>";
                }

                // Close statement and connection
                $stmt->close();
                $conn->close();
                ?>
   </tbody>
        </table>
    </div>
</div>
<script>
    function copyCredentials(username, password) {
        // Format the message to be copied
        const message = `Jellyfin login details\nHost/Server - \`stv1.xyz:53570\`\nUsername - \`${username}\`\nPassword - \`${password}\``;

        // Create a temporary textarea element to hold the message
        const textarea = document.createElement('textarea');
        textarea.value = message;

        // Append the textarea to the body (required for Firefox)
        document.body.appendChild(textarea);

        // Select the text
        textarea.select();
        textarea.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text to clipboard
        document.execCommand('copy');

        // Remove the textarea from the document
        document.body.removeChild(textarea);

        // Optional: Show a temporary alert or message
        alert('Login details copied to clipboard!');
    }
</script>

</div>
    </div>
    
  </section>
</section>
  </main><!-- End #main -->
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
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
</body>
</html>