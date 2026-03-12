<?php
// Файл: register.php
session_start();
$page_title = 'Регистрация - Корочки.есть';

$errors = $_SESSION['registration_errors'] ?? [];
$old_data = $_SESSION['old_data'] ?? [];

unset($_SESSION['registration_errors']);
unset($_SESSION['old_data']);

include 'includes/header.php';
?>
<html>
    <head>
        <link rel="stylesheet" href="./css/style.css">
    </head>
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <h1 class="text-center mb-4">Регистрация</h1>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form action="register_handler.php" method="POST">
                    <div class="form-group">
                        <label for="login">Логин</label>
                        <input type="text" class="form-control" id="login" name="login" required 
                            placeholder="Только латиница и цифры, не менее 6 символов"
                            value="<?php echo htmlspecialchars($old_data['login'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                            placeholder="Минимум 8 символов">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Подтверждение пароля</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                            placeholder="Повторите пароль">
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">ФИО</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required 
                            placeholder="Иванов Иван Иванович"
                            value="<?php echo htmlspecialchars($old_data['full_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required 
                            placeholder="8(999)123-45-67"
                            value="<?php echo htmlspecialchars($old_data['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                            placeholder="example@mail.ru"
                            value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Зарегистрироваться
                    </button>
                    
                    <p class="text-center mb-0">
                        Уже есть аккаунт? <a href="login.php">Войти</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</html>