DROP DATABASE IF EXISTS furniture_inspiration_db;
CREATE DATABASE furniture_inspiration_db;
USE furniture_inspiration_db;

CREATE TABLE Users(
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(10) NOT NULL DEFAULT 'customer'
);

CREATE TABLE Designer(
    designer_id INT PRIMARY KEY AUTO_INCREMENT,
    designer_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100) UNIQUE,
    phone_number VARCHAR(20) UNIQUE
);

CREATE TABLE Category(
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Furniture(
    furniture_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    designer_id INT NOT NULL,
    furniture_name VARCHAR(100) NOT NULL,
    color VARCHAR(50),
    material VARCHAR(50),
    style VARCHAR(50),
    image_url VARCHAR(256) NOT NULL,
    FOREIGN KEY (category_id) REFERENCES Category(category_id),
    FOREIGN KEY (designer_id) REFERENCES Designer(designer_id)
);

CREATE TABLE User_Favorite(
    favorite_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    furniture_id INT NOT NULL,
    date_saved DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (furniture_id) REFERENCES Furniture(furniture_id),
    UNIQUE (user_id, furniture_id)
);

CREATE TABLE Inspiration_Board(
    board_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    board_name VARCHAR(100) NOT NULL,
    created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);

CREATE TABLE Inspiration_Board_Item(
    board_item_id INT PRIMARY KEY AUTO_INCREMENT,
    board_id INT NOT NULL,
    furniture_id INT NOT NULL,
    FOREIGN KEY (board_id) REFERENCES Inspiration_Board(board_id),
    FOREIGN KEY (furniture_id) REFERENCES Furniture(furniture_id),
    UNIQUE (board_id, furniture_id)
);

CREATE TABLE Admin_Log(
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_user_id INT NOT NULL,
    action_type VARCHAR(100) NOT NULL,
    table_affected VARCHAR(100) NOT NULL,
    record_id INT NOT NULL,
    action_timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_user_id) REFERENCES Users(user_id)
);

INSERT INTO Users(first_name, last_name, email, password, role)
VALUES
    ('Joshua', 'Akeredolu', 'joshtemi6@gmail.com', '$2y$10$dSFyIe/2W1sow9XHKaVG/.yRejH5vk0vVAldF5uSnu12sZ0olvEzC', 'admin'),
    ('Emmanuel', 'Ayeni', 'ifedapo_emmanuel@yahoo.com', '$2y$10$dSFyIe/2W1sow9XHKaVG/.yRejH5vk0vVAldF5uSnu12sZ0olvEzC', 'customer'),
    ('Ethan', 'Brooks', 'ethan.brooks@email.com', '13ae3ed6fe76d459c9c66fe38ff187593561a1f24d34cb22e06148c77e4cc02b', 'customer'),
    ('Olivia', 'Carter', 'olivia.carter@gmail.com', '25eb92ae54cc5089e9b995f4176795d3c67d927069a3009a655c76cf0e447536', 'customer'),
    ('Daniel', 'Kim', 'daniel.kim@email.com', 'd25949ef9e762fe7cfb9cc5d125e8a7bbca56662abc51b9d0dc0d2265aa28dce', 'customer'),
    ('Sophia', 'Reed', 'sophia.reed@email.com', 'f58e4a64909caaa56bfb6c1f5bc5e5ffe98345f52a8233c47a7b7e1b4d9ed1ec', 'customer'),
    ('Liam', 'Foster', 'liam.foster@email.com', '7229de2d88d23cb0dd58131b4a2e974a6f5a988d4d003ec359abcc231d3dc590', 'customer'),
    ('Ava', 'Bennett', 'ava.bennett@email.com', '8fc49a37693b9427e0dfd4d09d03faf974fe82701a2f1c1ee078924f87507166', 'customer'),
    ('Josh', 'Ake', 'joake1@morgan.edu', '$2y$10$dSFyIe/2W1sow9XHKaVG/.yRejH5vk0vVAldF5uSnu12sZ0olvEzC', 'customer'),
    ('Blessing', 'Sobowale', 'i.sobowal@yahoo.com', '$2y$10$dSFyIe/2W1sow9XHKaVG/.yRejH5vk0vVAldF5uSnu12sZ0olvEzC', 'admin');

INSERT INTO Designer(designer_name, contact_email, phone_number)
VALUES
    ('Leah Morgan', 'leah.morgan@designstudio.com', '410-555-1011'),
    ('Marcus Allen', 'marcus.allen@interiorspace.com', '410-555-1012'),
    ('Sofia Bennett', 'sofia.bennett@modernliving.com', '410-555-1013'),
    ('Andre Collins', 'andre.collins@urbanform.com', '410-555-1014'),
    ('Naomi Turner', 'naomi.turner@craftedhome.com', '410-555-1015');

