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
    $search_input = isset($_POST['search_input']) ? trim($_POST['search_input']) : '';

    // Base query
    $sql = "SELECT id, full_name, email, created, plan_valid_for, plan_amount, purchase, plan_expiry, phone, txn_id, username, jellyfin_status 
            FROM users WHERE 1=1";

    // Check if the search input is an email
    if (!empty($search_input)) {
        if (filter_var($search_input, FILTER_VALIDATE_EMAIL)) {
            // Search by email
            $sql .= " AND email LIKE ?";
        } else {
            // Search by username
            $sql .= " AND username LIKE ?";
        }

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $search_term = '%' . $search_input . '%';
        $stmt->bind_param('s', $search_term);
    } else {
        // If no search input, don't add filters
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = false;
}
?>


<!-- HTML code -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title></title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #141414;
        color: #fff;
        margin: 0;
        padding: 0;
        height: 100vh;
    }

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

    .btn-buy {
        background: linear-gradient(42deg, #5846f9 0%, #7b27d8 100%);
        padding: 5px 10px;
        border-radius: 4px;
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-buy:hover {
        background: linear-gradient(42deg, #1e1289 0%, #2c0656 100%);
        color: #fff;
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

      /*Custom slider css*/
    .blog-header{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 5px 0;}
    .blog-body{background: linear-gradient(to bottom right, #3b3b3b, #141414);padding: 20px 0;    }
    .breadcrumbs{margin-top:0;    }

    .logo-img{    max-height: 80px;    }
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

.hidden{
    display:none;
}

section{
  padding: 10px 0;
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
    <!-- Search form for username -->
    <section class="contact section-bg">
      <div class="container">
        <h2>Admin - <?php echo "$admin_name"; ?> </h2>

        <div class="form-container">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="form-wrapper" id='search_form'>
            <div class="form-group">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="Username or Email" value="<?php echo isset($_POST['search_input']) ? htmlspecialchars($_POST['search_input']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-buy" id="search_btn">Search</button>
          </form>
        </div>

        <div class="table-responsive mt-3">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Full name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Created</th>
                <th>Plan Validity</th>
                <th>Plan Amount</th>
                <th>Plan Expiry</th>
                <th>Phone</th>
                <th>txn id</th>
                 <th>Purchase</th>
                 <th>jelly_status</th>

              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['created']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_valid_for']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_amount']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_expiry']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                     <td><?php echo $user['txn_id']; ?></td>
                     <td><?php echo htmlspecialchars($user['purchase']); ?></td>
                      <td><?php echo htmlspecialchars($user['jellyfin_status']); ?></td>
                  </tr>
                <?php }
              } else { ?>
                <tr>
                  <td colspan="8" style="text-align: center;">No users found</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("search_form").addEventListener("submit", function(event) {
        var button = document.getElementById("search_btn");
        button.disabled = true;
        button.classList.add("loading");
        button.innerHTML = "";
      });
    });
  </script>
</body>
</html>
