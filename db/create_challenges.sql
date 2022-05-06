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

INSERT INTO CTF_challenge (challenge_name, flag, description, type, category)
VALUES
    ("ez_jmp", "ITT{W3_l1K3_s0Ft_L4Nd1nGsz!!}", );

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