INSERT INTO Category(category_name)
VALUES
    ('Sofa'),
    ('Chair'),
    ('Table'),
    ('Bed'),
    ('Storage');



INSERT INTO furniture
    (category_id, designer_id, furniture_name, color, material, style, image_url)
VALUES
    (1, 1, 'Green Velvet Sofa','Green, Brown', 'Velvet, Wood', 'Mid-Century Modern', '/images/Green Velvet Sofa.jpg'),
    (1, 1, 'Brown Leather Sofa','Brown', 'Leather, Wood', 'Modern', '/images/Brown Leather Sofa.jpg'),
    (2, 2, 'Yellow Accent Armchair','Yellow, Black', 'Fabric, Metal', 'Modern Minimalist', '/images/Yellow Accent Armchair.jpg'),
    (2, 2, 'Black Home Office Chair','Black, Brown', 'Plastic, Wood', 'Minimalist', '/images/Black Home Office Chair.jpg'),
    (3, 3, 'Round Wooden Table','Brown', 'Wood', 'Modern Minimalist', '/images/Round Wooden Table.jpg'),
    (2, 3, 'Blue Upholstered Bench Chair','Blue, Brown', 'Fabric, Wood', 'Rustic', '/images/Blue Upholstered Bench Chair.jpg'),
    (5, 4, 'Brown Wooden Cabinet','Brown', 'Wood', 'Mid-Century Modern', '/images/Brown Wooden Cabinet.jpg'),
    (5, 4, 'White Painted Cabinet','White', 'Painted Wood', 'Classic', '/images/White Painted Cabinet.jpg'),
    (3, 5, 'Wood Console Table','Brown', 'Wood', 'Contemporary', '/images/Wood Console Table.png'),
    (3, 5, 'Red Metal Cafe Table Set','Red', 'Metal', 'Industrial', '/images/Red Metal Cafe Table Set.png');

INSERT INTO User_Favorite(user_id, furniture_id, date_saved)
VALUES
    (1, 1, '2026-04-01 09:15:00'),
    (1, 5, '2026-04-01 09:18:00'),
    (2, 2, '2026-04-01 10:05:00'),
    (2, 6, '2026-04-01 10:09:00'),
    (3, 3, '2026-04-01 10:45:00'),
    (4, 4, '2026-04-01 11:20:00'),
    (5, 7, '2026-04-01 12:05:00'),
    (6, 8, '2026-04-01 12:30:00'),
    (7, 9, '2026-04-01 01:15:00'),
    (8, 10, '2026-04-01 01:45:00');

INSERT INTO Inspiration_Board(user_id, board_name, created_date)
VALUES
    (1, 'Living Room Refresh', '2026-03-28 08:30:00'),
    (2, 'Cozy Apartment Ideas', '2026-03-28 09:00:00'),
    (3, 'Reading Corner Setup', '2026-03-28 09:20:00'),
    (4, 'Office Lounge Inspiration', '2026-03-28 09:45:00'),
    (5, 'Bedroom Upgrade', '2026-03-28 10:10:00'),
    (6, 'Dining Space Plans', '2026-03-28 10:40:00'),
    (7, 'Scandinavian Favorites', '2026-03-28 11:05:00'),
    (8, 'Modern Storage Picks', '2026-03-28 11:30:00'),
    (1, 'Guest Room Concepts', '2026-03-29 01:00:00'),
    (2, 'Weekend Redesign Ideas', '2026-03-29 02:15:00');

INSERT INTO Inspiration_Board_Item(board_id, furniture_id)
VALUES
    (1, 1),
    (1, 6),
    (2, 2),
    (2, 4),
    (3, 3),
    (3, 9),
    (4, 4),
    (4, 10),
    (5, 7),
    (5, 8),
    (6, 5),
    (7, 3),
    (7, 9),
    (8, 10),
    (9, 7),
    (10, 1);

INSERT INTO Admin_Log(admin_user_id, action_type, table_affected, record_id, action_timestamp)
VALUES
    (9, 'INSERT', 'Furniture', 1, '2026-03-30 09:00:00'),
    (9, 'INSERT', 'Furniture', 2, '2026-03-30 09:05:00'),
    (9, 'INSERT', 'Furniture', 3, '2026-03-30 09:10:00'),
    (9, 'UPDATE', 'Furniture', 6, '2026-03-30 10:15:00'),
    (9, 'INSERT', 'Category', 5, '2026-03-30 10:45:00'),
    (10, 'INSERT', 'Designer', 4, '2026-03-30 11:00:00'),
    (10, 'UPDATE', 'Designer', 2, '2026-03-30 11:20:00'),
    (10, 'DELETE', 'Furniture', 9, '2026-03-30 11:45:00'),
    (10, 'INSERT', 'Furniture', 10, '2026-03-30 12:10:00'),
    (9, 'UPDATE', 'Category', 3, '2026-03-30 12:35:00');
