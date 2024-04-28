DROP DATABASE IF EXISTS bd_train;
CREATE DATABASE bd_train;
USE bd_train;

CREATE TABLE user
(
    user_id INT AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    contact_no VARCHAR(255) NOT NULL,
    nid VARCHAR(255) NOT NULL,
    user_type VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_id)
) ENGINE=InnoDB;

CREATE TABLE place
(
    place_id INT AUTO_INCREMENT,
    place_name VARCHAR(255) NOT NULL,
    PRIMARY KEY (place_id)
) ENGINE=InnoDB;

CREATE TABLE route
(
    route_id INT AUTO_INCREMENT,
    time_required INT NOT NULL,
    cost INT NOT NULL,
    start_place_id INT NOT NULL,
    end_place_id INT NOT NULL,
    PRIMARY KEY (route_id),
    FOREIGN KEY (start_place_id) REFERENCES place(place_id) ON DELETE CASCADE,
    FOREIGN KEY (end_place_id) REFERENCES place(place_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE train
(
    train_id INT AUTO_INCREMENT,
    train_name VARCHAR(255) NOT NULL,
    compartments INT NOT NULL,
    seats INT NOT NULL,
    PRIMARY KEY (train_id)
) ENGINE=InnoDB;

CREATE TABLE train_route
(
    train_route_id INT AUTO_INCREMENT,
    train_id INT NOT NULL,
    route_id INT NOT NULL,
    PRIMARY KEY (train_route_id),
    FOREIGN KEY (train_id) REFERENCES train(train_id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES route(route_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE trip
(
    trip_id INT AUTO_INCREMENT,
    trip_date DATETIME NOT NULL,
    train_id INT NOT NULL,
    PRIMARY KEY (trip_id),
    FOREIGN KEY (train_id) REFERENCES train(train_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ticket
(
    ticket_id INT AUTO_INCREMENT,
    ticket_price INT NOT NULL,
    buying_date DATETIME NOT NULL,
    travel_date DATETIME NOT NULL,
    source_place_id INT NOT NULL,
    destination_place_id INT NOT NULL,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (ticket_id),
    FOREIGN KEY (source_place_id) REFERENCES place(place_id) ON DELETE CASCADE,
    FOREIGN KEY (destination_place_id) REFERENCES place(place_id) ON DELETE CASCADE,
    FOREIGN KEY (trip_id) REFERENCES trip(trip_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE ticket_seat
(
    ticket_seat_id INT AUTO_INCREMENT,
    seat_no INT NOT NULL,
    compartment_no INT NOT NULL,
    ticket_id INT NOT NULL,
    PRIMARY KEY (ticket_seat_id),
    FOREIGN KEY (ticket_id) REFERENCES ticket(ticket_id) ON DELETE CASCADE
) ENGINE=InnoDB;