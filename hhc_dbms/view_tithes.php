<?php
include 'db.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
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
$edit_entry = '';
$edit_name = '';
$edit_contact = '';
$edit_amount = '';
$edit_date = '';
$messages = [];

// Handle form submission for adding/editing a tithe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_tithe'])) {
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];

        $stmt = $conn->prepare("INSERT INTO TITHES (NAME, CONTACT, AMOUNT, DATE) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $contact, $amount, $date);

        if ($stmt->execute()) {
            $messages[] = ['message' => 'Tithe record added successfully!', 'type' => 'success'];
        } else {
            $messages[] = ['message' => 'Error adding tithe record: ' . $stmt->error, 'type' => 'error'];
        }
    } elseif (isset($_POST['edit_tithe'])) {
        $entry = $_POST['entry'];
        $name = $_POST['name'];
        $contact = $_POST['contact'];
        $amount = $_POST['amount'];
        $date = $_POST['date'];

        $stmt = $conn->prepare("UPDATE TITHES SET NAME=?, CONTACT=?, AMOUNT=?, DATE=? WHERE ENTRY=?");
        $stmt->bind_param("ssdss", $name, $contact, $amount, $date, $entry);

        if ($stmt->execute()) {
            $messages[] = ['message' => 'Tithe record updated successfully!', 'type' => 'success'];
        } else {
            $messages[] = ['message' => 'Error updating tithe record: ' . $stmt->error, 'type' => 'error'];
        }
    }
}

// Handle delete tithe
if (isset($_GET['delete'])) {
    $entry = $_GET['delete'];
    if ($conn->query("DELETE FROM TITHES WHERE ENTRY='$entry'")) {
        $messages[] = ['message' => 'Tithe record deleted successfully!', 'type' => 'success'];
    } else {
        $messages[] = ['message' => 'Error deleting tithe record: ' . $conn->error, 'type' => 'error'];
    }
}

// Handle edit tithe
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $entry = $_GET['edit'];
    $result = $conn->query("SELECT * FROM TITHES WHERE ENTRY='$entry'");
    if ($result) {
        $tithe = $result->fetch_assoc();
        $edit_entry = $tithe['ENTRY'];
        $edit_name = $tithe['NAME'];
        $edit_contact = $tithe['CONTACT'];
        $edit_amount = $tithe['AMOUNT'];
        $edit_date = $tithe['DATE'];
    }
}

// Fetch all tithes
$result = $conn->query("SELECT * FROM TITHES");

// Fetch all members for the dropdown
$members = $conn->query("SELECT NAME, CONTACT FROM MEMBERS");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tithes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            max-width: 1800px;
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
        }
        form {
            margin: 10px 0;
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        input[type="number"], input[type="text"], input[type="date"], select {
            padding: 10px;
            margin: 5px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .amount-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .amount-container input {
            flex: 1;
            min-width:70px;
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

       @media (max-width: 465px) {
           body{
               font-size:12px;
           }
         form {
                 flex-direction: column;
                height: 200px;
                
                font-size:5px;
              }
        table{
            max-width:95%;
            font-size:7px;
        }
        input[type="number"], input[type="text"], input[type="date"], select {
            
            font-size:7px;
        }
        .actions {
            
            gap: 2px;
        }
             
}

        @media (max-width: 768px){
            form{
                flex-direction: column;
                height: 200px;
            }
        }
       
    </style>
    <script>
        function updateContact() {
            const memberSelect = document.getElementById('memberSelect');
            const contactInput = document.getElementById('contactInput');
            const selectedMember = memberSelect.options[memberSelect.selectedIndex];

            contactInput.value = selectedMember.getAttribute('data-contact');
        }

        function showNotification(message, type) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = 'notification show notification-' + type;

            setTimeout(() => {
                notification.classList.remove('show');
            }, 3500);
        }
    </script>
</head>
<body>

<h1>Tithes</h1>

<p><a href="index.php" class="button">Back to Home</a></p>

<form method="POST" action="view_tithes.php">
    <input type="hidden" name="entry" value="<?php echo $edit_entry; ?>">
    
    <select name="name" id="memberSelect" onchange="updateContact()" required>
        <option value="">Select Member</option>
        <?php while ($member = $members->fetch_assoc()) : ?>
            <option value="<?php echo htmlspecialchars($member['NAME']); ?>" data-contact="<?php echo htmlspecialchars($member['CONTACT']); ?>" <?php if ($edit_name == $member['NAME']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($member['NAME']); ?>
            </option>
        <?php endwhile; ?>
    </select>
    
    <input type="text" id="contactInput" name="contact" placeholder="Contact" required value="<?php echo htmlspecialchars($edit_contact); ?>" readonly>
    
    <div class="amount-container">
        <input type="number" name="amount" placeholder="Amount" required value="<?php echo htmlspecialchars($edit_amount); ?>" step="0.01">
        <span>GHC</span>
    </div>
    
    <input type="date" name="date" required value="<?php echo htmlspecialchars($edit_date); ?>">
    <input type="submit" name="<?php echo $edit_mode ? 'edit_tithe' : 'add_tithe'; ?>" value="<?php echo $edit_mode ? 'Update Tithe' : 'Add Tithe'; ?>" class="button">
</form>

<div style="overflow-x:auto;">
    <table>
        <tr>
            <th>Entry</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Amount (GHC)</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['ENTRY']; ?></td>
                <td><?php echo htmlspecialchars($row['NAME']); ?></td>
                <td><?php echo htmlspecialchars($row['CONTACT']); ?></td>
                <td> GHC <?php echo $row['AMOUNT']; ?></td>
                <td><?php echo $row['DATE']; ?></td>
                <td class="actions">
                    <a href="view_tithes.php?edit=<?php echo $row['ENTRY']; ?>" class="button">Edit</a>
                    <a href="view_tithes.php?delete=<?php echo $row['ENTRY']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this tithe?');">Delete</a>
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