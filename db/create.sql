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
    challenge_name VARCHAR(64),
    flag VARCHAR(48) NOT NULL,
    description VARCHAR(1024) NOT NULL,
    type CHAR(1) NOT NULL,
    category VARCHAR(64) NOT NULL,
    PRIMARY KEY (challenge_name),
    UNIQUE (flag),
    FOREIGN KEY (category) REFERENCES CTF_challenge_category(category)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_hint (
    hint_id INT,
    cost INT NOT NULL,
    description VARCHAR(1024) NOT NULL,
    challenge_name VARCHAR(64) NOT NULL,
    PRIMARY KEY (hint_id),
    FOREIGN KEY (challenge_name) REFERENCES CTF_challenge(challenge_name)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_resource (
    link VARCHAR(255) NOT NULL,
    challenge_name VARCHAR(64) NOT NULL,
    PRIMARY KEY (link),
    FOREIGN KEY (challenge_name) REFERENCES CTF_challenge(challenge_name)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_team (
    team_name VARCHAR(32) NOT NULL,
    token VARCHAR(32) NOT NULL,
    registration_date DATETIME NOT NULL,
    PRIMARY KEY (team_name),
    UNIQUE (token)
) ENGINE = INNODB;

CREATE TABLE CTF_user (
    username VARCHAR(16) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(254) NOT NULL,
    registration_date DATETIME NOT NULL,
    last_login DATETIME NOT NULL,
    role CHAR(1),
    team_name VARCHAR(32),
    PRIMARY KEY (username),
    UNIQUE (email),
    FOREIGN KEY (team_name) REFERENCES CTF_team(team_name)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_submit (
    submit_id INT AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    team_name VARCHAR(32),
    challenge_name VARCHAR(64) NOT NULL,
    content VARCHAR(64) NOT NULL,
    points INT,
    submit_time DATETIME NOT NULL,
    PRIMARY KEY (submit_id),
    FOREIGN KEY (username) REFERENCES CTF_user(username)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    FOREIGN KEY (team_name) REFERENCES CTF_team(team_name)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    FOREIGN KEY (challenge_name) REFERENCES CTF_challenge(challenge_name)
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


-- INSERT INTO CTF_challenge (challenge_name, flag)
-- VALUES
--     (1, "");

-- INSERT INTO CTF_team (team_name, token, registration_date)
-- VALUES
--     ("", "", "2021-12-15 10:20:54");

INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_name)
VALUES
    ("imBenjamin741", "$2y$10$CMsAXjoNz.o6PrfdXGA/Kug6vnwHPobD3jyVxivKtftQm6LXle6Om", "beniaminovagnarelli@gmail.com", NOW(), NOW(), 'A', NULL),
    ("guest", "$2y$10$GS4ESBetviny2YrrJHQgkOriX4dO5T0P01Ir9VX0GVEVxp5HUSj6W", "guest@gmail.com", NOW(), NOW(), 'U', NULL);

-- INSERT INTO CTF_submit (username, team_name, challenge_name, submit_time, points)
-- VALUES
--     (1, NULL, 1, "2021-12-19 9:20:54", 1);