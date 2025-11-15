<?php
session_start();
require '../conn.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: Babualogin.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Query to fetch admin's first name
$admin_sql = "SELECT first_name, username FROM admins WHERE id = ?";
$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->bind_param('i', $admin_id);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();

if ($admin_result->num_rows === 0) {
    echo "<h2 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>Admin not found</h2>";
    exit;
}

$admin = $admin_result->fetch_assoc();
$admin_name = htmlspecialchars($admin['username']);

$sql = "";
$total_amount = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users_plan_expired = $_POST['users_plan_expired'];

    if ($users_plan_expired == 'expired') {
        // Query to fetch users with expired plans
        $sql = "SELECT id, full_name, email, password, modified, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id
                FROM users 
                WHERE plan_expiry < NOW()";
    } elseif ($users_plan_expired == 'paid_users') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, paid_amt, no_of_devices, allocated_ip, phone, username, txn_id, jellyfin_status
                FROM users 
                WHERE purchase = 'Successfull & Verified' 
                AND txn_id REGEXP '^[0-9]{1,16}$'";
    }
     elseif ($users_plan_expired == 'trial_users') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id
                FROM users 
                WHERE txn_id = '1_day_trial'";
    }
     elseif ($users_plan_expired == 'guest_users') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id
                FROM users 
                WHERE txn_id = 'guest'";
    }
      elseif ($users_plan_expired == 'Payment not made') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id
                FROM users 
                WHERE purchase = 'Payment not made'";
    }
       elseif ($users_plan_expired == 'All Users') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, phone, username, txn_id
                FROM users";
    }
    elseif ($users_plan_expired == 'Active Users') {
        // Query to fetch users with successful purchases
        $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, phone, username, txn_id, jellyfin_status
                FROM users where jellyfin_status like 'active%'";
    }
}

