-- ======================================
-- DATABASE
-- ======================================

DROP DATABASE IF EXISTS activitree;
CREATE DATABASE IF NOT EXISTS activitree;
USE activitree;

-- ======================================
-- USERS
-- ======================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
)

-- ======================================
-- LOCATIONS
-- ======================================

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,

    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,

    country VARCHAR(100),
    country_code VARCHAR(10),
    city VARCHAR(100),
    postal_code VARCHAR(20),
    street VARCHAR(150),
    house_number VARCHAR(20),

    formatted_address VARCHAR(255),

    INDEX (latitude, longitude)
);

-- ======================================
-- ACTIVITIES
-- ======================================

CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(100) NOT NULL,
    description TEXT,
    activity_type ENUM('indoor','outdoor') NOT NULL,
    status ENUM('planned','cancelled','completed') DEFAULT 'planned',
    activity_time TIMESTAMP NOT NULL,
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
-- SAMPLE DATA
-- ======================================

-- Keep seed data aligned with ActivitiesPage test fixtures.
-- Explicit IDs ensure stable references across users, locations, activities, and participants.
INSERT INTO users (id, username, email, password, role)
VALUES
(1, 'NoomStuff', 'noomstuff@example.com', 'password', 'admin'),
(2, 'JBGhostly', 'jbgostly@example.com', 'password', 'admin'),
(3, 'John Person', 'john.doe@example.com', 'password', 'user'),
(4, 'Aisha Carter', 'aisha.carter@example.com', 'password', 'user'),
(5, 'Luca Moretti', 'luca.moretti@example.com', 'password', 'user'),
(6, 'Sofia Hernandez', 'sofia.hernandez@example.com', 'password', 'user'),
(7, 'Noah Kim', 'noah.kim@example.com', 'password', 'user'),
(8, 'Maya Patel', 'maya.patel@example.com', 'password', 'user'),
(9, 'Ethan Lee', 'ethan.lee@example.com', 'password', 'user'),
(10, 'Olivia Smith', 'olivia.smith@example.com', 'password', 'user')
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    email = VALUES(email),
    password = VALUES(password),
    role = VALUES(role);

INSERT INTO locations (
    id,
    latitude, longitude,
    country, country_code, city, postal_code,
    street, house_number, formatted_address
)
VALUES
(1, 40.712800, -74.006000, 'United States', 'US', 'Hometown', '10001', 'Mountain Rd', '123', '123 Mountain Rd, Hometown, USA'),
(2, 40.713800, -74.007000, 'United States', 'US', 'Hometown', '10002', 'Main St', '456', '456 Main St, Hometown, USA'),
(3, 40.714800, -74.008000, 'United States', 'US', 'Hometown', '10003', 'Park Ave', '789', '789 Park Ave, Hometown, USA'),
(4, 40.715800, -74.009000, 'United States', 'US', 'Hometown', '10004', 'Library Ln', '321', '321 Library Ln, Hometown, USA'),
(5, 40.716800, -74.010000, 'United States', 'US', 'Hometown', '10005', 'Art St', '654', '654 Art St, Hometown, USA'),
(6, 40.717800, -74.011000, 'United States', 'US', 'Hometown', '10006', 'Tech Blvd', '987', '987 Tech Blvd, Hometown, USA'),
(7, 40.718800, -74.012000, 'United States', 'US', 'Hometown', '10007', 'Garden Rd', '246', '246 Garden Rd, Hometown, USA')
ON DUPLICATE KEY UPDATE
    latitude = VALUES(latitude),
    longitude = VALUES(longitude),
    country = VALUES(country),
    country_code = VALUES(country_code),
    city = VALUES(city),
    postal_code = VALUES(postal_code),
    street = VALUES(street),
    house_number = VALUES(house_number),
    formatted_address = VALUES(formatted_address);

