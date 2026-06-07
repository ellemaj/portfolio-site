CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    url VARCHAR(500) NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO projects (name, description, url, sort_order) VALUES
('ITDP — Portfolio', 'This portfolio website, built with a custom PHP MVC framework, Twig and Tailwind CSS.', 'https://elmarvanloenhout.nl', 1),
('TurboTaal — Educational game', 'An educational game to help learn 10 year olds the Dutch language, built with Typescript', 'https://hz-ict1-2526.github.io/oop-team02/', 2),
('Program Career Orientation — Website', 'My first website, built with HTML and CSS', 'https://ellemaj.github.io/', 3);
