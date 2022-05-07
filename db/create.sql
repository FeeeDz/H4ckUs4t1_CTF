DROP TABLE IF EXISTS CTF_submit;
DROP TABLE IF EXISTS CTF_user;
DROP TABLE IF EXISTS CTF_team;
DROP TABLE IF EXISTS CTF_resource;
DROP TABLE IF EXISTS CTF_hint;
DROP TABLE IF EXISTS CTF_challenge;
DROP TABLE IF EXISTS CTF_challenge_category;

CREATE TABLE CTF_challenge_category (
    category VARCHAR(64),
    PRIMARY KEY (category)
) ENGINE = INNODB;

CREATE TABLE CTF_challenge (
    challenge_id INT NOT NULL AUTO_INCREMENT,
    challenge_name VARCHAR(64) NOT NULL,
    flag VARCHAR(48) NOT NULL,
    description VARCHAR(1024) NOT NULL,
    type CHAR(1) NOT NULL,
    category VARCHAR(64) NOT NULL,
    PRIMARY KEY (challenge_id),
    UNIQUE (challenge_name),
    UNIQUE (flag),
    FOREIGN KEY (category) REFERENCES CTF_challenge_category(category)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_hint (
    hint_id INT NOT NULL AUTO_INCREMENT,
    challenge_id INT NOT NULL,
    cost INT NOT NULL,
    description VARCHAR(1024) NOT NULL,
    PRIMARY KEY (hint_id),
    FOREIGN KEY (challenge_id) REFERENCES CTF_challenge(challenge_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_resource (
    resource_id INT NOT NULL AUTO_INCREMENT,
    link VARCHAR(255) NOT NULL,
    challenge_id INT NOT NULL,
    PRIMARY KEY (resource_id),
    FOREIGN KEY (challenge_id) REFERENCES CTF_challenge(challenge_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_team (
    team_id INT NOT NULL AUTO_INCREMENT,
    team_name VARCHAR(32) NOT NULL,
    token VARCHAR(32) NOT NULL,
    registration_date DATETIME NOT NULL,
    PRIMARY KEY (team_id),
    UNIQUE (team_name),
    UNIQUE (token)
) ENGINE = INNODB;

CREATE TABLE CTF_user (
    user_id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(254) NOT NULL,
    registration_date DATETIME NOT NULL,
    last_login DATETIME NOT NULL,
    role CHAR(1),
    team_id INT,
    PRIMARY KEY (user_id),
    UNIQUE (username),
    UNIQUE (email),
    FOREIGN KEY (team_id) REFERENCES CTF_team(team_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_submit (
    submit_id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    team_id INT,
    challenge_id INT NOT NULL,
    content VARCHAR(64) NOT NULL,
    points INT,
    submit_time DATETIME NOT NULL,
    PRIMARY KEY (submit_id),
    FOREIGN KEY (user_id) REFERENCES CTF_user(user_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES CTF_team(team_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    FOREIGN KEY (challenge_id) REFERENCES CTF_challenge(challenge_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;


INSERT INTO CTF_challenge_category (category)
VALUES
    ("web"),
    ("misc"),
    ("crypto"),
    ("reverse"),
    ("pwn");

INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
VALUES
    ("imBenjamin741", "$2y$10$CMsAXjoNz.o6PrfdXGA/Kug6vnwHPobD3jyVxivKtftQm6LXle6Om", "beniaminovagnarelli@gmail.com", NOW(), NOW(), 'A', NULL),
    ("guest", "$2y$10$GS4ESBetviny2YrrJHQgkOriX4dO5T0P01Ir9VX0GVEVxp5HUSj6W", "guest@gmail.com", NOW(), NOW(), 'U', NULL);