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
-- SAMPLE DATA
-- ======================================

INSERT INTO users (username, email, password, role)
VALUES
('NoomStuff', 'Noomstuff@mail.fake', 'admin', 'admin'),
('Ghostly', 'Ghostly@mail.fake', 'admin', 'admin'),
('Alice', 'Alice@mail.fake', 'password', 'user'),
('Bob', 'Bob@mail.fake', 'password', 'user'),
('Charlie', 'Charlie@mail.fake', 'password', 'user'),
('Diana', 'Diana@mail.fake', 'password', 'user'),
('Eve', 'Eve@mail.fake', 'password', 'user');

INSERT INTO locations (
    latitude, longitude,
    country, country_code, city, postal_code,
    street, house_number, formatted_address
)
VALUES
(40.712776, -74.005974, 'United States', 'US', 'New York', '10007', 'Broadway', '1', '1 Broadway, New York, NY 10007, USA'),
(34.052235, -118.243683, 'United States', 'US', 'Los Angeles', '90012', 'Spring St', '200', '200 Spring St, Los Angeles, CA 90012, USA'),
(41.878113, -87.629799, 'United States', 'US', 'Chicago', '60604', 'S LaSalle St', '50', '50 S LaSalle St, Chicago, IL 60604, USA'),
(29.760427, -95.369804, 'United States', 'US', 'Houston', '77002', 'Travis St', '901', '901 Travis St, Houston, TX 77002, USA'),
(25.761681, -80.191788, 'United States', 'US', 'Miami', '33130', 'Biscayne Blvd', '600', '600 Biscayne Blvd, Miami, FL 33130, USA'),
(47.606209, -122.332069, 'United States', 'US', 'Seattle', '98101', '4th Ave', '600', '600 4th Ave, Seattle, WA 98101, USA'),
(39.739235, -104.990250, 'United States', 'US', 'Denver', '80202', 'Colfax Ave', '100', '100 E Colfax Ave, Denver, CO 80202, USA'),
(37.774929, -122.419418, 'United States', 'US', 'San Francisco', '94103', 'Market St', '1355', '1355 Market St, San Francisco, CA 94103, USA');

INSERT INTO activities (title, description, activity_type, activity_date, activity_time, location_id, created_by)
VALUES
('Hiking in the Mountains', 'Join us for a refreshing hike through the scenic mountain trails. Perfect for all skill levels!', 'outdoor', '2024-07-15', '09:00:00', 7, 1),
('Cooking Class', 'Learn to cook delicious meals with our hands-on cooking class. Suitable for beginners and food enthusiasts alike.', 'indoor', '2024-07-20', '18:00:00', 1, 1),
('Yoga in the Park', 'Relax and rejuvenate with our outdoor yoga sessions in the park. All levels welcome, bring your own mat!', 'outdoor', '2024-07-25', '07:00:00', 8, 2),
('Book Club Meeting', 'Join our monthly book club to discuss the latest bestsellers and literary classics. New members are always welcome!', 'indoor', '2024-07-30', '19:00:00', 3, 3),
('Art Workshop', 'Unleash your creativity in our art workshop, where you can explore various mediums and techniques. No experience necessary!', 'indoor', '2024-08-05', '14:00:00', 1, 7),
('Tech Meetup', 'Connect with fellow tech enthusiasts at our monthly meetup, featuring guest speakers, networking opportunities, and discussions on the latest trends in technology.', 'indoor', '2024-08-10', '18:30:00', 1, 2),
('Gardening Club', 'Get your hands dirty and learn about sustainable gardening practices in our community gardening club. All skill levels welcome!', 'outdoor', '2024-08-15', '10:00:00', 4, 5);

INSERT INTO activity_participants (activity_id, user_id, role)
VALUES
(1, 1, 'organizer'),
(1, 2, 'participant'),
(2, 1, 'organizer'),
(2, 3, 'participant'),
(3, 2, 'organizer'),
(3, 4, 'participant'),
(4, 3, 'organizer'),
(4, 5, 'participant'),
(5, 7, 'organizer'),
(5, 6, 'participant'),
(6, 2, 'organizer'),
(6, 1, 'participant'),
(7, 5, 'organizer'),
(7, 4, 'participant');