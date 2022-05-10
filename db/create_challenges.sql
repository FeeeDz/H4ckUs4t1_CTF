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