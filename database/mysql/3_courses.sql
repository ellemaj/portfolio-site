CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blok VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    ec DECIMAL(4,1) NOT NULL,
    exam_type VARCHAR(100) NOT NULL,
    grade DECIMAL(4,1) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO courses (blok, name, ec, exam_type, grade) VALUES
('1', 'Program- & Career Orientation', 2.5, 'Presentatie', 7.8),
('1', 'Computer Science Basics', 5, 'Schriftelijke Kennistoets', 6.5),
('1', 'Programming Basics', 5, 'Casustoets', 8.3),
('2', 'Object-Oriented Programming', 5, 'Presentatie', 7.7),
('2', 'Object-Oriented Programming', 5, 'Schriftelijke Kennistoets', 9.8),
('3', 'Framework Project 1', 5, 'On-site Casustoets', 5.6),
('3', 'Framework Project 1', 2.5, 'Groepspresentatie project resultaat', 8.1),
('3', 'Framework Project 1', 2.5, 'Groepsportfolio op requirements', 8.7),
('3', 'Business Understanding Basics', 2.5, 'Video', null),
('4', 'Framework Project 2', 5, 'IT-ontwikkelingsportfolio', null),
('4', 'Framework Project 2', 2.5, 'Final Delivery', null),
('4', 'Framework Project 2', 2.5, 'Individueel Project Assessment', null),
('jaar', 'IT Personality 1', 1.25, 'Portfolio', null),
('jaar', 'IT Personality 2', 1.25, 'Portfolio', null),
('jaar', 'Personal Professional Development Exploration', 12.5, 'Criterium Focused Interview', null);