<?php
// Файл: php/test_connection.php
// Назначение: Проверка подключения к БД и вывод данных из таблицы users

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест подключения к БД</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1 { color: #333; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Тест подключения к базе данных "Корочки.есть"</h1>
    
    <?php if (!$db_conn): ?>
        <div class="error">
            <strong>Ошибка:</strong> Подключение к базе данных отсутствует.
        </div>
    <?php else: ?>
        <div class="success">
            <strong>Успех:</strong> Подключение к базе данных установлено!
        </div>
        
        <?php
        $query = "SELECT id, login, full_name, phone, email, role, created_at FROM users ORDER BY id";
        $result = pg_query($db_conn, $query);
        
        if (!$result): ?>
            <div class="error">
                <strong>Ошибка запроса:</strong> <?php echo pg_last_error($db_conn); ?>
            </div>
        <?php else:
            $rows_count = pg_num_rows($result);
            echo "<p>Найдено пользователей: <strong>$rows_count</strong></p>";
            
            if ($rows_count > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Логин</th>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Дата регистрации</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = pg_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['login']); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Таблица users пуста.</p>
            <?php endif;
            
            pg_free_result($result);
        endif;
        ?>
    <?php endif; ?>
</body>
</html>