if ($sql) {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
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
<!-- DataTables CSS -->
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
  input {
        padding: 10px 15px;
    }
    input, textarea {
        border-radius: 5px;
        box-shadow: none;
        font-size: 14px;
    }
 .my-email-form .sent-message {
    display: none;
    color: #fff;
    background: #18d26e;
    text-align: center;
    padding: 15px;
    font-weight: 600;
}
 .my-email-form .error-message {
    display: none;
    color: #fff;
    background: #ed3c0d;
    text-align: left;
    padding: 15px;
    font-weight: 600;
}
 .my-email-form .loading:before {
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
  .form-container {
            display: flex;
            justify-content: center;
        }
        .form-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            max-width: 500px;
            width: 100%;
        }
        .loading:before {
    content: "";
    display: inline-block;
    border-radius: 50%;
    width: 14px;
    height: 14px;
    margin: 0 10px -6px 0;
    border: 3px solid #18d26e;
    border-top-color: #eee;
    animation: animate-loading 1s linear infinite;
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
</style>
</head>

<body> 
  <main id="main">
 <!-- ======= Breadcrumbs ======= -->
 <section class="breadcrumbs blog-header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      
      <a href="../index.php" class="logo"><img src="../assets/img/scoobytv_logo.png" alt="logo" class="img-fluid logo-img"></a>
      
      <div class="my-breadcrumb-list">
        <ol > 
          <li><a href="../index.php">Home</a></li>
          <li><a href="services.php">Services</a></li>
          <li>AdminPanel</li>
        </ol>
        <a href="../signout.php" class="btn btn-sign" style="">Sign out</a>
      </div>
     
    </div>

  </div>
</section><!-- End Breadcrumbs -->

<section class="inner-page blog-body" style=" padding: 5px 0;">
  <div class="container">
   <h2 style="margin-left:auto;margin-right: auto;padding-left:10px ;font-size: 18px;">Admin-<?php echo "$admin_name"; ?> </h2>
   
  </div>
  <section class="contact section-bg">
    <div class="container profile-container mt-0" data-aos="fade-up">
      <div class="section-title mt-0">
       <div class="form-container">
                   
                      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="my-email-form form-wrapper" id="users_form">
                                <select id="all_users" name="all_users" class="form-control" required>
                                    <option value="all">All</option>
                                </select>
                                <select id="users_plan_expired" name="users_plan_expired" class="form-control" required>
                                     <option value="expired" <?php echo ($users_plan_expired == 'expired') ? 'selected' : ''; ?>>Plan Expired</option>
                                    <option value="paid_users" <?php echo ($users_plan_expired == 'paid_users') ? 'selected' : ''; ?>>Paid users</option>
                                     <option value="trial_users" <?php echo ($users_plan_expired == 'trial_users') ? 'selected' : ''; ?>>Trial users</option>
                                       <option value="guest_users" <?php echo ($users_plan_expired == 'guest_users') ? 'selected' : ''; ?>>Guest users</option>
                                        <option value="Payment not made" <?php echo ($users_plan_expired == 'Payment not made') ? 'selected' : ''; ?>>Payment not made</option>
                                        <option value="All Users" <?php echo ($users_plan_expired == 'All Users') ? 'selected' : ''; ?>>All Users</option>
                                        <option value="Active Users" <?php echo ($users_plan_expired == 'Active Users') ? 'selected' : ''; ?>>Active Users</option>
                                </select>
                                <button type="submit" class="btn btn-buy" id="submit_btn">Submit</button>
                            </form>
                </div>
                 <div class="mt-3">
                 <a class="btn btn-sign" href="search_by_username.php">Search by Username</a>
                 </div>
                 <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("users_form").addEventListener("submit", function(event) {
            var button = document.getElementById("submit_btn");
            button.disabled = true;
            button.classList.add("loading");
              button.innerHTML = "";
        });
    });
    </script>
    

        <h2 style="margin-bottom:0;font-size: 26px;padding-bottom:0;"><?php echo $payrequests; ?></h2>
      </div>
      <div class="row">
        <div class="col-lg-12 mb-3 table-responsive">
       <table id="userTable">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email <button type="button" class="btn btn-sm btn-primary" id="copyEmailsBtn">Copy</button></th> 
             <?php if ($users_plan_expired == 'paid_users' || $users_plan_expired == 'trial_users' || $users_plan_expired == 'guest_users' || $users_plan_expired == 'Payment not made' || $users_plan_expired == 'All Users' || $users_plan_expired == 'Active Users') { ?>
                 <th>Username</th>
                 <th>Created</th>
                 <th>Valid For</th>
                 <th>Plan or Paid</th>
                <th>Txn ID</th>
                 <th>Pass</th>
            <?php } ?>
            <th>Plan Expiry</th>
              <th>Jellyfin_status</th>
        </tr>
    </thead>
    <tbody id="userTableBody">
        <?php if ($result) {
                                        while ($user = $result->fetch_assoc()) {  if ($users_plan_expired == 'paid_users' || $users_plan_expired == 'Active Users') {
                                                $total_amount += $user['paid_amt'];
                                            } ?>
                                            <tr>
                                                <td class="user-id-cell"><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td class="email-cell"><?php echo htmlspecialchars($user['email']); ?></td>
                         <?php if ($users_plan_expired == 'paid_users' || $users_plan_expired == 'trial_users' || $users_plan_expired == 'guest_users' || $users_plan_expired == 'Payment not made' || $users_plan_expired == 'All Users' || $users_plan_expired == 'Active Users') { ?>
                                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['created']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['plan_valid_for']). ' mon'; ?></td>
                                                    <td><?php 
echo $users_plan_expired == 'paid_users' 
    ? htmlspecialchars($user['paid_amt']) 
    : htmlspecialchars($user['plan_amount']); 
?></td>
                                                    <td><?php echo htmlspecialchars($user['txn_id']); ?></td>
                                                    <td><input type="password" class="form-control password-input" value="<?php echo htmlspecialchars($user['password']); ?>" disabled><button class="btn btn-sm btn-outline-primary toggle-password" type="button"><i class="fa fa-eye" id="togglePassword"></i></button></td>
                                                <?php } ?>  
                                                <td style="<?php echo (strtotime($user['plan_expiry']) < time()) ? 'color: #ff4b2b;' : ''; ?>"><?php echo htmlspecialchars($user['plan_expiry']); ?></td>
                                                  <td><?php echo htmlspecialchars($user['jellyfin_status']); ?></td>
                                         
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='3'>No results found. Please click submit as per query</td></tr>";
                                    } ?>
    </tbody>
</table>
  <?php if ($users_plan_expired == 'paid_users') { ?>
                                <div style="margin-top: 20px;">
                                    <h3>Total Amount: <span style="color:#15fb15;">₹<?php echo $total_amount; ?></span></h3>
                                </div>
                            <?php } ?>
</div>

</div>
    </div>
    
  </section>
</section>

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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var userRows = document.querySelectorAll('#userTableBody tr');
        userRows.forEach(function(row, index) {
            var userIdCell = row.querySelector('.user-id-cell');
            userIdCell.textContent = index + 1; // Set sequential user ID starting from 1
        });
    // Function to copy emails to clipboard
    document.getElementById('copyEmailsBtn').addEventListener('click', function() {
        var emailCells = document.querySelectorAll('.email-cell'); // Select all cells with email
        var emails = Array.from(emailCells).map(cell => cell.textContent.trim()).join('\n'); // Extract and join emails
        
        // Create a temporary textarea to copy text to clipboard
        var tempTextarea = document.createElement('textarea');
        tempTextarea.value = emails;
        tempTextarea.setAttribute('readonly', '');
        tempTextarea.style.position = 'absolute';
        tempTextarea.style.left = '-9999px';
        document.body.appendChild(tempTextarea);
        tempTextarea.select();
        document.execCommand('copy');
        document.body.removeChild(tempTextarea);
        document.getElementById('copyEmailsBtn').innerHTML="Copied";
    });

    });

</script>
  </main><!-- End #main -->
 

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
  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>
<script>
$(document).ready(function() {
    $('#userTable').DataTable({
        "pageLength": 10, // Set default number of rows to display
        "lengthMenu": [10, 25, 50, 75, 100] // Options for number of rows
    });
});
</script>

</body>
</html>