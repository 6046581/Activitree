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
    profile_picture_path VARCHAR(255) NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    token VARCHAR(255) NULL
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
    activity_time TIMESTAMP NOT NULL,
    photo_path VARCHAR(255) NULL,
    location_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (location_id) REFERENCES locations(id)
        ON DELETE SET NULL,

    FOREIGN KEY (created_by) REFERENCES users(id)
        ON DELETE SET NULL
);

-- ======================================
-- ACTIVITY PARTICIPANTS
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
-- INVITATIONS
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
(10, 'Olivia Smith', 'olivia.smith@example.com', 'password', 'user'),
(11, 'Sanne de Vries', 'sanne.devries@example.com', 'password', 'user'),
(12, 'Daan Jansen', 'daan.jansen@example.com', 'password', 'user'),
(13, 'Lotte Bakker', 'lotte.bakker@example.com', 'password', 'user'),
(14, 'Milan Smit', 'milan.smit@example.com', 'password', 'user'),
(15, 'Emma Visser', 'emma.visser@example.com', 'password', 'user'),
(16, 'Bram van Dijk', 'bram.vandijk@example.com', 'password', 'user'),
(17, 'Tess Mulder', 'tess.mulder@example.com', 'password', 'user'),
(18, 'Ruben Meijer', 'ruben.meijer@example.com', 'password', 'user'),
(19, 'Nina de Boer', 'nina.deboer@example.com', 'password', 'user'),
(20, 'Finn Kuiper', 'finn.kuiper@example.com', 'password', 'user'),
(21, 'Yara Prins', 'yara.prins@example.com', 'password', 'user'),
(22, 'Sem van Leeuwen', 'sem.vanleeuwen@example.com', 'password', 'user'),
(23, 'Roos Bos', 'roos.bos@example.com', 'password', 'user'),
(24, 'Jesse Dekker', 'jesse.dekker@example.com', 'password', 'user'),
(25, 'Iris Brouwer', 'iris.brouwer@example.com', 'password', 'user')
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
(1, 52.367600, 4.904100, 'Netherlands', 'NL', 'Amsterdam', '1012', 'Damrak', '1', 'Damrak 1, 1012 Amsterdam, Netherlands'),
(2, 52.090700, 5.121400, 'Netherlands', 'NL', 'Utrecht', '3511', 'Oudegracht', '210', 'Oudegracht 210, 3511 Utrecht, Netherlands'),
(3, 51.922500, 4.479170, 'Netherlands', 'NL', 'Rotterdam', '3011', 'Coolsingel', '80', 'Coolsingel 80, 3011 Rotterdam, Netherlands'),
(4, 52.070500, 4.300700, 'Netherlands', 'NL', 'Den Haag', '2511', 'Spui', '70', 'Spui 70, 2511 Den Haag, Netherlands'),
(5, 51.441600, 5.469700, 'Netherlands', 'NL', 'Eindhoven', '5611', 'Stratumseind', '55', 'Stratumseind 55, 5611 Eindhoven, Netherlands'),
(6, 51.585300, 4.775000, 'Netherlands', 'NL', 'Breda', '4811', 'Grote Markt', '12', 'Grote Markt 12, 4811 Breda, Netherlands'),
(7, 51.697800, 5.303700, 'Netherlands', 'NL', 'Den Bosch', '5211', 'Hinthamerstraat', '33', 'Hinthamerstraat 33, 5211 Den Bosch, Netherlands'),
(8, 51.812600, 5.837200, 'Netherlands', 'NL', 'Nijmegen', '6511', 'Molenstraat', '90', 'Molenstraat 90, 6511 Nijmegen, Netherlands'),
(9, 51.571900, 4.768300, 'Netherlands', 'NL', 'Tilburg', '5038', 'Heuvelring', '140', 'Heuvelring 140, 5038 Tilburg, Netherlands'),
(10, 52.516800, 6.083000, 'Netherlands', 'NL', 'Zwolle', '8011', 'Melkmarkt', '24', 'Melkmarkt 24, 8011 Zwolle, Netherlands'),
(11, 52.221500, 6.893700, 'Netherlands', 'NL', 'Enschede', '7511', 'Langestraat', '45', 'Langestraat 45, 7511 Enschede, Netherlands'),
(12, 52.160100, 4.497000, 'Netherlands', 'NL', 'Leiden', '2312', 'Breestraat', '88', 'Breestraat 88, 2312 Leiden, Netherlands'),
(13, 52.387400, 4.646200, 'Netherlands', 'NL', 'Haarlem', '2011', 'Grote Houtstraat', '100', 'Grote Houtstraat 100, 2011 Haarlem, Netherlands'),
(14, 53.219400, 6.566500, 'Netherlands', 'NL', 'Groningen', '9711', 'Vismarkt', '9', 'Vismarkt 9, 9711 Groningen, Netherlands'),
(15, 53.201200, 5.799900, 'Netherlands', 'NL', 'Leeuwarden', '8911', 'Nieuwestad', '70', 'Nieuwestad 70, 8911 Leeuwarden, Netherlands'),
(16, 52.011600, 4.357100, 'Netherlands', 'NL', 'Delft', '2611', 'Markt', '20', 'Markt 20, 2611 Delft, Netherlands'),
(17, 51.924400, 4.477700, 'Netherlands', 'NL', 'Schiedam', '3111', 'Lange Haven', '15', 'Lange Haven 15, 3111 Schiedam, Netherlands'),
(18, 51.495000, 3.610000, 'Netherlands', 'NL', 'Middelburg', '4331', 'Lange Delft', '62', 'Lange Delft 62, 4331 Middelburg, Netherlands'),
(19, 51.498800, 3.613900, 'Netherlands', 'NL', 'Vlissingen', '4381', 'Walstraat', '27', 'Walstraat 27, 4381 Vlissingen, Netherlands'),
(20, 52.992800, 6.564200, 'Netherlands', 'NL', 'Assen', '9401', 'Brink', '5', 'Brink 5, 9401 Assen, Netherlands'),
(21, 52.779200, 6.906700, 'Netherlands', 'NL', 'Emmen', '7811', 'Noorderstraat', '40', 'Noorderstraat 40, 7811 Emmen, Netherlands'),
(22, 50.851400, 5.690000, 'Netherlands', 'NL', 'Maastricht', '6211', 'Vrijthof', '18', 'Vrijthof 18, 6211 Maastricht, Netherlands'),
(23, 51.842500, 5.852800, 'Netherlands', 'NL', 'Arnhem', '6811', 'Korenmarkt', '11', 'Korenmarkt 11, 6811 Arnhem, Netherlands'),
(24, 51.985100, 5.898700, 'Netherlands', 'NL', 'Apeldoorn', '7311', 'Hoofdstraat', '120', 'Hoofdstraat 120, 7311 Apeldoorn, Netherlands'),
(25, 52.306100, 4.690700, 'Netherlands', 'NL', 'Hoofddorp', '2132', 'Burgemeester van Stamplein', '290', 'Burgemeester van Stamplein 290, 2132 Hoofddorp, Netherlands'),
(26, 53.215600, 6.564800, 'Netherlands', 'NL', 'Haren', '9751', 'Rijksstraatweg', '150', 'Rijksstraatweg 150, 9751 Haren, Netherlands'),
(27, 52.632400, 4.753400, 'Netherlands', 'NL', 'Alkmaar', '1811', 'Laat', '186', 'Laat 186, 1811 Alkmaar, Netherlands'),
(28, 52.508300, 6.094400, 'Netherlands', 'NL', 'Kampen', '8261', 'Oudestraat', '140', 'Oudestraat 140, 8261 Kampen, Netherlands'),
(29, 51.812600, 4.668600, 'Netherlands', 'NL', 'Dordrecht', '3311', 'Voorstraat', '250', 'Voorstraat 250, 3311 Dordrecht, Netherlands'),
(30, 52.508900, 5.475300, 'Netherlands', 'NL', 'Lelystad', '8232', 'Stadhuisplein', '2', 'Stadhuisplein 2, 8232 Lelystad, Netherlands')
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
(1, 'Walk', 'Short city walk.', 'outdoor', 'completed', '2026-01-08 10:00:00', 1, 11, '2025-12-28 09:30:00'),
(2, 'Canal Sketch Session', 'Bring your notebook and sketch city canals together.', 'indoor', 'completed', '2026-01-21 14:00:00', 2, 13, '2026-01-05 12:15:00'),
(3, 'Rotterdam Board Game Night', 'Strategy and party games for all levels. Quick rounds, long rounds, chaotic rounds, and a late bonus table for anyone who still has energy after the main session.', 'indoor', 'completed', '2026-02-03 19:30:00', 3, 5, '2026-01-22 17:20:00'),
(4, 'Sunrise Beach Run', 'Easy-paced group run by the coast.', 'outdoor', 'completed', '2026-02-15 07:15:00', 19, 7, '2026-01-29 18:00:00'),
(5, 'Community Cooking Lab', 'Cook a full shared meal in small teams.', 'indoor', 'completed', '2026-03-01 18:00:00', 6, 2, '2026-02-10 13:00:00'),
(6, 'Tech Career Coffee', NULL, 'indoor', 'cancelled', '2026-03-08 16:30:00', 5, 6, '2026-02-12 10:30:00'),
(7, 'Museum Day Den Haag', 'Art and design highlights with guided discussion.', 'indoor', 'completed', '2026-03-14 11:00:00', 4, 4, '2026-02-20 11:15:00'),
(8, 'Open Air Yoga', 'Gentle yoga session in the park.', 'outdoor', 'completed', '2026-03-22 09:00:00', 16, 15, '2026-03-01 08:40:00'),
(9, 'Photography Walk Haarlem', 'Street and architecture photo walk.', 'outdoor', 'cancelled', '2026-03-28 15:00:00', 13, 9, '2026-03-03 19:10:00'),
(10, 'Brunch', 'Snacks.', 'indoor', 'planned', '2026-04-01 11:30:00', 12, 1, '2026-03-24 09:00:00'),
(11, 'Evening Bouldering Session', 'Indoor climbing for beginners and regulars.', 'indoor', 'planned', '2026-04-02 19:00:00', 23, 14, '2026-03-20 15:20:00'),
(12, 'City Cleanup Utrecht', 'Help clean canalside streets for two hours.', 'outdoor', 'planned', '2026-04-03 09:00:00', 2, 16, '2026-03-18 10:05:00'),
(13, 'Book Club Dutch Fiction', 'Discuss this month''s selected novel.', 'indoor', 'planned', '2026-04-04 20:00:00', 10, 17, '2026-03-22 16:45:00'),
(14, 'Saturday Bike Loop', 'A 35 km social ride with coffee stop.', 'outdoor', 'planned', '2026-04-05 10:00:00', 24, 20, '2026-03-19 09:50:00'),
(15, 'Pottery Workshop', 'Hands-on clay session with local artist.', 'indoor', 'planned', '2026-04-07 18:45:00', 8, 19, '2026-03-15 12:30:00'),
(16, 'Beginner Salsa Class', 'Partner dance basics and fun social ending.', 'indoor', 'planned', '2026-04-10 19:30:00', 9, 8, '2026-03-21 17:40:00'),
(17, 'Lelystad Birdwatching', 'Observe spring migration from the wetlands.', 'outdoor', 'planned', '2026-04-13 06:30:00', 30, 25, '2026-03-25 14:10:00'),
(18, 'Startup Lightning Talks', 'Five-minute talks by local founders.', 'indoor', 'planned', '2026-04-16 18:00:00', 25, 12, '2026-03-23 11:00:00'),
(19, 'King''s Day Prep Meetup', 'Coordinate neighborhood orange market stands.', 'indoor', 'planned', '2026-04-24 17:00:00', 1, 3, '2026-03-26 13:00:00'),
(20, 'King''s Day Street Games and Hyper-Competitive Oranje Team Tournament Extravaganza', 'Play traditional games and music outdoors. Teams rotate through mini challenges including giant-jenga, sack races, speed puzzling, and a neighborhood scavenger sprint. Scores are tracked live and the final round is winner-takes-all.', 'outdoor', 'planned', '2026-04-27 13:00:00', 27, 21, '2026-03-26 13:15:00'),
(21, 'May Day Hike Veluwe', 'Forest trail with picnic stop.', 'outdoor', 'planned', '2026-05-01 09:30:00', 24, 7, '2026-03-27 18:30:00'),
(22, 'Game Dev Jam', 'Build a small prototype in one evening.', 'indoor', 'planned', '2026-05-08 18:30:00', 5, 6, '2026-03-28 15:30:00'),
(23, 'Maastricht Food Tour', 'Taste regional dishes in the city center.', 'outdoor', 'planned', '2026-05-15 12:00:00', 22, 18, '2026-03-29 09:20:00'),
(24, 'Canal Kayak Clinic', 'Paddle.', 'outdoor', 'planned', '2026-05-21 08:00:00', 1, 10, '2026-03-29 16:00:00'),
(25, 'Leeuwarden Coding Breakfast', 'Coffee, croissants, and pair programming.', 'indoor', 'planned', '2026-06-04 07:30:00', 15, 2, '2026-03-30 07:45:00'),
(26, 'Midsummer Garden Day', 'Planting and compost workshop.', 'outdoor', 'planned', '2026-06-21 10:00:00', 26, 23, '2026-03-30 18:20:00'),
(27, 'Beach Volleyball Tournament', 'Mixed teams and rotating mini-matches.', 'outdoor', 'planned', '2026-07-05 14:00:00', 19, 9, '2026-03-30 20:00:00'),
(28, 'Open Mic Poetry Night', 'Share your own poem or read a favorite piece.', 'indoor', 'planned', '2026-07-18 20:00:00', 14, 11, '2026-03-31 10:00:00'),
(29, 'Summer Night Cycling', 'Relaxed evening route through historic streets.', 'outdoor', 'planned', '2026-08-02 21:00:00', 29, 24, '2026-03-31 09:30:00'),
(30, 'Design Portfolio Review', 'Peer feedback session for digital creatives.', 'indoor', 'planned', '2026-08-20 19:00:00', 3, 13, '2026-03-31 12:10:00'),
(31, 'Zwolle Jazz Picnic', 'Live acoustic set in the park.', 'outdoor', 'planned', '2026-09-12 16:00:00', 10, 4, '2026-03-31 08:50:00'),
(32, 'Autumn Photography Course', 'Learn composition and low-light techniques.', 'indoor', 'planned', '2026-10-03 10:30:00', 20, 15, '2026-03-31 14:00:00'),
(33, 'Tech Hackathon Weekend for Data, Design, Policy, Prototyping, Accessibility, and Community Impact', 'A full two-day challenge where mixed teams of designers, developers, students, local organizers, and policy nerds co-create practical prototypes for real community problems. Day one focuses on interviews, problem framing, and rough experiments; day two moves into implementation, testing, and short demo pitches with feedback loops every two hours. Mentors are available for UX, backend architecture, public data usage, and ethical AI choices. Expect whiteboards, sticky notes, too much coffee, and at least one dramatic last-minute bug before presentations.', 'indoor', 'planned', '2026-11-14 09:00:00', 11, 12, '2026-03-31 18:00:00'),
(34, 'Winter Charity Run', 'Fundraising run through decorated city streets.', 'outdoor', 'planned', '2026-12-13 08:30:00', 4, 16, '2026-03-31 15:15:00'),
(35, 'New Year Vision Board', 'Set goals and create a visual plan for 2027.', 'indoor', 'planned', '2027-01-09 11:00:00', 6, 19, '2026-03-31 21:00:00'),
(36, 'Snow Hike Limburg Hills', 'Winter hiking day with warm lunch stop.', 'outdoor', 'planned', '2027-02-06 10:00:00', 22, 21, '2026-03-31 21:30:00')
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
(1, 11, 'organizer', '2025-12-28 09:45:00'),
(2, 13, 'organizer', '2026-01-05 12:30:00'),
(2, 3, 'participant', '2026-01-10 13:05:00'),
(2, 10, 'participant', '2026-01-15 10:20:00'),
(3, 5, 'organizer', '2026-01-22 17:25:00'),
(3, 2, 'participant', '2026-01-26 19:10:00'),
(3, 12, 'participant', '2026-01-29 08:45:00'),
(3, 25, 'participant', '2026-02-01 16:15:00'),
(3, 7, 'participant', '2026-02-01 16:20:00'),
(3, 8, 'participant', '2026-02-01 16:25:00'),
(4, 7, 'organizer', '2026-01-29 18:05:00'),
(4, 4, 'participant', '2026-02-02 09:40:00'),
(5, 2, 'organizer', '2026-02-10 13:10:00'),
(5, 9, 'participant', '2026-02-15 09:15:00'),
(5, 14, 'participant', '2026-02-20 17:35:00'),
(5, 22, 'participant', '2026-02-24 08:25:00'),
(5, 23, 'participant', '2026-02-24 08:35:00'),
(7, 4, 'organizer', '2026-02-20 11:20:00'),
(8, 15, 'organizer', '2026-03-01 08:45:00'),
(8, 7, 'participant', '2026-03-05 07:50:00'),
(8, 13, 'participant', '2026-03-12 12:40:00'),
(9, 9, 'organizer', '2026-03-03 19:20:00'),
(9, 2, 'participant', '2026-03-08 11:15:00'),
(9, 17, 'participant', '2026-03-14 14:45:00'),
(10, 1, 'organizer', '2026-03-24 09:10:00'),
(10, 5, 'participant', '2026-03-26 08:40:00'),
(10, 12, 'participant', '2026-03-30 18:15:00'),
(10, 20, 'participant', '2026-03-31 19:45:00'),
(10, 2, 'participant', '2026-03-31 20:00:00'),
(10, 3, 'participant', '2026-03-31 20:05:00'),
(10, 4, 'participant', '2026-03-31 20:10:00'),
(10, 6, 'participant', '2026-03-31 20:15:00'),
(11, 14, 'organizer', '2026-03-20 15:25:00'),
(11, 3, 'participant', '2026-03-24 20:10:00'),
(12, 16, 'organizer', '2026-03-18 10:25:00'),
(12, 4, 'participant', '2026-03-21 09:00:00'),
(12, 11, 'participant', '2026-03-30 18:30:00'),
(13, 17, 'organizer', '2026-03-22 16:55:00'),
(13, 6, 'participant', '2026-03-25 09:40:00'),
(13, 15, 'participant', '2026-03-29 22:10:00'),
(13, 24, 'participant', '2026-04-01 21:00:00'),
(14, 20, 'organizer', '2026-03-19 09:55:00'),
(14, 1, 'participant', '2026-03-24 14:25:00'),
(14, 9, 'participant', '2026-03-28 16:15:00'),
(14, 23, 'participant', '2026-04-02 07:35:00'),
(15, 19, 'organizer', '2026-03-15 12:45:00'),
(15, 7, 'participant', '2026-03-22 18:30:00'),
(16, 8, 'organizer', '2026-03-21 17:50:00'),
(16, 2, 'participant', '2026-03-24 09:15:00'),
(16, 10, 'participant', '2026-03-29 10:40:00'),
(16, 25, 'participant', '2026-04-04 16:10:00'),
(16, 18, 'participant', '2026-04-04 16:15:00'),
(17, 25, 'organizer', '2026-03-25 14:15:00'),
(17, 5, 'participant', '2026-03-28 11:00:00'),
(17, 13, 'participant', '2026-03-31 12:20:00'),
(17, 19, 'participant', '2026-04-05 08:30:00'),
(18, 12, 'organizer', '2026-03-23 11:05:00'),
(19, 3, 'organizer', '2026-03-26 13:10:00'),
(19, 1, 'participant', '2026-03-28 16:40:00'),
(20, 21, 'organizer', '2026-03-26 13:20:00'),
(20, 1, 'participant', '2026-03-27 10:00:00'),
(20, 2, 'participant', '2026-03-27 10:05:00'),
(20, 3, 'participant', '2026-03-27 10:10:00'),
(20, 4, 'participant', '2026-03-27 10:15:00'),
(20, 5, 'participant', '2026-03-27 10:20:00'),
(20, 6, 'participant', '2026-03-27 10:25:00'),
(20, 7, 'participant', '2026-03-27 10:30:00'),
(20, 8, 'participant', '2026-03-27 10:35:00'),
(20, 9, 'participant', '2026-03-27 10:40:00'),
(20, 10, 'participant', '2026-03-27 10:45:00'),
(20, 11, 'participant', '2026-03-27 10:50:00'),
(20, 12, 'participant', '2026-03-27 10:55:00'),
(20, 13, 'participant', '2026-03-27 11:00:00'),
(20, 14, 'participant', '2026-03-27 11:05:00'),
(20, 15, 'participant', '2026-03-27 11:10:00'),
(20, 16, 'participant', '2026-03-27 11:15:00'),
(20, 17, 'participant', '2026-03-27 11:20:00'),
(20, 18, 'participant', '2026-03-27 11:25:00'),
(20, 19, 'participant', '2026-03-27 11:30:00'),
(20, 20, 'participant', '2026-03-27 11:35:00'),
(20, 22, 'participant', '2026-03-27 11:40:00'),
(20, 23, 'participant', '2026-03-27 11:45:00'),
(20, 24, 'participant', '2026-03-27 11:50:00'),
(20, 25, 'participant', '2026-03-27 11:55:00'),
(21, 7, 'organizer', '2026-03-27 18:45:00'),
(21, 2, 'participant', '2026-03-30 10:15:00'),
(21, 9, 'participant', '2026-04-06 18:05:00'),
(22, 6, 'organizer', '2026-03-28 15:35:00'),
(22, 5, 'participant', '2026-04-02 11:55:00'),
(22, 12, 'participant', '2026-04-11 16:45:00'),
(22, 20, 'participant', '2026-04-20 09:05:00'),
(23, 18, 'organizer', '2026-03-29 09:30:00'),
(23, 8, 'participant', '2026-04-05 14:00:00'),
(24, 10, 'organizer', '2026-03-29 16:10:00'),
(25, 2, 'organizer', '2026-03-30 07:50:00'),
(25, 3, 'participant', '2026-04-03 09:40:00'),
(25, 11, 'participant', '2026-04-14 08:15:00'),
(25, 19, 'participant', '2026-04-25 19:20:00'),
(25, 1, 'participant', '2026-04-26 09:00:00'),
(25, 4, 'participant', '2026-04-26 09:05:00'),
(25, 5, 'participant', '2026-04-26 09:10:00'),
(26, 23, 'organizer', '2026-03-30 18:30:00'),
(26, 4, 'participant', '2026-04-08 13:15:00'),
(27, 9, 'organizer', '2026-03-30 20:10:00'),
(27, 5, 'participant', '2026-04-09 19:20:00'),
(27, 16, 'participant', '2026-04-22 08:55:00'),
(27, 24, 'participant', '2026-05-11 17:40:00'),
(28, 11, 'organizer', '2026-03-31 10:05:00'),
(29, 24, 'organizer', '2026-03-31 09:35:00'),
(29, 6, 'participant', '2026-04-17 07:50:00'),
(29, 12, 'participant', '2026-05-03 19:10:00'),
(29, 20, 'participant', '2026-05-29 14:20:00'),
(30, 13, 'organizer', '2026-03-31 12:20:00'),
(30, 1, 'participant', '2026-04-15 17:00:00'),
(30, 15, 'participant', '2026-05-10 12:30:00'),
(30, 25, 'participant', '2026-06-01 09:10:00'),
(32, 15, 'organizer', '2026-03-31 14:10:00'),
(32, 3, 'participant', '2026-05-01 13:35:00'),
(32, 10, 'participant', '2026-06-07 16:20:00'),
(33, 12, 'organizer', '2026-03-31 18:05:00'),
(33, 1, 'participant', '2026-06-01 09:00:00'),
(33, 2, 'participant', '2026-06-01 09:10:00'),
(33, 3, 'participant', '2026-06-01 09:20:00'),
(33, 4, 'participant', '2026-06-01 09:30:00'),
(33, 5, 'participant', '2026-06-01 09:40:00'),
(33, 6, 'participant', '2026-06-01 09:50:00'),
(33, 7, 'participant', '2026-06-01 10:00:00'),
(33, 8, 'participant', '2026-06-01 10:10:00'),
(33, 9, 'participant', '2026-06-01 10:20:00'),
(33, 10, 'participant', '2026-06-01 10:30:00'),
(33, 11, 'participant', '2026-06-01 10:40:00'),
(33, 13, 'participant', '2026-06-01 10:50:00'),
(33, 14, 'participant', '2026-06-01 11:00:00'),
(33, 15, 'participant', '2026-06-01 11:10:00'),
(33, 16, 'participant', '2026-06-01 11:20:00'),
(33, 17, 'participant', '2026-06-01 11:30:00'),
(33, 18, 'participant', '2026-06-01 11:40:00'),
(33, 19, 'participant', '2026-06-01 11:50:00'),
(33, 20, 'participant', '2026-06-01 12:00:00'),
(33, 21, 'participant', '2026-06-01 12:10:00'),
(33, 22, 'participant', '2026-06-01 12:20:00'),
(33, 23, 'participant', '2026-06-01 12:30:00'),
(33, 24, 'participant', '2026-06-01 12:40:00'),
(33, 25, 'participant', '2026-06-01 12:50:00'),
(34, 16, 'organizer', '2026-03-31 15:20:00'),
(34, 2, 'participant', '2026-07-02 09:10:00'),
(34, 9, 'participant', '2026-08-16 14:05:00'),
(35, 19, 'organizer', '2026-03-31 21:05:00'),
(35, 6, 'participant', '2026-08-09 11:20:00'),
(35, 13, 'participant', '2026-10-25 09:15:00'),
(35, 24, 'participant', '2026-12-15 17:30:00'),
(36, 21, 'organizer', '2026-03-31 21:40:00')
ON DUPLICATE KEY UPDATE
    role = VALUES(role),
    joined_at = VALUES(joined_at);