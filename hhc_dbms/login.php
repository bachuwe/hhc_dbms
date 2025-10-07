<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HHC Takoradi DBMS</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(120deg); }
            66% { transform: translate(-20px, 20px) rotate(240deg); }
        }
        
        .login-wrapper {
            position: relative;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container fade-in">
            <div class="login-header">
                <div class="text-center mb-6">
                    <div class="stat-icon" style="font-size: 3rem; color: var(--primary-color); margin-bottom: var(--spacing-4);">
                        <i class="fas fa-church"></i>
                    </div>
                    <h1 class="mb-2">HHC Takoradi</h1>
                    <p class="text-gray-600">Database Management System</p>
                </div>
            </div>

            <form method="POST" action="process_login.php">
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" placeholder="Username" class="form-input input-with-icon" required>
                </div>
                
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" placeholder="Password" class="form-input input-with-icon" required>
                </div>
                
                <div class="form-group mt-6">
                    <button type="submit" class="btn btn-primary btn-full btn-lg">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </div>

                <div class="text-center mt-6">
                    <p class="text-gray-600">
                        <i class="fas fa-shield-alt"></i>
                        Secure Login Portal
                    </p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add ripple effect to login button
        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            const button = e.target;
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.pointerEvents = 'none';
            
            button.style.position = 'relative';
            button.style.overflow = 'hidden';
            button.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
        
        // Add CSS animation for ripple
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
