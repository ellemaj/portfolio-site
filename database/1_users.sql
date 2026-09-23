CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login BIGINT NULL,
    deleted_at BIGINT NULL
);

INSERT INTO users (firstName, lastName, email, password, role, created_at, last_login, deleted_at)
VALUES (
    'Elmar',
    'van Loenhout',
    'elmarvloenhout@gmail.com',
    '$2y$12$YXS2yo0p.830i3eavIzGuunRsHD96XasjyfeTgR7/.rlE7VHZVCVK',
    'admin',
    CURRENT_TIMESTAMP,
    UNIX_TIMESTAMP(),
    NULL
);