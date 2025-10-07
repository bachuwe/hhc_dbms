<?php
include 'db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'hhctakoradi.free.nf',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validate_input($name, $contact) {
    if (empty($name) || !preg_match("/^[a-zA-Z-' ]*$/", $name)) {
        return "Invalid name format.";
    }
    if (empty($contact) || !preg_match("/^\+?[0-9\s]*$/", $contact)) {
        return "Invalid contact format.";
    }
    return true;
}

$messages = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = strtoupper(trim($_POST['name']));
    $contact = trim($_POST['contact']);
    $sex = strtoupper(trim($_POST['sex']));
    $marital_status = strtoupper(trim($_POST['marital_status']));
    $location = strtoupper(trim($_POST['location']));
    $department_name = isset($_POST['department_name']) ? trim($_POST['department_name']) : null;
    $date_of_birth = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : null;
    $employment_status = isset($_POST['employment_status']) ? $_POST['employment_status'] : null;

    $validation = validate_input($name, $contact);
    if ($validation !== true) {
        $messages[] = ['message' => $validation, 'type' => 'error'];
    } else {
        if (isset($_POST['add_member'])) {
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM MEMBERS WHERE NAME = ? AND CONTACT = ?");
            $check_stmt->bind_param("ss", $name, $contact);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count > 0) {
                $messages[] = ['message' => 'A member with the same Name and Contact already exists!', 'type' => 'error'];
            } else {
                $stmt = $conn->prepare("INSERT INTO MEMBERS (NAME, SEX, MARITAL_STATUS, LOCATION, CONTACT, DEPARTMENT_NAME, date_of_birth, employment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $name, $sex, $marital_status, $location, $contact, $department_name, $date_of_birth, $employment_status);
                if ($stmt->execute()) {
                    $messages[] = ['message' => 'Member successfully processed!', 'type' => 'success'];
                } else {
                    error_log("Database error: " . $stmt->error);
                    $messages[] = ['message' => 'An error occurred. Please try again.', 'type' => 'error'];
                }
            }
        } elseif (isset($_POST['edit_member'])) {
            $stmt = $conn->prepare("UPDATE MEMBERS SET SEX=?, MARITAL_STATUS=?, LOCATION=?, DEPARTMENT_NAME=?, date_of_birth=?, employment_status=? WHERE NAME=? AND CONTACT=?");
            $stmt->bind_param("ssssssss", $sex, $marital_status, $location, $department_name, $date_of_birth, $employment_status, $name, $contact);
            if ($stmt->execute()) {
                $messages[] = ['message' => 'Member successfully processed!', 'type' => 'success'];
            } else {
                error_log("Database error: " . $stmt->error);
                $messages[] = ['message' => 'An error occurred. Please try again.', 'type' => 'error'];
            }
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete'])) {
    $delete_name = strtoupper(trim($_GET['name']));
    $delete_contact = trim($_GET['contact']);
    
    $delete_stmt = $conn->prepare("DELETE FROM MEMBERS WHERE NAME=? AND CONTACT=?");
    $delete_stmt->bind_param("ss", $delete_name, $delete_contact);
    if ($delete_stmt->execute()) {
        $messages[] = ['message' => 'Member deleted successfully!', 'type' => 'success'];
    } else {
        $messages[] = ['message' => 'Error deleting member!', 'type' => 'error'];
    }
}

$search_name = '';
$search_location = '';
$search_department_name = '';
$search_employment_status = '';
$params = [];
$sql = "SELECT * FROM MEMBERS WHERE 1=1";

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    if (!empty($_GET['search_name'])) {
        $search_name = strtoupper(trim($_GET['search_name']));
        $sql .= " AND NAME LIKE ?";
        $params[] = "%$search_name%";
    }
    if (!empty($_GET['search_location'])) {
        $search_location = strtoupper(trim($_GET['search_location']));
        $sql .= " AND LOCATION LIKE ?";
        $params[] = "%$search_location%";
    }
    if (!empty($_GET['search_department_name'])) {
        $search_department_name = $_GET['search_department_name'];
        $sql .= " AND DEPARTMENT_NAME = ?";
        $params[] = $search_department_name;
    }
    if (!empty($_GET['search_employment_status'])) {
        $search_employment_status = $_GET['search_employment_status'];
        $sql .= " AND employment_status = ?";
        $params[] = $search_employment_status;
    }
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$departments = $conn->query("SELECT DEPARTMENT_NAME FROM DEPARTMENTS");

$edit_member = false;
if (isset($_GET['edit'])) {
    $edit_member = true;
    $edit_name = strtoupper(trim($_GET['name']));
    $edit_contact = trim($_GET['contact']);
    $edit_stmt = $conn->prepare("SELECT * FROM MEMBERS WHERE NAME=? AND CONTACT=?");
    $edit_stmt->bind_param("ss", $edit_name, $edit_contact);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    $edit_data = $edit_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management - HHC Takoradi</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="mb-0">
                    <i class="fas fa-users"></i>
                    Members Management
                </h1>
                <p class="mb-4">Add, edit, search and manage church members</p>
                <a href="index.php" class="btn btn-outline" style="color: white; border-color: white;">
                    <i class="fas fa-home"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Add/Edit Member Form -->
        <div class="card mt-8 fade-in">
            <div class="card-header">
                <h3 class="mb-0">
                    <i class="fas fa-<?php echo $edit_member ? 'edit' : 'user-plus'; ?>"></i>
                    <?php echo $edit_member ? 'Edit Member' : 'Add New Member'; ?>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="view_members.php" class="search-form">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                        </label>
                        <input type="text" name="name" class="form-input" placeholder="Enter full name" required 
                               value="<?php echo $edit_member ? htmlspecialchars($edit_data['NAME']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-phone"></i> Contact <span class="required">*</span>
                        </label>
                        <input type="tel" name="contact" class="form-input" placeholder="Enter contact number" required 
                               value="<?php echo $edit_member ? htmlspecialchars($edit_data['CONTACT']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-venus-mars"></i> Gender <span class="required">*</span>
                        </label>
                        <select name="sex" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="MALE" <?php if ($edit_member && $edit_data['SEX'] == 'MALE') echo 'selected'; ?>>Male</option>
                            <option value="FEMALE" <?php if ($edit_member && $edit_data['SEX'] == 'FEMALE') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-heart"></i> Marital Status <span class="required">*</span>
                        </label>
                        <select name="marital_status" class="form-select" required>
                            <option value="">Select Status</option>
                            <option value="SINGLE" <?php if ($edit_member && $edit_data['MARITAL_STATUS'] == 'SINGLE') echo 'selected'; ?>>Single</option>
                            <option value="MARRIED" <?php if ($edit_member && $edit_data['MARITAL_STATUS'] == 'MARRIED') echo 'selected'; ?>>Married</option>
                            <option value="WIDOWED" <?php if ($edit_member && $edit_data['MARITAL_STATUS'] == 'WIDOWED') echo 'selected'; ?>>Widowed</option>
                            <option value="DIVORCED" <?php if ($edit_member && $edit_data['MARITAL_STATUS'] == 'DIVORCED') echo 'selected'; ?>>Divorced</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Location <span class="required">*</span>
                        </label>
                        <input type="text" name="location" class="form-input" placeholder="Enter location" required 
                               value="<?php echo $edit_member ? htmlspecialchars($edit_data['LOCATION']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-users"></i> Department
                        </label>
                        <select name="department_name" class="form-select">
                            <option value="">Select Department</option>
                            <?php while ($row = $departments->fetch_assoc()) : ?>
                                <option value="<?php echo $row['DEPARTMENT_NAME']; ?>" 
                                        <?php if ($edit_member && $edit_data['DEPARTMENT_NAME'] == $row['DEPARTMENT_NAME']) echo 'selected'; ?>>
                                    <?php echo $row['DEPARTMENT_NAME']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-birthday-cake"></i> Date of Birth
                        </label>
                        <input type="date" name="date_of_birth" class="form-input" 
                               value="<?php echo $edit_member ? htmlspecialchars($edit_data['date_of_birth']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-briefcase"></i> Employment Status
                        </label>
                        <select name="employment_status" class="form-select">
                            <option value="">Select Status</option>
                            <option value="APPRENTICESHIP" <?php if ($edit_member && $edit_data['employment_status'] == 'APPRENTICESHIP') echo 'selected'; ?>>Apprenticeship</option>
                            <option value="EMPLOYED" <?php if ($edit_member && $edit_data['employment_status'] == 'EMPLOYED') echo 'selected'; ?>>Employed</option>
                            <option value="UNEMPLOYED" <?php if ($edit_member && $edit_data['employment_status'] == 'UNEMPLOYED') echo 'selected'; ?>>Unemployed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="<?php echo $edit_member ? 'edit_member' : 'add_member'; ?>" 
                                class="btn <?php echo $edit_member ? 'btn-warning' : 'btn-primary'; ?> btn-full">
                            <i class="fas fa-<?php echo $edit_member ? 'save' : 'user-plus'; ?>"></i>
                            <?php echo $edit_member ? 'Update Member' : 'Add Member'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search Form -->
        <div class="search-container fade-in" style="animation-delay: 0.2s;">
            <h3 class="search-title">
                <i class="fas fa-search"></i>
                Search Members
            </h3>
            <form method="GET" action="view_members.php" class="search-form">
                <div class="form-group">
                    <input type="text" name="search_name" class="form-input" placeholder="Search by Name" 
                           value="<?php echo htmlspecialchars($search_name); ?>">
                </div>
                <div class="form-group">
                    <input type="text" name="search_location" class="form-input" placeholder="Search by Location" 
                           value="<?php echo htmlspecialchars($search_location); ?>">
                </div>
                <div class="form-group">
                    <select name="search_department_name" class="form-select">
                        <option value="">All Departments</option>
                        <?php
                        $departments->data_seek(0);
                        while ($row = $departments->fetch_assoc()) : ?>
                            <option value="<?php echo $row['DEPARTMENT_NAME']; ?>" 
                                    <?php if ($search_department_name == $row['DEPARTMENT_NAME']) echo 'selected'; ?>>
                                <?php echo $row['DEPARTMENT_NAME']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <select name="search_employment_status" class="form-select">
                        <option value="">All Employment Status</option>
                        <option value="APPRENTICESHIP" <?php if ($search_employment_status == 'APPRENTICESHIP') echo 'selected'; ?>>Apprenticeship</option>
                        <option value="EMPLOYED" <?php if ($search_employment_status == 'EMPLOYED') echo 'selected'; ?>>Employed</option>
                        <option value="UNEMPLOYED" <?php if ($search_employment_status == 'UNEMPLOYED') echo 'selected'; ?>>Unemployed</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" name="search" class="btn btn-secondary btn-full">
                        <i class="fas fa-search"></i>
                        Search Members
                    </button>
                </div>
            </form>
        </div>

        <!-- Members Table -->
        <div class="table-container fade-in" style="animation-delay: 0.4s;">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="fas fa-user"></i> Name</th>
                        <th><i class="fas fa-venus-mars"></i> Gender</th>
                        <th><i class="fas fa-heart"></i> Marital Status</th>
                        <th><i class="fas fa-map-marker-alt"></i> Location</th>
                        <th><i class="fas fa-phone"></i> Contact</th>
                        <th><i class="fas fa-users"></i> Department</th>
                        <th><i class="fas fa-birthday-cake"></i> Date of Birth</th>
                        <th><i class="fas fa-briefcase"></i> Employment</th>
                        <th><i class="fas fa-cogs"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['NAME']); ?></td>
                                <td>
                                    <span class="badge">
                                        <i class="fas fa-<?php echo $row['SEX'] == 'MALE' ? 'mars' : 'venus'; ?>"></i>
                                        <?php echo htmlspecialchars($row['SEX']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($row['MARITAL_STATUS']); ?></td>
                                <td><?php echo htmlspecialchars($row['LOCATION']); ?></td>
                                <td>
                                    <a href="tel:<?php echo htmlspecialchars($row['CONTACT']); ?>" class="text-primary">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($row['CONTACT']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($row['DEPARTMENT_NAME'])): ?>
                                        <span class="badge-secondary">
                                            <i class="fas fa-users"></i>
                                            <?php echo htmlspecialchars($row['DEPARTMENT_NAME']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['date_of_birth'])): ?>
                                        <i class="fas fa-birthday-cake"></i>
                                        <?php echo date('M d, Y', strtotime($row['date_of_birth'])); ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not provided</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['employment_status'])): ?>
                                        <span class="badge-<?php echo strtolower($row['employment_status']) == 'employed' ? 'success' : (strtolower($row['employment_status']) == 'unemployed' ? 'danger' : 'warning'); ?>">
                                            <i class="fas fa-briefcase"></i>
                                            <?php echo htmlspecialchars($row['employment_status']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not specified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_members.php?edit&name=<?php echo urlencode($row['NAME']); ?>&contact=<?php echo urlencode($row['CONTACT']); ?>" 
                                           class="btn btn-sm btn-warning" title="Edit Member">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view_members.php?delete&name=<?php echo urlencode($row['NAME']); ?>&contact=<?php echo urlencode($row['CONTACT']); ?>" 
                                           class="btn btn-sm btn-danger" title="Delete Member"
                                           onclick="return confirm('Are you sure you want to delete this member?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center" style="padding: var(--spacing-8);">
                                <i class="fas fa-users text-gray-400" style="font-size: 3rem; margin-bottom: var(--spacing-4);"></i>
                                <p class="text-gray-600">No members found matching your criteria.</p>
                                <a href="view_members.php" class="btn btn-primary">
                                    <i class="fas fa-refresh"></i>
                                    View All Members
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (!empty($messages)): ?>
        <div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
            <?php foreach ($messages as $index => $msg): ?>
                <div class="alert alert-<?php echo $msg['type']; ?> fade-in" 
                     style="animation-delay: <?php echo $index * 0.2; ?>s; margin-bottom: var(--spacing-2);" 
                     id="notification-<?php echo $index; ?>">
                    <?php if ($msg['type'] == 'success'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php else: ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($msg['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <style>
        .badge, .badge-secondary, .badge-success, .badge-danger, .badge-warning {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-1);
            padding: var(--spacing-1) var(--spacing-2);
            border-radius: var(--radius-sm);
            font-size: var(--text-xs);
            font-weight: 500;
        }

        .badge {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .badge-secondary {
            background-color: var(--secondary-color);
            color: var(--white);
        }

        .badge-success {
            background-color: var(--success-color);
            color: var(--white);
        }

        .badge-danger {
            background-color: var(--error-color);
            color: var(--white);
        }

        .badge-warning {
            background-color: var(--warning-color);
            color: var(--white);
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-2);
            justify-content: center;
        }

        .text-primary {
            color: var(--primary-color);
            text-decoration: none;
        }

        .text-primary:hover {
            color: var(--primary-hover);
        }

        .text-gray-400 {
            color: var(--gray-400);
            font-style: italic;
        }

        .text-gray-600 {
            color: var(--gray-600);
        }

        /* Auto-hide notifications */
        .alert {
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    </style>

    <script>
        // Auto-hide notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $index => $msg): ?>
                    setTimeout(() => {
                        const notification = document.getElementById('notification-<?php echo $index; ?>');
                        if (notification) {
                            notification.style.animation = 'slideOutRight 0.5s ease-out forwards';
                            setTimeout(() => notification.remove(), 500);
                        }
                    }, <?php echo 3000 + ($index * 500); ?>);
                <?php endforeach; ?>
            <?php endif; ?>
        });

        // Form enhancement
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to buttons
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        const originalHTML = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                        submitBtn.disabled = true;
                        
                        // Re-enable after 3 seconds if form doesn't submit
                        setTimeout(() => {
                            submitBtn.innerHTML = originalHTML;
                            submitBtn.disabled = false;
                        }, 3000);
                    }
                });
            });

            // Add row hover effects
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                    this.style.boxShadow = 'var(--shadow-md)';
                    this.style.transition = 'all 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>
</html>