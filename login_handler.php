<?php
// Файл: login_handler.php
session_start();
require_once 'php/user_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($login) || empty($password)) {
        $_SESSION['login_error'] = 'Заполните все поля формы';
        header('Location: login.php');
        exit();
    }
    
    $user = findUserByLogin($login);
    
    if (!$user) {
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: login.php');
        exit();
    }
    
    // Проверка пароля
    if (password_verify($password, $user['password'])) {
        // Для администратора проверяем специальный пароль из задания
        if ($login === 'Admin' && $password !== 'KorokNET') {
            $_SESSION['login_error'] = 'Неверный пароль для администратора';
            header('Location: login.php');
            exit();
        }
        
        // Сохранение данных в сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        
        header('Location: my_applications.php');
        exit();
    } else {
        $_SESSION['login_error'] = 'Неверный логин или пароль';
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>