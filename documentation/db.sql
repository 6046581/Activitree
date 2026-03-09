-- ======================================
-- DATABASE
-- ======================================

CREATE DATABASE IF NOT EXISTS activitree;
USE activitree;

-- ======================================
-- USERS
-- ======================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================================
-- LOCATIONS
-- ======================================

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150),
    address VARCHAR(255),
    city VARCHAR(100)
);

-- ======================================
-- ACTIVITIES
-- ======================================

CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    activity_type ENUM('indoor','outdoor') NOT NULL,
    status ENUM('planned','cancelled','completed') DEFAULT 'planned',
    activity_date DATE NOT NULL,
    activity_time TIME NOT NULL,
    location_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (location_id) REFERENCES locations(id)
        ON DELETE SET NULL,

    FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
);

-- ======================================
-- ACTIVITY PARTICIPANTS (MANY TO MANY)
-- ======================================

CREATE TABLE activity_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('organizer','participant') DEFAULT 'participant',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (activity_id) REFERENCES activities(id)
        ON DELETE CASCADE,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE(activity_id, user_id)
);

-- ======================================
-- INVITATIONS (FOR GUESTS)
-- ======================================

CREATE TABLE invitations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    email VARCHAR(150) NOT NULL,
    status ENUM('pending','accepted','declined') DEFAULT 'pending',
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (activity_id) REFERENCES activities(id)
        ON DELETE CASCADE
);

-- ======================================
-- WEATHER (FOR OUTDOOR ACTIVITIES)
-- ======================================

CREATE TABLE weather (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    temperature DECIMAL(4,1),
    weather_description VARCHAR(100),
    wind_speed DECIMAL(4,1),
    fetched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (activity_id) REFERENCES activities(id)
        ON DELETE CASCADE
);

-- ======================================
-- NOTIFICATIONS
-- ======================================

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_id INT,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (activity_id) REFERENCES activities(id)
        ON DELETE SET NULL
);

-- ======================================
-- SAMPLE DATA (OPTIONAL)
-- ======================================

INSERT INTO users (name, email, password)
VALUES
('Alice', 'alice@email.com', 'hashedpassword'),
('Bob', 'bob@email.com', 'hashedpassword');

INSERT INTO locations (name, address, city)
VALUES
('Bowling Center', 'Main Street 10', 'Rotterdam'),
('City Park', 'Park Lane 5', 'Rotterdam');

INSERT INTO activities (title, description, activity_type, activity_date, activity_time, location_id, created_by)
VALUES
('Bowling Night', 'Team bowling event', 'indoor', '2026-04-10', '19:00:00', 1, 1),
('Company BBQ', 'Outdoor team BBQ', 'outdoor', '2026-05-15', '17:00:00', 2, 1);

INSERT INTO activity_participants (activity_id, user_id, role)
VALUES
(1, 1, 'organizer'),
(1, 2, 'participant'),
(2, 1, 'organizer');