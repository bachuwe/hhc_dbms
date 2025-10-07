<?php
include 'db.php'; // Include your database connection file

// SQL to add new columns to MEMBERS table
$sql1 = "ALTER TABLE MEMBERS ADD COLUMN date_of_birth DATE";
$sql2 = "ALTER TABLE MEMBERS ADD COLUMN employment_status ENUM('APPRENTICESHIP', 'EMPLOYED', 'UNEMPLOYED')";

echo "Starting database update...\n";

// Execute first SQL statement
if ($conn->query($sql1) === TRUE) {
    echo "Successfully added 'date_of_birth' column to MEMBERS table.\n";
} else {
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "'date_of_birth' column already exists.\n";
    } else {
        echo "Error adding 'date_of_birth' column: " . $conn->error . "\n";
    }
}

// Execute second SQL statement
if ($conn->query($sql2) === TRUE) {
    echo "Successfully added 'employment_status' column to MEMBERS table.\n";
} else {
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "'employment_status' column already exists.\n";
    } else {
        echo "Error adding 'employment_status' column: " . $conn->error . "\n";
    }
}

echo "Database update completed.\n";

$conn->close();
?>
