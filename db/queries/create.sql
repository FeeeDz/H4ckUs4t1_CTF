DROP TABLE IF EXISTS CTF_submit;
DROP TABLE IF EXISTS CTF_user;
DROP TABLE IF EXISTS CTF_team;
DROP TABLE IF EXISTS CTF_challenge;


CREATE TABLE CTF_challenge (
    challenge_name VARCHAR(128),
    flag VARCHAR(48) NOT NULL,
    PRIMARY KEY (challenge_name),
    UNIQUE (flag)
) ENGINE = INNODB;

CREATE TABLE CTF_team (
    team_name VARCHAR(32) NOT NULL,
    token VARCHAR(128) NOT NULL,
    registration_date DATETIME NOT NULL,
    PRIMARY KEY (team_name),
    UNIQUE (token)
) ENGINE = INNODB;

CREATE TABLE CTF_user (
    username VARCHAR(16) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(256) NOT NULL,
    registration_date DATETIME NOT NULL,
    last_login DATETIME NOT NULL,
    role CHAR(1),
    team_name VARCHAR(32),
    PRIMARY KEY (username),
    UNIQUE (email(191)),
    FOREIGN KEY (team_name) REFERENCES CTF_team(team_name)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_submit (
    submit_id INT AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    team_name VARCHAR(32),
    challenge_name VARCHAR(128) NOT NULL,
    submit_time DATETIME NOT NULL,
    points INT,
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





-- DROP TABLE IF EXISTS CTF_submit;
-- DROP TABLE IF EXISTS CTF_user;
-- DROP TABLE IF EXISTS CTF_team;
-- DROP TABLE IF EXISTS CTF_challenge;


-- CREATE TABLE CTF_challenge (
--     challenge_id INT,
--     flag VARCHAR(48) NOT NULL,
--     PRIMARY KEY (challenge_id),
--     UNIQUE (flag)
-- ) ENGINE = INNODB;

-- CREATE TABLE CTF_team (
--     team_id INT AUTO_INCREMENT,
--     team_name VARCHAR(32) NOT NULL,
--     token VARCHAR(128) NOT NULL,
--     registration_date DATETIME NOT NULL,
--     PRIMARY KEY (team_id),
--     UNIQUE (team_name),
--     UNIQUE (token)
-- ) ENGINE = INNODB;

-- CREATE TABLE CTF_user (
--     user_id INT AUTO_INCREMENT,
--     username VARCHAR(16) NOT NULL,
--     password_hash VARCHAR(255) NOT NULL,
--     email VARCHAR(256) NOT NULL,
--     registration_date DATETIME NOT NULL,
--     last_login DATETIME NOT NULL,
--     role CHAR(1),
--     team_id INT,
--     PRIMARY KEY (user_id),
--     UNIQUE (username),
--     UNIQUE (email(191)),
--     FOREIGN KEY (team_id) REFERENCES CTF_team(team_id)
--     ON UPDATE CASCADE
--     ON DELETE CASCADE
-- ) ENGINE = INNODB;

-- CREATE TABLE CTF_submit (
--     submit_id INT AUTO_INCREMENT,
--     user_id INT NOT NULL,
--     team_id INT,
--     challenge_id INT NOT NULL,
--     submit_time DATETIME NOT NULL,
--     points INT,
--     PRIMARY KEY (submit_id),
--     FOREIGN KEY (user_id) REFERENCES CTF_user(user_id)
--     ON UPDATE CASCADE
--     ON DELETE CASCADE,
--     FOREIGN KEY (team_id) REFERENCES CTF_team(team_id)
--     ON UPDATE CASCADE
--     ON DELETE CASCADE,
--     FOREIGN KEY (challenge_id) REFERENCES CTF_challenge(challenge_id)
--     ON UPDATE CASCADE
--     ON DELETE CASCADE
-- ) ENGINE = INNODB;


-- -- INSERT INTO CTF_challenge (challenge_id, flag)
-- -- VALUES
-- --     (1, "");

-- -- INSERT INTO CTF_team (team_name, token, registration_date)
-- -- VALUES
-- --     ("", "", "2021-12-15 10:20:54");

-- INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
-- VALUES
--     ("imBenjamin741", "23fadb9e853ac514dc31be67721f1ee78124e274816fac6b90268473a2aa72ff0e6acd8af721b168361fb9c3f523434a8ef6aabc325a93ecc50f8dcfcded0a0b", "beniaminovagnarelli@gmail.com", NOW(), NOW(), 'A', NULL),
--     ("guest", "53c401862d47f129f5cd469596a9221cdb63af31bd1dcb831182d48d436cc8c92c3722cdba520648ebbbe96c52e76125e74a9766f6388668717f2a70ea26a0d4", "guest@gmail.com", NOW(), NOW(), 'U', NULL);

-- -- INSERT INTO CTF_submit (user_id, team_id, challenge_id, submit_time, points)
-- -- VALUES
-- --     (1, NULL, 1, "2021-12-19 9:20:54", 1);
