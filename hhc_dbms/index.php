<?php

// Include the database connection
include 'db.php';

// Set session cookie parameters before starting session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

// Set session timeout to 15 minutes (900 seconds)
$timeout_duration = 900;

if (isset($_SESSION['LAST_ACTIVITY'])) {
    $elapsed_time = time() - $_SESSION['LAST_ACTIVITY'];
    if ($elapsed_time > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?timeout=true");
        exit();
    }
}

$_SESSION['LAST_ACTIVITY'] = time();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get total member count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_members FROM MEMBERS");
$stmt->execute();
$member_count = $stmt->get_result()->fetch_assoc()['total_members'];
$stmt->close();

// Get total department count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_departments FROM DEPARTMENTS");
$stmt->execute();
$department_count = $stmt->get_result()->fetch_assoc()['total_departments'];
$stmt->close();

// Get total tithe count and calculate percentage
// Get total unique tithers (counting distinct names and contacts together)
$tithe_count_result = $conn->query("SELECT COUNT(DISTINCT CONCAT(NAME, CONTACT)) AS total_tithers FROM TITHES");
$total_tithers = $tithe_count_result ? $tithe_count_result->fetch_assoc()['total_tithers'] : 0;

// Calculate the percentage of tithers
$percentage_tithers = ($member_count > 0) ? ($total_tithers / $member_count) * 100 : 0;
$percentage_tithers = round($percentage_tithers, 2); // Round to 2 decimal places
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HHC Takoradi DBMS - Dashboard</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <img src="hhctak.jpg" alt="HHC Takoradi Logo" class="logo" width="500">
                <h1 class="mb-0">Church Management System</h1>
                <p class="mb-0">Welcome to HHC Takoradi Database Management System</p>
            </div>
            <a href="logout.php" class="logout-button">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card fade-in">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-title">
                    <i class="fas fa-users"></i>
                    Total Members
                </h3>
                <div class="stat-value"><?php echo $member_count; ?></div>
                <p class="text-center mt-4 text-gray-600">Registered church members</p>
            </div>
            
            <div class="stat-card fade-in" style="animation-delay: 0.1s;">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="stat-title">
                    <i class="fas fa-layer-group"></i>
                    Departments
                </h3>
                <div class="stat-value"><?php echo $department_count; ?></div>
                <p class="text-center mt-4 text-gray-600">Active departments</p>
            </div>
            
            <div class="stat-card fade-in" style="animation-delay: 0.2s;">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h3 class="stat-title">
                    <i class="fas fa-hand-holding-usd"></i>
                    Tithe Participation
                </h3>
                <div class="stat-value"><?php echo "$percentage_tithers%"; ?></div>
                <p class="text-center mt-4 text-gray-600">Members contributing tithes</p>
            </div>
        </div>

        <div class="nav-buttons">
            <a href="view_members.php" class="btn btn-primary nav-button">
                <i class="fas fa-users"></i>
                Manage Members
            </a>
            <a href="view_departments.php" class="btn btn-secondary nav-button">
                <i class="fas fa-layer-group"></i>
                Manage Departments
            </a>
            <a href="view_tithes.php" class="btn btn-success nav-button">
                <i class="fas fa-hand-holding-usd"></i>
                Manage Tithes
            </a>
            <a href="add_member.php" class="btn btn-warning nav-button">
                <i class="fas fa-user-plus"></i>
                Add New Member
            </a>
        </div>

        <div class="card mt-8 fade-in" style="animation-delay: 0.3s;">
            <div class="card-header">
                <h3 class="mb-0">
                    <i class="fas fa-info-circle"></i>
                    System Information
                </h3>
            </div>
            <div class="card-body">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--spacing-4);">
                    <div>
                        <h4><i class="fas fa-database"></i> Database Status</h4>
                        <p class="text-success">Connected and operational</p>
                    </div>
                    <div>
                        <h4><i class="fas fa-clock"></i> Last Updated</h4>
                        <p><?php echo date('F j, Y - g:i A'); ?></p>
                    </div>
                    <div>
                        <h4><i class="fas fa-shield-alt"></i> Security</h4>
                        <p class="text-success">Session active and secure</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add loading animation on page load
        window.addEventListener('load', function() {
            document.body.style.opacity = '0';
            document.body.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                document.body.style.opacity = '1';
            }, 100);
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-4px) scale(1)';
            });
        });
    </script>
</body>
</html>
