<?php
include 'db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'yourdomain.com',
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

$edit_mode = false;
$edit_department_id = '';
$edit_department_name = '';
$edit_leader = '';

$messages = [];

// Handle form submission for adding/editing a department
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_department'])) {
        $department_name = strtoupper(trim($_POST['department_name']));
        $leader = strtoupper(trim($_POST['leader']));
        
        $sql = "INSERT INTO DEPARTMENTS (DEPARTMENT_NAME, LEADER) VALUES ('$department_name', '$leader')";
        if ($conn->query($sql) === TRUE) {
            $messages[] = ['message' => 'Department added successfully!', 'type' => 'success'];
        } else {
            $messages[] = ['message' => 'Error adding department: ' . $conn->error, 'type' => 'error'];
        }
    } elseif (isset($_POST['edit_department'])) {
        $department_id = $_POST['department_id'];
        $department_name = strtoupper(trim($_POST['department_name']));
        $leader = strtoupper(trim($_POST['leader']));
        
        $sql = "UPDATE DEPARTMENTS SET DEPARTMENT_NAME='$department_name', LEADER='$leader' WHERE DEPARTMENT_ID='$department_id'";
        if ($conn->query($sql) === TRUE) {
            $messages[] = ['message' => 'Department updated successfully!', 'type' => 'success'];
        } else {
            $messages[] = ['message' => 'Error updating department: ' . $conn->error, 'type' => 'error'];
        }
    }
}

// Handle delete department
if (isset($_GET['delete'])) {
    $department_id = $_GET['delete'];
    if ($conn->query("DELETE FROM DEPARTMENTS WHERE DEPARTMENT_ID='$department_id'") === TRUE) {
        $messages[] = ['message' => 'Department deleted successfully!', 'type' => 'success'];
    } else {
        $messages[] = ['message' => 'Error deleting department: ' . $conn->error, 'type' => 'error'];
    }
}

// Handle edit department
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $department_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM DEPARTMENTS WHERE DEPARTMENT_ID='$department_id'");
    $department = $result->fetch_assoc();
    $edit_department_id = $department['DEPARTMENT_ID'];
    $edit_department_name = $department['DEPARTMENT_NAME'];
    $edit_leader = $department['LEADER'];
}

// Fetch all departments
$result = $conn->query("SELECT * FROM DEPARTMENTS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            margin: 5px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }
        form {
            margin: 20px 0;
        }
        input[type="text"] {
            padding: 10px;
            margin: 5px;
            width: calc(100% - 22px);
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translate(-50%, -20px);
            padding: 15px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            min-width: 250px;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease, transform 0.5s ease;
            z-index: 1000;
        }
        .notification.show {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, 0);
        }
        .notification-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .notification-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 600px) {
            input[type="text"] {
                width: calc(100% - 20px);
            }
            .actions {
                flex-direction: column;
            }
        }
         @media (max-width: 465px){
             table{
                 width:95%;
                 font-size:7px;
                 overflow-x:hidden;
             }
         }
    </style>
</head>
<body>

<h1>Departments</h1>

<p><a href="index.php" class="button">Back to Home</a></p>

<form method="POST" action="view_departments.php">
    <input type="hidden" name="department_id" value="<?php echo $edit_department_id; ?>">
    <input type="text" name="department_name" placeholder="Department Name" required value="<?php echo htmlspecialchars($edit_department_name); ?>">
    <input type="text" name="leader" placeholder="Leader" required value="<?php echo htmlspecialchars($edit_leader); ?>">
    <input type="submit" name="<?php echo $edit_mode ? 'edit_department' : 'add_department'; ?>" value="<?php echo $edit_mode ? 'Update Department' : 'Add Department'; ?>" class="button">
</form>

<div style="overflow-x:auto;">
    <table>
        <tr>
            <th>Department ID</th>
            <th>Department Name</th>
            <th>Leader</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['DEPARTMENT_ID']; ?></td>
                <td><?php echo $row['DEPARTMENT_NAME']; ?></td>
                <td><?php echo $row['LEADER']; ?></td>
                <td class="actions">
                    <a href="view_departments.php?edit=<?php echo $row['DEPARTMENT_ID']; ?>" class="button">Edit</a>
                    <a href="view_departments.php?delete=<?php echo $row['DEPARTMENT_ID']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this department?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<div id="notification" class="notification"></div>

<script>
function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = 'notification show notification-' + type;

    setTimeout(() => {
        notification.classList.remove('show');
    }, 3500);
}

<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $msg): ?>
          showNotification('<?php echo addslashes($msg['message']); ?>', '<?php echo $msg['type']; ?>');
    <?php endforeach; ?>
<?php endif; ?>
</script>

</body>
</html>