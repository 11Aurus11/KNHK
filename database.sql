
CREATE TABLE users (
id SERIAL PRIMARY KEY,
full_name TEXT,
email TEXT,
login TEXT,
password TEXT,
role TEXT
);

CREATE TABLE payment_methods (
id SERIAL PRIMARY KEY,
name TEXT
);

INSERT INTO payment_methods(name) VALUES
('Карта'),
('Наличные'),
('Онлайн');

CREATE TABLE applications (
id SERIAL PRIMARY KEY,
user_id INT REFERENCES users(id),
course_name TEXT,
desired_start_date DATE,
payment_method_id INT REFERENCES payment_methods(id),
status TEXT,
review TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
