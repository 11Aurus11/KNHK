<?php
// Файл: includes/header.php
// Назначение: Общий шаблон шапки сайта

// Определяем текущую страницу для подсветки активного пункта меню
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Корочки.есть'; ?></title>
    
    <!-- Bootstrap 5 CSS (через CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Наши собственные стили -->
    <link rel="stylesheet" href="/korochki_project/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-graduation-cap"></i> Корочки.<span>есть</span>
                </a>
                
                <nav class="nav-menu">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Главная
                        </a>
                        <a href="my_applications.php" class="<?php echo $current_page === 'my_applications.php' ? 'active' : ''; ?>">
                            <i class="fas fa-list"></i> Мои заявки
                        </a>
                        <a href="new_application.php" class="<?php echo $current_page === 'new_application.php' ? 'active' : ''; ?>">
                            <i class="fas fa-plus-circle"></i> Новая заявка
                        </a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <a href="admin/dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>">
                                <i class="fas fa-cog"></i> Админ панель
                            </a>
                        <?php endif; ?>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Выход
                        </a>
                    <?php else: ?>
                        <a href="index.php" class="<?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                            <i class="fas fa-home"></i> Главная
                        </a>
                        <a href="login.php" class="<?php echo $current_page === 'login.php' ? 'active' : ''; ?>">
                            <i class="fas fa-sign-in-alt"></i> Вход
                        </a>
                        <a href="register.php" class="<?php echo $current_page === 'register.php' ? 'active' : ''; ?>">
                            <i class="fas fa-user-plus"></i> Регистрация
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">