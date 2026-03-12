<?php
// Файл: php/user_functions.php
// Назначение: Функции для работы с пользователями (регистрация, поиск)

require_once 'config.php';

/**
 * Функция регистрации нового пользователя
 */
function registerUser($login, $password, $full_name, $phone, $email) {
    global $db_conn;
    
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }
    
    // Хешируем пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Экранируем специальные символы для безопасности
    $login = pg_escape_string($db_conn, $login);
    $full_name = pg_escape_string($db_conn, $full_name);
    $phone = pg_escape_string($db_conn, $phone);
    $email = pg_escape_string($db_conn, $email);
    
    // Формируем SQL-запрос для вставки нового пользователя
    $query = "INSERT INTO users (login, password, full_name, phone, email, role) 
              VALUES ('$login', '$hashed_password', '$full_name', '$phone', '$email', 'user')";
    
    // Выполняем запрос
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        if (strpos($error, 'duplicate key') !== false && strpos($error, 'users_login_key') !== false) {
            return ['error' => 'Пользователь с таким логином уже существует'];
        }
        return ['error' => 'Ошибка при регистрации: ' . $error];
    }
}

/**
 * Функция поиска пользователя по логину
 */
function findUserByLogin($login) {
    global $db_conn;
    
    if (!$db_conn) {
        return false;
    }
    
    $login = pg_escape_string($db_conn, $login);
    $query = "SELECT * FROM users WHERE login='$login'";
    $result = pg_query($db_conn, $query);
    
    if ($result && pg_num_rows($result) > 0) {
        return pg_fetch_assoc($result);
    }
    
    return false;
}

/**
 * Функция проверки существования пользователя по логину
 */
function userExists($login) {
    global $db_conn;
    
    if (!$db_conn) {
        return false;
    }
    
    $login = pg_escape_string($db_conn, $login);
    $query = "SELECT id FROM users WHERE login='$login'";
    $result = pg_query($db_conn, $query);
    
    return ($result && pg_num_rows($result) > 0);
}
?>