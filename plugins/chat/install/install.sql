-- plugins/chat/install/install.sql
-- Schema initiala / actualizata pentru pluginul chat

CREATE TABLE IF NOT EXISTS chat_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (username)
);

CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (created_at),
    FOREIGN KEY (user_id) REFERENCES chat_users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS chat_bans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    ip VARCHAR(45) NULL,
    reason VARCHAR(255) NULL,
    banned_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    INDEX (user_id),
    INDEX (ip)
);

CREATE TABLE IF NOT EXISTS chat_moderation_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50) NOT NULL,
    target_id INT NULL,
    target_type VARCHAR(50) NULL,
    actor_id INT NULL,
    details TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);