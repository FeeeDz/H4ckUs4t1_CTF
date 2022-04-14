DROP TABLE IF EXISTS CTF_permission;
DROP TABLE IF EXISTS CTF_role;
DROP TABLE IF EXISTS CTF_challenge;
DROP TABLE IF EXISTS CTF_team;
DROP TABLE IF EXISTS CTF_user;
DROP TABLE IF EXISTS CTF_submit;


CREATE TABLE CTF_permission (
    permission_id INT AUTO_INCREMENT,
    permission_type VARCHAR(32) NOT NULL,
    permission_description VARCHAR(MAX),
    PRIMARY KEY (permission_id),
    UNIQUE (permission_type)
) ENGINE = INNODB;

CREATE TABLE CTF_role (
    role_id INT AUTO_INCREMENT,
    role_name VARCHAR(32) NOT NULL,
    role_description VARCHAR(MAX),
    PRIMARY KEY (role_id),
    UNIQUE (role_name)
) ENGINE = INNODB;

CREATE TABLE CTF_challenge (
    challenge_id INT AUTO_INCREMENT,
    flag VARCHAR(48) NOT NULL,
    PRIMARY KEY (challenge_id),
    UNIQUE (flag)
) ENGINE = INNODB;

CREATE TABLE CTF_team (
    team_id INT AUTO_INCREMENT,
    team_name VARCHAR(32) NOT NULL,
    token VARCHAR(128) NOT NULL,
    registration_date DATETIME NOT NULL,
    PRIMARY KEY (team_id),
    UNIQUE (team_name),
    UNIQUE (token)
) ENGINE = INNODB;

CREATE TABLE CTF_user (
    user_id INT AUTO_INCREMENT,
    username VARCHAR(16) NOT NULL,
    password_hash VARCHAR(512) NOT NULL,
    email VARCHAR(320) NOT NULL,
    registration_date DATETIME NOT NULL,
    last_login DATETIME NOT NULL,
    role_id INT NOT NULL,
    team_id INT,
    PRIMARY KEY (user_id),
    UNIQUE (username),
    UNIQUE (email),
    FOREIGN KEY (role_id) REFERENCES CTF_role(role_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES CTF_team(team_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_submit (
    submit_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    team_id INT,
    challenge_id INT NOT NULL,
    submit_time DATETIME NOT NULL,
    points INT,
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


INSERT INTO CTF_permission (permission_type, permission_description)
VALUES
    ("", "");

INSERT INTO CTF_role (role_name, role_description)
VALUES
    ("", "");

INSERT INTO CTF_challenge (flag)
VALUES
    ("");

INSERT INTO CTF_team (team_name, token, registration_date)
VALUES
    ("", "", "2021-12-15 10:20:54");

INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role_id, team_id)
VALUES
    ("", "", "", "2021-12-19 9:20:54", "2021-12-19 9:20:54", 1, 1);

INSERT INTO CTF_submit (user_id, team_id, challenge_id, submit_time, points)
VALUES
    (1, NULL, 1, "2021-12-19 9:20:54", 1);
