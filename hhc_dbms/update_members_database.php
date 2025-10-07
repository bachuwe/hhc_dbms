<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update - HHC Takoradi</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="mb-0">
                    <i class="fas fa-database"></i>
                    Database Update
                </h1>
                <p class="mb-0">Add new fields to the Members table</p>
            </div>
        </div>
    </div>

    <div class="container-sm" style="padding: var(--spacing-8) var(--spacing-4);">
        <?php if (isset($_POST['update_db'])): ?>
            <!-- Update Results -->
            <div class="card fade-in">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-cogs"></i>
                        Database Update Results
                    </h3>
                </div>
                <div class="card-body">
                    <?php
                    include 'db.php';
                    
                    echo '<div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Starting database update...
                          </div>';
                    
                    // SQL to add new columns to MEMBERS table
                    $sql1 = "ALTER TABLE MEMBERS ADD COLUMN date_of_birth DATE";
                    $sql2 = "ALTER TABLE MEMBERS ADD COLUMN employment_status ENUM('APPRENTICESHIP', 'EMPLOYED', 'UNEMPLOYED')";
                    
                    $success_count = 0;
                    $error_count = 0;
                    
                    // Execute first SQL statement
                    if ($conn->query($sql1) === TRUE) {
                        echo '<div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Successfully added \'date_of_birth\' column to MEMBERS table.
                              </div>';
                        $success_count++;
                    } else {
                        if (strpos($conn->error, "Duplicate column name") !== false) {
                            echo '<div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    \'date_of_birth\' column already exists.
                                  </div>';
                        } else {
                            echo '<div class="alert alert-error">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Error adding \'date_of_birth\' column: ' . $conn->error . '
                                  </div>';
                            $error_count++;
                        }
                    }
                    
                    // Execute second SQL statement
                    if ($conn->query($sql2) === TRUE) {
                        echo '<div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                Successfully added \'employment_status\' column to MEMBERS table.
                              </div>';
                        $success_count++;
                    } else {
                        if (strpos($conn->error, "Duplicate column name") !== false) {
                            echo '<div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    \'employment_status\' column already exists.
                                  </div>';
                        } else {
                            echo '<div class="alert alert-error">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Error adding \'employment_status\' column: ' . $conn->error . '
                                  </div>';
                            $error_count++;
                        }
                    }
                    
                    if ($success_count > 0 && $error_count == 0) {
                        echo '<div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Database update completed successfully!</strong>
                              </div>';
                        echo '<div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                You can now use the updated member forms with the new fields.
                              </div>';
                    } elseif ($error_count > 0) {
                        echo '<div class="alert alert-error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Database update completed with errors.</strong>
                              </div>';
                    } else {
                        echo '<div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Database update completed. No changes were made (columns already exist).</strong>
                              </div>';
                    }
                    
                    $conn->close();
                    ?>
                    
                    <div class="text-center mt-6">
                        <div class="nav-buttons">
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-home"></i>
                                Return to Dashboard
                            </a>
                            <a href="view_members.php" class="btn btn-success">
                                <i class="fas fa-users"></i>
                                View Members
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Update Form -->
            <div class="card fade-in">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-database"></i>
                        Members Table Update
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-6">This script will add the following fields to the MEMBERS table:</p>
                    
                    <div class="grid gap-4 mb-6" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">
                                    <i class="fas fa-birthday-cake text-primary"></i>
                                    Date of Birth
                                </h4>
                                <p class="text-sm text-gray-600">DATE field for storing member birth dates</p>
                                <div class="badge">Optional Field</div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-2">
                                    <i class="fas fa-briefcase text-primary"></i>
                                    Employment Status
                                </h4>
                                <p class="text-sm text-gray-600">ENUM field with values:</p>
                                <ul class="text-sm text-gray-600 mt-2">
                                    <li>• APPRENTICESHIP</li>
                                    <li>• EMPLOYED</li>
                                    <li>• UNEMPLOYED</li>
                                </ul>
                                <div class="badge">Optional Field</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mb-6">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Important:</strong> This will modify your database structure. Make sure you have a backup before proceeding.
                    </div>
                    
                    <form method="POST" action="" class="text-center">
                        <button type="submit" name="update_db" class="btn btn-primary btn-lg" 
                                onclick="return confirm('Are you sure you want to update the database structure?');">
                            <i class="fas fa-database"></i>
                            Update Database
                        </button>
                    </form>
                    
                    <div class="text-center mt-6">
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i>
                            Cancel and Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="card mt-6 fade-in" style="animation-delay: 0.2s;">
                <div class="card-header">
                    <h3 class="mb-0">
                        <i class="fas fa-info-circle"></i>
                        What This Update Does
                    </h3>
                </div>
                <div class="card-body">
                    <div class="grid gap-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                        <div>
                            <h4><i class="fas fa-plus-circle text-success"></i> Adds New Fields</h4>
                            <p class="text-sm text-gray-600">Extends the members table with additional demographic information</p>
                        </div>
                        <div>
                            <h4><i class="fas fa-shield-alt text-info"></i> Safe Operation</h4>
                            <p class="text-sm text-gray-600">Existing data remains unchanged and intact</p>
                        </div>
                        <div>
                            <h4><i class="fas fa-backward text-warning"></i> Backward Compatible</h4>
                            <p class="text-sm text-gray-600">All current functionality continues to work normally</p>
                        </div>
                        <div>
                            <h4><i class="fas fa-edit text-primary"></i> Enhanced Forms</h4>
                            <p class="text-sm text-gray-600">Member forms will include the new fields after update</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add confirmation with detailed information
        document.addEventListener('DOMContentLoaded', function() {
            const updateBtn = document.querySelector('button[name="update_db"]');
            if (updateBtn) {
                updateBtn.addEventListener('click', function(e) {
                    const confirmed = confirm(
                        'Database Update Confirmation\n\n' +
                        'This will add two new columns to your MEMBERS table:\n' +
                        '• date_of_birth (DATE)\n' +
                        '• employment_status (ENUM)\n\n' +
                        'This operation is safe and will not affect existing data.\n\n' +
                        'Do you want to proceed?'
                    );
                    
                    if (confirmed) {
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating Database...';
                        this.disabled = true;
                    } else {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