INSERT INTO activities (
    id,
    title,
    description,
    activity_type,
    status,
    activity_time,
    location_id,
    created_by,
    created_at
)
VALUES
(1, 'Hiking in the Mountains', 'Join us for a refreshing hike through the scenic mountain trails. Perfect for all skill levels! Boy do I love hiking in the mountains. The fresh air, the beautiful views, the sense of adventure - it''s all so invigorating. Whether you''re a seasoned hiker or just looking for a leisurely stroll, this activity is sure to be a great time. Don''t forget to bring your water bottle and hiking boots! I could talk about hiking all day! Enough that it would be a good character limit test for the activity description. Hiking in the mountains is truly one of my favorite things to do. The feeling of being surrounded by nature, the sound of birds chirping, and the breathtaking views make it an unforgettable experience. I can''t wait to share this adventure with you all! Let''s hit the trails and make some amazing memories together. See you on the mountain!', 'outdoor', 'planned', '2026-07-15 09:00:00', 1, 1, '2026-06-01 12:00:00'),
(2, 'Cooking Class', 'Learn to cook delicious meals with our hands-on cooking class. Suitable for beginners and food enthusiasts alike.', 'indoor', 'planned', '2026-07-20 18:00:00', 2, 2, '2026-06-05 15:30:00'),
(3, 'Yoga in the Park', 'Relax and rejuvenate with our outdoor yoga sessions in the park. All levels welcome, bring your own mat!', 'outdoor', 'planned', '2026-07-25 08:00:00', 3, 3, '2026-06-10 09:00:00'),
(4, 'Book Club Meeting', 'Join our monthly book club to discuss the latest bestsellers and literary classics. New members are always welcome!', 'indoor', 'planned', '2026-07-30 19:00:00', 4, 4, '2026-06-15 14:00:00'),
(5, 'Art Workshop', 'Unleash your creativity in our art workshop, where you can explore various mediums and techniques. No experience necessary!', 'indoor', 'planned', '2026-08-05 14:00:00', 5, 5, '2026-06-20 11:00:00'),
(6, 'Tech Meetup', 'Connect with fellow tech enthusiasts at our monthly meetup, featuring guest speakers, networking opportunities, and discussions on the latest trends in technology.', 'indoor', 'planned', '2026-08-10 19:00:00', 6, 6, '2026-06-25 16:00:00'),
(7, 'Gardening Club', 'Get your hands dirty and learn about sustainable gardening practices in our community gardening club. All skill levels welcome!', 'outdoor', 'planned', '2026-08-15 10:00:00', 7, 7, '2026-06-30 08:00:00')
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    description = VALUES(description),
    activity_type = VALUES(activity_type),
    status = VALUES(status),
    activity_time = VALUES(activity_time),
    location_id = VALUES(location_id),
    created_by = VALUES(created_by),
    created_at = VALUES(created_at);

INSERT INTO activity_participants (activity_id, user_id, role, joined_at)
VALUES
(1, 1, 'organizer', '2026-06-04 15:00:00'),
(1, 2, 'participant', '2026-06-02 12:00:00'),
(1, 3, 'participant', '2026-06-02 11:00:00'),
(1, 7, 'participant', '2026-06-03 10:00:00'),
(1, 8, 'participant', '2026-06-04 10:30:00'),
(2, 1, 'participant', '2026-06-04 18:00:00'),
(2, 2, 'organizer', '2026-06-04 18:00:00'),
(2, 3, 'participant', '2026-06-04 18:00:00'),
(2, 4, 'participant', '2026-06-04 18:00:00'),
(2, 5, 'participant', '2026-06-04 18:00:00'),
(2, 8, 'participant', '2026-06-04 18:00:00'),
(3, 2, 'participant', '2026-06-05 09:00:00'),
(3, 3, 'organizer', '2026-06-10 09:00:00'),
(3, 5, 'participant', '2026-06-05 09:30:00'),
(3, 6, 'participant', '2026-06-05 10:00:00'),
(4, 3, 'participant', '2026-06-05 10:30:00'),
(4, 4, 'organizer', '2026-06-15 14:00:00'),
(4, 7, 'participant', '2026-06-05 10:30:00'),
(5, 1, 'participant', '2026-06-05 11:00:00'),
(5, 5, 'organizer', '2026-06-20 11:00:00'),
(5, 6, 'participant', '2026-06-05 11:00:00'),
(5, 9, 'participant', '2026-06-05 11:00:00'),
(6, 1, 'participant', '2026-06-05 11:00:00'),
(6, 3, 'participant', '2026-06-05 11:00:00'),
(6, 5, 'participant', '2026-06-05 11:00:00'),
(6, 6, 'organizer', '2026-06-25 16:00:00'),
(7, 2, 'participant', '2026-06-05 11:00:00'),
(7, 4, 'participant', '2026-06-05 11:00:00'),
(7, 6, 'participant', '2026-06-05 11:00:00'),
(7, 7, 'organizer', '2026-06-30 08:00:00')
ON DUPLICATE KEY UPDATE
    role = VALUES(role),
    joined_at = VALUES(joined_at);