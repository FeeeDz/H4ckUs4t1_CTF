DROP TABLE IF EXISTS CTF_submit;
DROP TABLE IF EXISTS CTF_user;
DROP TABLE IF EXISTS CTF_team;
DROP TABLE IF EXISTS CTF_resource;
DROP TABLE IF EXISTS CTF_hint;
DROP TABLE IF EXISTS CTF_challenge;
DROP TABLE IF EXISTS CTF_challenge_category;
DROP TABLE IF EXISTS CTF_rule;
DROP TABLE IF EXISTS CTF_upcoming_event;


CREATE TABLE CTF_upcoming_event (
    event_id INT NOT NULL AUTO_INCREMENT,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    PRIMARY KEY (event_id)
) ENGINE = INNODB;

CREATE TABLE CTF_rule (
    rule_id INT NOT NULL AUTO_INCREMENT,
    type CHAR(1) NOT NULL,
    description VARCHAR(1024) NOT NULL,
    event_id INT,
    PRIMARY KEY (rule_id),
    FOREIGN KEY (event_id) REFERENCES CTF_upcoming_event(event_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB;

CREATE TABLE CTF_challenge_category (
    category VARCHAR(64),
    PRIMARY KEY (category)
) ENGINE = INNODB;

CREATE TABLE CTF_challenge (
    challenge_id INT NOT NULL AUTO_INCREMENT,
    challenge_name VARCHAR(64) NOT NULL,
    flag VARCHAR(48) NOT NULL,
    description VARCHAR(1024) NOT NULL,
    points INT NOT NULL,
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
    UNIQUE (link, challenge_id),
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


INSERT INTO CTF_rule (start_date, end_date)
VALUES
    ("2022-05-16 14:00:00", "2022-05-17 14:00:00"),
    ("2022-05-18 14:00:00", "2022-05-19 14:00:00"),
    ("2022-05-20 14:00:00", "2022-05-21 14:00:00");

INSERT INTO CTF_rule (type, description, event_id)
VALUES
    ("X","Share flags with or give help to other participants", NULL),
    ("X", "Use automated scanning and probing tools such as Nmap, Gobuster, Dirb, sqlmap, etc. <i>unless specified in the challenge description", NULL),
    ("X", "Attempt to (or succeed at) DoSing or DDoSing any infrastructure or other participants", NULL),
    ("X", "Try to brute force flag submissions - it will not work", NULL),
    ("X", "Attempt to (or succeed at) DoSing or DDoSing any infrastructure or other participants", NULL),
    ("X", "Hoard flags. Any user that solves multiple challenges but doesn't turn in the flag until the last moment to trick others into thinking they will win will be punished. <b>If you're really good enough to win, you don't need to do this", NULL),
    ("N", "The flag format is <code>ITT{example_flag}", NULL),
    ("N", "All flags are case sensitive unless specified", NULL),
    ("N", "Scoring is dynamic and decreases in value as more participants solve the problem. Most challenges start at 500 points", NULL),
    ("N", "Each problem has a tag telling you whether it's ""Easy"", ""Medium"", or ""Hard"" - these may not be perfect, but they are generally correct", NULL),
    ("N", "If any challenges are broken, you feel the flag you have is correct, or you have any other questions, please reach out to an admin on our Discord", NULL),
    ("N", "If you do anything that we believe to be directly against the spirit of the competition, we reserve the right to remove anyone at any point. Please don't make us do that. This is for your learning and benefit", NULL);

    
INSERT INTO CTF_challenge_category (category)
VALUES
    ("web"),
    ("misc"),
    ("crypto"),
    ("reverse"),
    ("pwn");

INSERT INTO CTF_challenge (challenge_name, flag, description, type, points, category)
VALUES
    ("baby_smash",
    "ITT{34zy_w4rmupz!!}",
    "Smash it!",
    500,
    "T",
    "pwn"),
    ("ez_jmp",
    "ITT{W3_l1K3_s0Ft_L4Nd1nGsz!!}",
    "Try to jump",
    500,
    "T",
    "pwn");

INSERT INTO CTF_hint (challenge_id, cost, description)
VALUES
    (1,
    50,
    "https://en.wikipedia.org/wiki/Buffer_overflow"),
    (1,
    100,
    "Ci sei quasi"),
    (2,
    50,
    "What would happen if you put an address in a register?");

INSERT INTO CTF_resource (challenge_id, link)
VALUES
    (1,
    "baby_smash"),
    (2,
    "ez_jmp");


INSERT INTO CTF_user (username, password_hash, email, registration_date, last_login, role, team_id)
VALUES
    ("imBenjamin741", "$2y$10$CMsAXjoNz.o6PrfdXGA/Kug6vnwHPobD3jyVxivKtftQm6LXle6Om", "beniaminovagnarelli@gmail.com", NOW(), NOW(), 'A', NULL),
    ("guest", "$2y$10$GS4ESBetviny2YrrJHQgkOriX4dO5T0P01Ir9VX0GVEVxp5HUSj6W", "guest@gmail.com", NOW(), NOW(), 'U', NULL),
    ("admin", "$2y$10$WtpyhuUu5KFj5PGO64YFIegm7yPLzz7B5ZIcmLJaYBBGmVtay4TR2", "admin@gmail.com", NOW(), NOW(), 'A', NULL);