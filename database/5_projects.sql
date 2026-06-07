CREATE TABLE projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    url TEXT,
    sort_order INTEGER DEFAULT 0,
    created_at INTEGER DEFAULT (strftime('%s', 'now'))
);

INSERT INTO projects (name, description, url, sort_order) VALUES
('ITDP - Portfolio', 'This portfolio website, built with a custom PHP MVC framework, Twig and Tailwind CSS.', 'https://elmarvanloenhout.nl', 1),
('TurboTaal - Educational game', 'An educational game to help learn 10 year olds the Dutch language, built with Typescript', 'https://hz-ict1-2526.github.io/oop-team02/', 2),
('Program Career Orientation - Website', 'My first website, built with HTML and CSS', 'https://ellemaj.github.io/', 3);
