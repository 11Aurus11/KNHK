<?php
// Файл: login.php
session_start();
$page_title = 'Вход - Корочки.есть';

$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['registration_success'] ?? '';

unset($_SESSION['login_error']);
unset($_SESSION['registration_success']);

include 'includes/header.php';
?>
<html>
    <head>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card">
                <h1 class="text-center mb-4">Вход в систему</h1>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form action="login_handler.php" method="POST">
                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" class="form-control" id="login" name="login" required 
                            placeholder="Введите ваш логин">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                            placeholder="Введите ваш пароль">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt"></i> Войти
                    </button>
                    
                    <p class="text-center mb-0">
                        Еще не зарегистрированы? <a href="register.php">Создать аккаунт</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>