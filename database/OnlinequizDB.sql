CREATE DATABASE quizpl;

USE quizpl;

-- ==============================
-- 1. ADMINS
-- ==============================

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==============================
-- 2. USERS
-- ==============================

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==============================
-- 3. CATEGORIES
-- ==============================

CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==============================
-- 4. QUESTION SETS
-- ==============================

CREATE TABLE question_sets (
    set_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    set_name VARCHAR(100) NOT NULL,

    timer_sec INT DEFAULT 10,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id)
        REFERENCES categories(category_id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_set_per_subject
        (category_id, set_name)
);


-- ==============================
-- 5. QUESTIONS
-- ==============================

CREATE TABLE questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    set_id INT NOT NULL,

    question_text TEXT NOT NULL,

    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,

    correct_option TINYINT NOT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (set_id)
        REFERENCES question_sets(set_id)
        ON DELETE CASCADE,

    CHECK (correct_option BETWEEN 0 AND 3)
);


-- ==============================
-- 6. QUIZ ATTEMPTS
-- ==============================

CREATE TABLE quiz_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    set_id INT NOT NULL,

    score INT DEFAULT 0,
    total_questions INT DEFAULT 10,
    time_taken_seconds INT DEFAULT 0,

    date_attempted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (set_id)
        REFERENCES question_sets(set_id)
        ON DELETE CASCADE
);


-- ==============================
-- 7. INDEXES
-- ==============================

CREATE INDEX idx_questions_set
ON questions(set_id);

CREATE INDEX idx_attempts_user
ON quiz_attempts(user_id);

CREATE INDEX idx_attempts_set
ON quiz_attempts(set_id);


-- ==============================
-- 8. FOUR SUBJECTS
-- ==============================

INSERT INTO categories (subject_name)
VALUES
('Subject 1'),
('Subject 2'),
('Subject 3'),
('Subject 4');


-- ==============================
-- 9. SET FOR EACH SUBJECT
-- ==============================

INSERT INTO question_sets
(category_id, set_name, timer_sec)
VALUES
(1, 'Set 1', 10),
(2, 'Set 1', 10),
(3, 'Set 1', 10),
(4, 'Set 1', 10);