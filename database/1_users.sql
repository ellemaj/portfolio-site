CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    name TEXT,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, name, email, password, role)
VALUES (
    'ellemaj',
    'Elmar van Loenhout',
    'elmarvloenhout@gmail.com',
    '$2y$12$YXS2yo0p.830i3eavIzGuunRsHD96XasjyfeTgR7/.rlE7VHZVCVK',
    'admin'
);