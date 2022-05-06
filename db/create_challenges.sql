INSERT INTO CTF_challenge (challenge_name, flag, description, type, category)
VALUES
    ("baby_smash",
    "ITT{34zy_w4rmupz!!}",
    "Smash it!",
    "T",
    "pwn"),
    ("ez_jmp",
    "ITT{W3_l1K3_s0Ft_L4Nd1nGsz!!}",
    "Try to jump",
    "T",
    "pwn");

INSERT INTO CTF_hint (challenge_name, cost, description)
VALUES
    ("baby_smash",
    50,
    "https://en.wikipedia.org/wiki/Buffer_overflow"),
    ("ez_jmp",
    50,
    "What would happen if you put an address in a register?");

INSERT INTO CTF_resource (challenge_name, link)
VALUES
    ("baby_smash",
    "challenges/pwn/baby_smash/baby_smash"),
    ("ez_jmp",
    "challenges/pwn/ez_jmp/ez_jmp");