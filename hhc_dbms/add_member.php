<?php
include 'db.php'; // Include your database connection file
session_start();

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize message variables
$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_member'])) {
    $name = strtoupper(trim($_POST['name']));
    $contact = trim($_POST['contact']);
    $sex = strtoupper(trim($_POST['sex']));
    $marital_status = strtoupper(trim($_POST['marital_status']));
    $location = strtoupper(trim($_POST['location']));
    $department_name = $_POST['department_name'] ?: null;
    $date_of_birth = $_POST['date_of_birth'] ?: null;
    $employment_status = $_POST['employment_status'] ?: null;

    // Validate input
    if (empty($name) || empty($contact)) {
        $message = "Name and contact are required.";
        $message_type = "error";
    } else {
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO MEMBERS (NAME, SEX, MARITAL_STATUS, LOCATION, CONTACT, DEPARTMENT_NAME, date_of_birth, employment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $sex, $marital_status, $location, $contact, $department_name, $date_of_birth, $employment_status);

        if ($stmt->execute()) {
            $message = "Form submitted successfully!";
            $message_type = "success";
        } else {
            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Member - HHC Takoradi</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <img src="hhctak.jpg" alt="HHC Takoradi Logo" class="logo" width="350">
                <h1 class="text-center mb-0">Add New Member</h1>
                <p class="text-center mb-0">Please fill the form below to register a new member</p>
            </div>
        </div>
    </div>

    <div class="container-sm" style="padding: var(--spacing-8) var(--spacing-4);">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> fade-in">
                <?php if ($message_type == 'success'): ?>
                    <i class="fas fa-check-circle"></i>
                <?php else: ?>
                    <i class="fas fa-exclamation-triangle"></i>
                <?php endif; ?>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-container fade-in">
            <div class="card-header">
                <h3 class="mb-0 text-center">
                    <i class="fas fa-user-plus"></i>
                    Member Registration Form
                </h3>
            </div>

            <form method="POST" action="add_member.php">
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i> Full Name <span class="required">*</span>
                    </label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="Enter full name" required>
                </div>

                <div class="form-group">
                    <label for="contact" class="form-label">
                        <i class="fas fa-phone"></i> Contact Number <span class="required">*</span>
                    </label>
                    <input type="tel" id="contact" name="contact" class="form-input" placeholder="Enter contact number" required>
                </div>

                <div class="form-group">
                    <label for="sex" class="form-label">
                        <i class="fas fa-venus-mars"></i> Gender <span class="required">*</span>
                    </label>
                    <select id="sex" name="sex" class="form-select" required>
                        <option value="">Select Gender</option>
                        <option value="MALE">Male</option>
                        <option value="FEMALE">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="marital_status" class="form-label">
                        <i class="fas fa-heart"></i> Marital Status <span class="required">*</span>
                    </label>
                    <select id="marital_status" name="marital_status" class="form-select" required>
                        <option value="">Select Marital Status</option>
                        <option value="SINGLE">Single</option>
                        <option value="MARRIED">Married</option>
                        <option value="DIVORCED">Divorced</option>
                        <option value="WIDOWED">Widowed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="location" class="form-label">
                        <i class="fas fa-map-marker-alt"></i> Location <span class="required">*</span>
                    </label>
                    <input type="text" id="location" name="location" class="form-input" placeholder="Enter location/address" required>
                </div>

                <div class="form-group">
                    <label for="department_name" class="form-label">
                        <i class="fas fa-users"></i> Department
                    </label>
                    <select id="department_name" name="department_name" class="form-select">
                        <option value="">Select Department</option>
                        <option value="HR">HR</option>
                        <option value="IT">IT</option>
                        <option value="Sales">Sales</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">
                        <i class="fas fa-birthday-cake"></i> Date of Birth
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-input">
                </div>

                <div class="form-group">
                    <label for="employment_status" class="form-label">
                        <i class="fas fa-briefcase"></i> Employment Status
                    </label>
                    <select id="employment_status" name="employment_status" class="form-select">
                        <option value="">Select Employment Status</option>
                        <option value="APPRENTICESHIP">Apprenticeship</option>
                        <option value="EMPLOYED">Employed</option>
                        <option value="UNEMPLOYED">Unemployed</option>
                    </select>
                </div>

                <div class="form-group mt-6">
                    <button type="submit" name="add_member" class="btn btn-primary btn-full btn-lg">
                        <i class="fas fa-user-plus"></i>
                        Add Member
                    </button>
                </div>

                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-home"></i>
                        Back to Dashboard
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select');
            
            // Add real-time validation
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.style.borderColor = 'var(--error-color)';
                        this.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
                    } else {
                        this.style.borderColor = 'var(--success-color)';
                        this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
                    }
                });
                
                input.addEventListener('focus', function() {
                    this.style.borderColor = 'var(--primary-color)';
                    this.style.boxShadow = '0 0 0 3px rgba(37, 99, 235, 0.1)';
                });
            });
            
            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Member...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>