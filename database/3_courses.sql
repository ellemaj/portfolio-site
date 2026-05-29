CREATE TABLE courses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    blok TEXT NOT NULL,
    name TEXT NOT NULL,
    ec REAL NOT NULL,
    exam_type TEXT NOT NULL,
    grade REAL,
    created_at INTEGER DEFAULT (strftime('%s', 'now'))
);

INSERT INTO courses (blok, name, ec, exam_type, grade) VALUES
('1', 'Program- & Career Orientation', 2.5, 'Presentatie', 7.8),
('1', 'Computer Science Basics', 5, 'Schriftelijke Kennistoets', 6.5),
('1', 'Programming Basics', 5, 'Casustoets', 8.3),
('2', 'Object-Oriented Programming', 5, 'Presentatie', 7.7),
('2', 'Object-Oriented Programming', 5, 'Schriftelijke Kennistoets', 9.8),
('3', 'Framework Project 1', 5, 'On-site Casustoets', 5.6),
('3', 'Framework Project 1', 2.5, 'Groepspresentatie project resultaat', 8.1),
('3', 'Framework Project 1', 2.5, 'Groepsportfolio op requirements', null),
('3', 'Business Understanding Basics', 2.5, 'Video', null),
('4', 'Framework Project 2', 5, 'IT-ontwikkelingsportfolio', null),
('4', 'Framework Project 2', 2.5, 'Final Delivery', null),
('4', 'Framework Project 2', 2.5, 'Individueel Project Assessment', null),
('jaar', 'IT Personality 1', 1.25, 'Portfolio', null),
('jaar', 'IT Personality 2', 1.25, 'Portfolio', null),
('jaar', 'IT Personality International Week', 1.25, 'Portfolio', null),
('jaar', 'IT Personality Projectweek 1', 1.25, 'Portfolio', null),
('jaar', 'Personal Professional Development Exploration', 12.5, 'Criterium Focused Interview', null);