CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    firstName TEXT NOT NULL,
    lastName TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login INTEGER,
    deleted_at INTEGER
);

INSERT INTO users (firstName, lastName, email, password, role, created_at, last_login, deleted_at)
VALUES (
    'Elmar',
    'van Loenhout',
    'elmarvloenhout@gmail.com',
    '$2y$12$YXS2yo0p.830i3eavIzGuunRsHD96XasjyfeTgR7/.rlE7VHZVCVK',
    'admin',
    strftime('%s', 'now'),
    strftime('%s', 'now'),
    strftime('%s', 'now')
);