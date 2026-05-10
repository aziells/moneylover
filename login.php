<?php
session_start();



if (isset($_SESSION['user_id'])) {
    if ($_SESSION['level'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: kasir/index.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MoneyLover</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #D2B48C;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        .card {
            width: 100%;
        }
        .btn-primary, 
    button[type="submit"], 
    .btn.btn-block.btn-primary {
        background-color: #8B4513 !important; 
        background-image: none !important;
        border-color: #654321 !important;
        color: #ffffff !important;
    }

    .btn-primary:hover {
        background-color: #654321 !important; 
    }

    .form-control:focus {
        border-color: #8B4513 !important;
        box-shadow: 0 0 0 0.2rem rgba(139, 69, 19, 0.25) !important;
    }

    .card-primary.card-outline {
        border-top: 3px solid #8B4513 !important;
    }
   
    .card, 
    .login-box,
    .login-card-body {
        border-radius: 25px !important; 
        overflow: hidden !important; 
        border: none !important;
    }

    .btn-primary, 
    button[type="submit"] {
        border-radius: 20px !important; 
        padding: 10px 25px !important;
        background-color: #8B4513 !important; 
    }

    .form-control {
        border-radius: 15px !important; 
        border: 1px solid #D2B48C !important;
    }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">MoneyLover</h3>
                        
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger">
                                <?php 
                                    echo $_SESSION['error']; 
                                    unset($_SESSION['error']);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="proses/proses_login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required autofocus>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
