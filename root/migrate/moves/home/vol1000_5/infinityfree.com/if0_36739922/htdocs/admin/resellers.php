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

// Fetch all usernames from Associate table for the select options
$associate_sql = "SELECT fullname, username FROM Associate";
$associate_stmt = $conn->prepare($associate_sql);
$associate_stmt->execute();
$associate_result = $associate_stmt->get_result();

if ($admin_result->num_rows === 0) {
    echo "<h2 style='color:red;margin-top:50px;padding:10px 20px;text-align:center;'>Admin not found</h2>";
    exit;
}

$admin = $admin_result->fetch_assoc();
$admin_name = htmlspecialchars($admin['username']);


$total_amount = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reseller = $_POST['reseller'];

    // Use a prepared statement with a parameter for txn_id
    $sql = "SELECT id, full_name, email, password, created, plan_valid_for, plan_amount, purchase, plan_expiry, no_of_devices, allocated_ip, phone, username, txn_id, jellyfin_status, paid_amt
            FROM users 
            WHERE txn_id = ?";  // Use a placeholder for the txn_id value

    if ($sql) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $reseller);  // Bind the reseller value to the prepared statement
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = false;
    }
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
                                <select id="users_plan_expired" name="reseller" class="form-control" required>
                                     <option value="">Select Reseller</option>
      <?php
// Dynamically populate options with usernames and full names from the Associate table
while ($row = $associate_result->fetch_assoc()) {
    $username = htmlspecialchars($row['username']);
    $fullname = htmlspecialchars($row['fullname']);  // Assuming 'fullname' is the column name for full name in the Associate table
    echo "<option value='$username'>$username - $fullname</option>";
}
?>
                                </select>
                                <button type="submit" class="btn btn-buy" id="submit_btn">Submit</button>
                            </form>
                </div>
                 <div class="mt-3">
                 <a class="btn btn-sign" href="search_reseller.php">Search Reseller</a>
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
       <table>
    <thead>
        <tr>
            <th>ID</th>
            
            
                 <th>Username</th>
                 <th>Created</th>
                 <th>Plan Expiry</th>
                 <th>Valid For</th>
                 <th>Plan or Paid</th>
                <th>Txn ID</th>
                 <th class="">Pass</th>
                 <th>jellyfin_status</th> 
          
            
        </tr>
    </thead>
    <tbody id="userTableBody">
        <?php if ($result) {
                                        while ($user = $result->fetch_assoc()) {  
                                                $total_amount += $user['paid_amt'];
                                             ?>
                                            <tr>
                                                <td class="user-id-cell"><?php echo htmlspecialchars($user['id']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['created']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['plan_expiry']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['plan_valid_for']). ' mon'; ?></td>
                                                    <td><?php echo htmlspecialchars($user['paid_amt']) ?></td>
                                                    <td><?php echo htmlspecialchars($user['txn_id']); ?></td>
                                                    <td class=""><input type="password" class="form-control password-input" value="<?php echo htmlspecialchars($user['id']); ?>" disabled><button class="btn btn-sm btn-outline-primary toggle-password" type="button"><i class="fa fa-eye" id="togglePassword"></i></button></td>
                                                    <td ><?php echo htmlspecialchars($user['jellyfin_status']); ?></td>
                                              
                                                
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='3'>No results found. Please click submit as per query</td></tr>";
                                    } ?>
    </tbody>
</table>
  
                                <div style="margin-top: 20px;">
                                    <h3>Total Amount: <span style="color:#15fb15;">₹<?php echo $total_amount; ?></span></h3>
                                </div>
                       
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

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>


</body>
</html>