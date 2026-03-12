<?php
// Файл: admin/update_status.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../php/application_functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? 0;
    $new_status = $_POST['status'] ?? '';
    
    if (!$application_id || empty($new_status)) {
        $_SESSION['admin_error'] = 'Не указан ID заявки или статус';
        header('Location: dashboard.php');
        exit();
    }
    
    $result = updateApplicationStatus($application_id, $new_status);
    
    if ($result === true) {
        $_SESSION['admin_success'] = "Статус заявки #$application_id успешно изменен на '$new_status'";
    } else {
        $_SESSION['admin_error'] = $result['error'];
    }
    
    // Сохраняем текущие параметры фильтрации для возврата
    $params = [];
    if (!empty($_GET['status'])) $params[] = 'status=' . urlencode($_GET['status']);
    if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
    if (!empty($_GET['page'])) $params[] = 'page=' . $_GET['page'];
    
    $redirect = 'dashboard.php';
    if (!empty($params)) {
        $redirect .= '?' . implode('&', $params);
    }
    
    header('Location: ' . $redirect);
    exit();
} else {
    header('Location: dashboard.php');
    exit();
}
?>