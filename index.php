<?php
session_start();
require_once "db.php";

// Handle login
if (isset($_POST['login_user'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();

  if ($user && $user['password'] === $password) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_id'] = $user['id'];
    header("Location: index.php");
    exit();
  } else {
    $login_error = "Invalid username or password.";
  }
}

// Handle registration
if (isset($_POST['register_user'])) {
  $newUsername = $_POST['newUsername'];
  $newEmail = $_POST['newEmail'];
  $newPassword = $_POST['newPassword'];
  $confirmPassword = $_POST['confirmPassword'];

  if ($newPassword !== $confirmPassword) {
    $register_error = "Passwords do not match.";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $newUsername, $newEmail, $newPassword);
    if ($stmt->execute()) {
      $_SESSION['username'] = $newUsername;
      $_SESSION['user_id'] = $stmt->insert_id;
      header("Location: index.php");
      exit();
    } else {
      $register_error = "Username already exists or error occurred.";
    }
  }
}

// Handle logout
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: index.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fuel Delivery</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: black;
      color: white;
      overflow: hidden;
    }
    .hidden { display: none; }
    .image-container {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
      overflow: hidden;
    }
    .image-container img {
      width: 100%; height: 100%;
      object-fit: cover;
    }
    .login-container, .dashboard, .form-page {
      background: rgba(0, 0, 0, 0.5);
      padding: 40px;
      margin: 14% auto;
      max-width: 400px;
      border-radius: 10px;
      text-align: center;
    }
    input, button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: none;
    }
    button {
      background: orange;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }
    .dashboard-text {
      margin: 10px 0;
      padding: 15px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 10px;
      cursor: pointer;
      font-size: 20px;
    }
    .options {
      position: absolute;
      top: 10px; right: 10px;
      display: flex;
      gap: 20px;
    }
    .option-box {
      background: rgba(159, 36, 36, 0.5);
      padding: 10px 20px;
      border-radius: 5px;
      color: white;
      font-size: 18px;
      cursor: pointer;
    }
    .logout {
      margin-top: 20px;
      color: red;
      cursor: pointer;
    }
    .error {
      color: red;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="image-container">
    <img src="images/R (1).jpg" alt="Background Image">
  </div>

  <div class="options">
    <span class="option-box" onclick="window.location.href='https://docs.google.com/forms/d/e/1FAIpQLSdEKqbrZwepzJ2n27TURYWVPSzfAoiXBxJvHpHqcXmr-hRKdw/viewform?usp=preview'">Survey</span>
    <span class="option-box" onclick="alert('Email: hppc53520@gmail.com')">Contact Us</span>
    <span class="option-box" onclick="window.location.href='https://example.com/api'">API</span>
  </div>

  <!-- Login -->
  <div id="loginPage" class="login-container <?php echo isset($_SESSION['username']) ? 'hidden' : ''; ?>">
    <h2>Login</h2>
    <?php if (isset($login_error)) echo "<div class='error'>$login_error</div>"; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="login_user">Login</button>
    </form>
    <button onclick="showPage('signup')">New User Sign Up</button>
  </div>

  <!-- Sign Up -->
  <div id="signupPage" class="hidden login-container">
    <h2>Sign Up</h2>
    <?php if (isset($register_error)) echo "<div class='error'>$register_error</div>"; ?>
    <form method="post">
      <input type="text" name="newUsername" placeholder="Create username" required />
      <input type="email" name="newEmail" placeholder="Email address" required />
      <input type="password" name="newPassword" placeholder="Create password" required />
      <input type="password" name="confirmPassword" placeholder="Confirm password" required />
      <button type="submit" name="register_user">Register</button>
    </form>
    <button onclick="showPage('login')">Back to Login</button>
  </div>

  <!-- Dashboard -->
  <div id="dashboard" class="hidden dashboard">
    <h2>Welcome, <?php echo $_SESSION['username'] ?? ''; ?>!</h2>
    <div class="dashboard-text" onclick="navigateTo('petrol')">Petrol Delivery</div>
    <div class="dashboard-text" onclick="navigateTo('diesel')">Diesel Delivery</div>
    <div class="dashboard-text" onclick="window.location.href='https://t.me/Fuelforyoubots'">Emergency Fuel Delivery</div>
    <div class="logout" onclick="window.location.href='?logout=1'">Logout</div>
  </div>

  <!-- Order Form -->
  <div id="formPage" class="hidden form-page">
    <h2 id="formTitle">Delivery</h2>
    <form method="post" action="order.php">
      <input type="hidden" name="fuel_type" id="fuel_type" />
      <label>Select Date & Time:</label>
      <input type="datetime-local" name="datetime" required />
      <label>Enter Address:</label>
      <input type="text" name="address" placeholder="Enter address..." required />
      <button type="submit">Confirm Order</button>
    </form>
  </div>

  <script>
    function showPage(page) {
      document.getElementById("loginPage").classList.add("hidden");
      document.getElementById("signupPage").classList.add("hidden");
      document.getElementById("dashboard").classList.add("hidden");
      document.getElementById("formPage").classList.add("hidden");

      if (page === "login") document.getElementById("loginPage").classList.remove("hidden");
      else if (page === "signup") document.getElementById("signupPage").classList.remove("hidden");
      else if (page === "dashboard") document.getElementById("dashboard").classList.remove("hidden");
      else if (page === "form") document.getElementById("formPage").classList.remove("hidden");
    }

    function navigateTo(type) {
      const title = type === "petrol" ? "Petrol Delivery" : "Diesel Delivery";
      document.getElementById("formTitle").textContent = title;
      document.getElementById("fuel_type").value = type;
      showPage("form");
    }

    window.onload = function () {
      <?php if (isset($_SESSION['username'])): ?>
        showPage('dashboard');
      <?php else: ?>
        showPage('login');
      <?php endif; ?>
    };
  </script>
</body>
</html>
