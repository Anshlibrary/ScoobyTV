<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $username = isset($_POST['search_input']) ? $_POST['search_input'] : '';

    // Base query
    $sql = "SELECT id, full_name, email, created, plan_valid_for, plan_amount, purchase, plan_expiry, phone, username FROM users WHERE 1=1";

    // Add username filter if a search input is provided
    if (!empty($username)) {
        $sql .= " AND username LIKE ?";
    }

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);

    // Bind parameters if username is provided
    if (!empty($username)) {
        $search_term = '%' . $username . '%';
        $stmt->bind_param('s', $search_term);
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
  </style>
</head>

<body>
  <main id="main">
    <!-- Search form for username -->
    <section class="contact section-bg">
      <div class="container">
        <h2>Admin - <?php echo "$admin_name"; ?> </h2>

        <div class="form-container">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" role="form" class="form-wrapper" id='search_form'>
            <div class="form-group">
              <input type="text" name="search_input" id="search_input" class="form-control" placeholder="Search by Username" value="<?php echo isset($_POST['search_input']) ? htmlspecialchars($_POST['search_input']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-buy" id="search_btn">Search</button>
          </form>
        </div>

        <div class="table-responsive mt-3">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Username</th>
                <th>Created</th>
                <th>Plan Validity</th>
                <th>Plan Amount</th>
                <th>Plan Expiry</th>
                <th>Phone</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($result && $result->num_rows > 0) {
                while ($user = $result->fetch_assoc()) { ?>
                  <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['created']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_valid_for']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_amount']); ?></td>
                    <td><?php echo htmlspecialchars($user['plan_expiry']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
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
