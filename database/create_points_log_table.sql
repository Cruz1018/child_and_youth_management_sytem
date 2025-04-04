CREATE TABLE points_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action ENUM('add', 'deduct') NOT NULL,
    points INT NOT NULL,
    reason TEXT NOT NULL,
    date_range VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id)
);
