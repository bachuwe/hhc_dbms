<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Check - HHC DBMS</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .health-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h1 {
            color: #2563eb;
            margin-bottom: 20px;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .status.success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        .status.error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .status.warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        .icon {
            font-size: 24px;
        }
        .info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .timestamp {
            color: #6b7280;
            font-size: 14px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="health-card">
        <h1>🏥 HHC DBMS Health Check</h1>
        
        <?php
        error_reporting(0);
        $checks = [];
        
        // Check database connection
        if (file_exists('db.php')) {
            include 'db.php';
            if (isset($conn) && $conn->ping()) {
                $checks['database'] = [
                    'status' => 'success',
                    'message' => 'Database connection successful',
                    'icon' => '✅'
                ];
            } else {
                $checks['database'] = [
                    'status' => 'error',
                    'message' => 'Database connection failed',
                    'icon' => '❌'
                ];
            }
        } else {
            $checks['database'] = [
                'status' => 'error',
                'message' => 'Database configuration file not found',
                'icon' => '❌'
            ];
        }
        
        // Check PHP version
        $phpVersion = phpversion();
        if (version_compare($phpVersion, '8.0.0', '>=')) {
            $checks['php'] = [
                'status' => 'success',
                'message' => "PHP Version: $phpVersion",
                'icon' => '✅'
            ];
        } else {
            $checks['php'] = [
                'status' => 'warning',
                'message' => "PHP Version: $phpVersion (Recommended: 8.0+)",
                'icon' => '⚠️'
            ];
        }
        
        // Check required files
        $requiredFiles = ['index.php', 'login.php', 'add_member.php', 'view_members.php'];
        $missingFiles = [];
        foreach ($requiredFiles as $file) {
            if (!file_exists($file)) {
                $missingFiles[] = $file;
            }
        }
        
        if (empty($missingFiles)) {
            $checks['files'] = [
                'status' => 'success',
                'message' => 'All required files present',
                'icon' => '✅'
            ];
        } else {
            $checks['files'] = [
                'status' => 'error',
                'message' => 'Missing files: ' . implode(', ', $missingFiles),
                'icon' => '❌'
            ];
        }
        
        // Check session support
        if (function_exists('session_start')) {
            $checks['session'] = [
                'status' => 'success',
                'message' => 'Session support enabled',
                'icon' => '✅'
            ];
        } else {
            $checks['session'] = [
                'status' => 'error',
                'message' => 'Session support not available',
                'icon' => '❌'
            ];
        }
        
        // Check mysqli extension
        if (extension_loaded('mysqli')) {
            $checks['mysqli'] = [
                'status' => 'success',
                'message' => 'MySQLi extension loaded',
                'icon' => '✅'
            ];
        } else {
            $checks['mysqli'] = [
                'status' => 'error',
                'message' => 'MySQLi extension not loaded',
                'icon' => '❌'
            ];
        }
        
        // Display results
        foreach ($checks as $name => $check) {
            echo "<div class='status {$check['status']}'>";
            echo "<span><strong>" . ucfirst($name) . ":</strong> {$check['message']}</span>";
            echo "<span class='icon'>{$check['icon']}</span>";
            echo "</div>";
        }
        ?>
        
        <div class="info">
            <strong>ℹ️ System Information</strong><br>
            Server: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
            Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
            PHP SAPI: <?php echo php_sapi_name(); ?>
        </div>
        
        <div class="timestamp">
            Last checked: <?php echo date('Y-m-d H:i:s'); ?>
        </div>
    </div>
</body>
</html>
