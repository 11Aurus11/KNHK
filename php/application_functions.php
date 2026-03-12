<?php
// Файл: php/application_functions.php
// Назначение: Функции для работы с заявками (создание, получение, обновление)

require_once 'config.php';

/**
 * Функция создания новой заявки
 */
function createApplication($user_id, $course_name, $start_date, $payment_method_id) {
    global $db_conn;
    
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }
    
    $user_id = (int)$user_id;
    $course_name = pg_escape_string($db_conn, $course_name);
    $start_date = pg_escape_string($db_conn, $start_date);
    $payment_method_id = (int)$payment_method_id;
    $status = 'Новая';
    
    $query = "INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status) 
              VALUES ($user_id, '$course_name', '$start_date', $payment_method_id, '$status')";
    
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при создании заявки: ' . $error];
    }
}

/**
 * Функция получения всех заявок конкретного пользователя
 */
function getUserApplications($user_id) {
    global $db_conn;
    $applications = [];
    
    if (!$db_conn) {
        return $applications;
    }
    
    $user_id = (int)$user_id;
    
    $query = "SELECT 
                a.id, 
                a.course_name, 
                a.desired_start_date, 
                a.status, 
                a.created_at, 
                a.review, 
                pm.name as payment_method_name 
              FROM applications a 
              JOIN payment_methods pm ON a.payment_method_id = pm.id 
              WHERE a.user_id = $user_id 
              ORDER BY a.created_at DESC";
    
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $applications[] = $row;
        }
        pg_free_result($result);
    }
    
    return $applications;
}

/**
 * Функция получения всех заявок (для администратора)
 */
function getAllApplications() {
    global $db_conn;
    $applications = [];
    
    if (!$db_conn) {
        return $applications;
    }
    
    $query = "SELECT 
                a.id, 
                a.course_name, 
                a.desired_start_date, 
                a.status, 
                a.created_at, 
                a.review, 
                u.id as user_id, 
                u.full_name as user_name, 
                u.login as user_login, 
                u.email as user_email, 
                pm.name as payment_method_name 
              FROM applications a 
              JOIN users u ON a.user_id = u.id 
              JOIN payment_methods pm ON a.payment_method_id = pm.id 
              ORDER BY a.created_at DESC";
    
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $applications[] = $row;
        }
        pg_free_result($result);
    }
    
    return $applications;
}

/**
 * Функция получения всех заявок с фильтрацией и пагинацией
 */
function getAllApplicationsFiltered($status_filter = '', $search = '', $page = 1, $items_per_page = 10) {
    global $db_conn;
    $applications = [];
    
    if (!$db_conn) {
        return $applications;
    }
    
    // Базовый запрос
    $query = "SELECT 
                a.id, 
                a.course_name, 
                a.desired_start_date, 
                a.status, 
                a.created_at, 
                a.review, 
                u.id as user_id, 
                u.full_name as user_name, 
                u.login as user_login, 
                u.email as user_email, 
                pm.name as payment_method_name 
              FROM applications a 
              JOIN users u ON a.user_id = u.id 
              JOIN payment_methods pm ON a.payment_method_id = pm.id";
    
    // Добавляем условия фильтрации
    $conditions = [];
    $params = [];
    $param_count = 1;
    
    if (!empty($status_filter)) {
        $conditions[] = "a.status = $" . $param_count;
        $params[] = $status_filter;
        $param_count++;
    }
    
    if (!empty($search)) {
        $conditions[] = "(u.full_name ILIKE $" . $param_count . " OR a.course_name ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }
    
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    // Сортировка
    $query .= " ORDER BY a.created_at DESC";
    
    // Пагинация
    $offset = ($page - 1) * $items_per_page;
    $query .= " LIMIT $" . $param_count . " OFFSET " . $offset;
    $params[] = $items_per_page;
    
    // Выполняем запрос с параметрами
    $result = pg_query_params($db_conn, $query, $params);
    
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $applications[] = $row;
        }
        pg_free_result($result);
    }
    
    return $applications;
}

/**
 * Функция получения общего количества заявок с учетом фильтров
 */
function getTotalApplicationsCount($status_filter = '', $search = '') {
    global $db_conn;
    
    if (!$db_conn) {
        return 0;
    }
    
    $query = "SELECT COUNT(*) as count 
              FROM applications a 
              JOIN users u ON a.user_id = u.id";
    
    // Добавляем условия фильтрации
    $conditions = [];
    $params = [];
    $param_count = 1;
    
    if (!empty($status_filter)) {
        $conditions[] = "a.status = $" . $param_count;
        $params[] = $status_filter;
        $param_count++;
    }
    
    if (!empty($search)) {
        $conditions[] = "(u.full_name ILIKE $" . $param_count . " OR a.course_name ILIKE $" . $param_count . ")";
        $params[] = "%$search%";
        $param_count++;
    }
    
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $result = pg_query_params($db_conn, $query, $params);
    
    if ($result) {
        $row = pg_fetch_assoc($result);
        pg_free_result($result);
        return (int)$row['count'];
    }
    
    return 0;
}

/**
 * Функция обновления статуса заявки
 */
function updateApplicationStatus($application_id, $new_status) {
    global $db_conn;
    
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }
    
    $application_id = (int)$application_id;
    $new_status = pg_escape_string($db_conn, $new_status);
    
    $allowed_statuses = ['Новая', 'Идет обучение', 'Обучение завершено'];
    
    if (!in_array($new_status, $allowed_statuses)) {
        return ['error' => 'Недопустимый статус'];
    }
    
    $query = "UPDATE applications SET status = '$new_status' WHERE id = $application_id";
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при обновлении статуса: ' . $error];
    }
}

/**
 * Функция добавления отзыва к заявке
 */
function addReview($application_id, $review) {
    global $db_conn;
    
    if (!$db_conn) {
        return ['error' => 'Ошибка подключения к базе данных'];
    }
    
    $application_id = (int)$application_id;
    $review = pg_escape_string($db_conn, $review);
    
    $query = "UPDATE applications SET review = '$review' WHERE id = $application_id";
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        return true;
    } else {
        $error = pg_last_error($db_conn);
        return ['error' => 'Ошибка при добавлении отзыва: ' . $error];
    }
}

/**
 * Функция получения всех способов оплаты
 */
function getPaymentMethods() {
    global $db_conn;
    $methods = [];
    
    if (!$db_conn) {
        return $methods;
    }
    
    $query = "SELECT * FROM payment_methods ORDER BY id";
    $result = pg_query($db_conn, $query);
    
    if ($result) {
        while ($row = pg_fetch_assoc($result)) {
            $methods[] = $row;
        }
        pg_free_result($result);
    }
    
    return $methods;
}

/**
 * Функция проверки, может ли пользователь оставить отзыв
 */
function canAddReview($application_id, $user_id) {
    global $db_conn;
    
    if (!$db_conn) {
        return false;
    }
    
    $application_id = (int)$application_id;
    $user_id = (int)$user_id;
    
    $query = "SELECT id FROM applications 
              WHERE id = $application_id 
              AND user_id = $user_id 
              AND status = 'Обучение завершено' 
              AND (review IS NULL OR review = '')";
    
    $result = pg_query($db_conn, $query);
    
    return ($result && pg_num_rows($result) > 0);
}
?>