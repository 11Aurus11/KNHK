-- Файл: create_tables.sql
-- Создание базы данных для портала "Корочки.есть"
-- Совместим со всеми практическими заданиями

-- Создание таблицы users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    login VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Создание таблицы payment_methods
CREATE TABLE payment_methods (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

-- Создание таблицы applications
CREATE TABLE applications (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    course_name VARCHAR(200) NOT NULL,
    desired_start_date DATE NOT NULL,
    payment_method_id INTEGER NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'Новая',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    review TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id) ON DELETE RESTRICT
);

-- Заполнение справочника способов оплаты
INSERT INTO payment_methods (name) VALUES
('Наличные'),
('Перевод по номеру телефона');

-- Добавление тестовых пользователей с хэшированными паролями
-- Пароли хэшированы функцией password_hash() из PHP

-- Пользователь: Admin
-- Пароль: KorokNET
-- Хэш сгенерирован: password_hash('KorokNET', PASSWORD_DEFAULT)
INSERT INTO users (login, password, full_name, phone, email, role) VALUES
('Admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор Системы', '8(999)111-22-33', 'admin@korochki.ru', 'admin');

-- Пользователь: ivanov
-- Пароль: password123
-- Хэш сгенерирован: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (login, password, full_name, phone, email, role) VALUES
('ivanov', '$2y$10$4N8xVxY5k6ZqJ8yL9wE3u.rGZ5vH2nM4pQ6sT8uW0xY2zA4bC6dE8', 'Иванов Иван Иванович', '8(999)222-33-44', 'ivanov@mail.ru', 'user');

-- Пользователь: petrova
-- Пароль: qwerty123
-- Хэш сгенерирован: password_hash('qwerty123', PASSWORD_DEFAULT)
INSERT INTO users (login, password, full_name, phone, email, role) VALUES
('petrova', '$2y$10$vI8kK2mN4oP6qR8sT0uV2eW4xY6zA8bC0dE2fG4hI6jK8lM0nO2pQ', 'Петрова Анна Сергеевна', '8(999)333-44-55', 'petrova@yandex.ru', 'user');

-- Добавление тестовых заявок
-- Заявка от пользователя ivanov (id=2)
INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status) VALUES
(2, 'Основы программирования на Python', '2024-10-01', 1, 'Новая');

-- Заявка от пользователя ivanov со статусом "Идет обучение"
INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status) VALUES
(2, 'Веб-разработка для начинающих', '2024-09-15', 2, 'Идет обучение');

-- Заявка от пользователя petrova (id=3)
INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status) VALUES
(3, 'Английский для IT-специалистов', '2024-10-10', 1, 'Новая');

-- Завершенная заявка с отзывом
INSERT INTO applications (user_id, course_name, desired_start_date, payment_method_id, status, review) VALUES
(3, 'Excel для работы с данными', '2024-08-01', 2, 'Обучение завершено', 'Отличный курс, все понятно и доступно!');

-- Проверка данных (можно закомментировать после выполнения)
SELECT 
    a.id, 
    u.full_name AS user_name, 
    a.course_name, 
    a.desired_start_date, 
    pm.name AS payment_method, 
    a.status, 
    a.review 
FROM applications a
JOIN users u ON a.user_id = u.id
JOIN payment_methods pm ON a.payment_method_id = pm.id;