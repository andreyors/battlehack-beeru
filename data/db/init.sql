CREATE DATABASE battlehackberlin;

USE battlehackberlin;

CREATE TABLE customers (
    id INT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    website VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    zipcode VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(40) NOT NULL,
    customer_id INT(9) NOT NULL,
    status_id INT(9) NOT NULL,
    amount DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE statuses (
    id INT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    is_active TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO statuses (title, is_active)
    VALUES
    ('pending', 1),
    ('paid', 1),
    ('rejected', 1),
    ('cancelled', 1),
    ('refunded', 1);

CREATE TABLE items (
    id INT(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    payment_id INT(9),
    title VARCHAR(255),
    price DECIMAL(10,2)
) ENGINE=InnoDB;