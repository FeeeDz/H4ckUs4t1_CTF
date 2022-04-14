DROP TABLE IF EXISTS Dis_ProdottoDisponibile;
DROP TABLE IF EXISTS Dis_Ricarica;
DROP TABLE IF EXISTS Dis_Acquisto;
DROP TABLE IF EXISTS Dis_Distributore;
DROP TABLE IF EXISTS Dis_Prodotto;
DROP TABLE IF EXISTS Dis_Cliente;

CREATE TABLE Dis_Distributore (
    cod_distributore INT NOT NULL AUTO_INCREMENT,
    desc_distributore VARCHAR(40) NOT NULL,
    litri_totali FLOAT NOT NULL,
    litri_residui FLOAT NOT NULL,
    CONSTRAINT pk_cod_distributore PRIMARY KEY (cod_distributore)
) ENGINE=INNODB;

CREATE TABLE Dis_Prodotto (
    cod_prodotto INT NOT NULL AUTO_INCREMENT,
    desc_prodotto VARCHAR(40) NOT NULL,
    prezzo FLOAT NOT NULL,
    CONSTRAINT pk_cod_prodotto PRIMARY KEY (cod_prodotto)
) ENGINE=INNODB;

CREATE TABLE Dis_Cliente (
    cod_cliente INT NOT NULL AUTO_INCREMENT,
    nome VARCHAR(40) NOT NULL,
    cognome VARCHAR(40) NOT NULL,
    saldo FLOAT DEFAULT 0,
    CONSTRAINT pk_cod_cliente PRIMARY KEY (cod_cliente)
) ENGINE=INNODB;

CREATE TABLE Dis_ProdottoDisponibile (
    cod_distributore INT NOT NULL,
    cod_prodotto INT NOT NULL,
    quantità INT NOT NULL,
    CONSTRAINT fk_ProdottoDisponibile_cod_distributore FOREIGN KEY (cod_distributore)
    REFERENCES Dis_Distributore(cod_distributore)
    ON UPDATE CASCADE
    ON DELETE NO ACTION,
    CONSTRAINT fk_ProdottoDisponibile_cod_prodotto FOREIGN KEY (cod_prodotto)
    REFERENCES Dis_Prodotto(cod_prodotto)
    ON UPDATE CASCADE
    ON DELETE NO ACTION,
    UNIQUE(cod_distributore, cod_prodotto)
) ENGINE=INNODB;

CREATE TABLE Dis_Ricarica (
    cod_ricarica INT NOT NULL AUTO_INCREMENT,
    cod_cliente INT NOT NULL,
    cod_distributore INT NOT NULL,
    importo FLOAT NOT NULL,
    data_ricarica DATETIME NOT NULL,
    contabilizzata BOOLEAN DEFAULT 0,
    CONSTRAINT pk_cod_ricarica PRIMARY KEY (cod_ricarica),
    CONSTRAINT fk_Ricarica_cod_cliente FOREIGN KEY (cod_cliente)
    REFERENCES Dis_Cliente(cod_cliente)
    ON UPDATE CASCADE
    ON DELETE NO ACTION,
    CONSTRAINT fk_Ricarica_cod_distributore FOREIGN KEY (cod_distributore)
    REFERENCES Dis_Distributore(cod_distributore)
    ON UPDATE CASCADE
    ON DELETE NO ACTION
    -- UNIQUE(cod_cliente, cod_distributore)
) ENGINE=INNODB;

CREATE TABLE Dis_Acquisto (
    cod_acquisto INT NOT NULL AUTO_INCREMENT,
    cod_cliente INT NOT NULL,
    cod_distributore INT NOT NULL,
    cod_prodotto INT NOT NULL,
    data_acquisto DATETIME NOT NULL,
    contabilizzato BOOLEAN DEFAULT 0,
    CONSTRAINT pk_cod_acquisto PRIMARY KEY (cod_acquisto),
    CONSTRAINT fk_Acquisto_cod_distributore FOREIGN KEY (cod_distributore)
    REFERENCES Dis_Distributore(cod_distributore)
    ON UPDATE CASCADE
    ON DELETE NO ACTION,
    CONSTRAINT fk_Acquisto_cod_prodotto FOREIGN KEY (cod_prodotto)
    REFERENCES Dis_Prodotto(cod_prodotto)
    ON UPDATE CASCADE
    ON DELETE NO ACTION,
    CONSTRAINT fk_Acquisto_cod_cliente FOREIGN KEY (cod_cliente)
    REFERENCES Dis_Cliente(cod_cliente)
    ON UPDATE CASCADE
    ON DELETE NO ACTION
) ENGINE=INNODB;

INSERT INTO Dis_Distributore (desc_distributore, litri_totali, litri_residui)
VALUES
    ('distributore Polo1', 100, 50),          -- 1
    ('distributore Polo3', 500, 450),         -- 2
    ('distributore Biennio', 800, 800);       -- 3

INSERT INTO Dis_Prodotto (desc_prodotto, prezzo)
VALUES
    ("Caffè", 0.50),            -- 1
    ("Decaffeinato", 0.50),     -- 2
    ("Te'", 0.80),              -- 3
    ("Cioccolata", 0.50),      -- 4
    ("Kinder Bueno", 1.00),     -- 5
    ("Croccantelle", 0.60),     -- 6
    ("Acqua", 1.00);            -- 7

INSERT INTO Dis_Cliente (nome, cognome, saldo)
VALUES
    ("utente", "non registrato", 0.00) ,    -- 1
    ("Mario", "Rossi", 2.00),               -- 2
    ("Luigi", "Bianchi", 0.00),             -- 3
    ("Maria", "Verdi", 7.00),               -- 4
    ("Beniamino", "Vagnarelli", 10.00),     -- 5
    ("Gianluca", "Violoni", 780.00),        -- 6
    ("Lorenzo", "Liguori", 3.00),           -- 7
    ("Mattia", "Vesprini", 15.00);          -- 8

INSERT INTO Dis_ProdottoDisponibile (cod_distributore, cod_prodotto, quantità)
VALUES
    (1, 1, 2),
    (2, 1, 3),
    (3, 1, 2),
    (1, 2, 4),
    (2, 2, 5),
    (3, 2, 2),
    (1, 3, 3),
    (2, 3, 2),
    (3, 3, 4),
    (1, 4, 2),
    (2, 4, 3),
    (3, 4, 2);

INSERT INTO Dis_Ricarica (cod_cliente, cod_distributore, importo, data_ricarica, contabilizzata)
VALUES
    (2, 2, 2.00, '2021-12-15 10:20:54', 1),
    (3, 3, 3.00, '2021-12-15 8:20:54', 1),
    (4, 1, 1.00, '2021-12-15 9:20:54', 1),
    (2, 2, 2.00, '2021-12-16 10:20:54', 1),
    (3, 3, 3.00, '2021-12-16 8:20:54', 1),
    (4, 1, 1.00, '2021-12-16 9:20:54', 1),
    (2, 2, 2.00, '2021-12-17 10:20:54', 1),
    (3, 3, 3.00, '2021-12-17 8:20:54', 1),
    (4, 1, 1.00, '2021-12-17 9:20:54', 1),
    (2, 2, 2.00, '2021-12-18 10:20:54', 1),
    (3, 3, 3.00, '2021-12-18 8:20:54', 1),
    (4, 1, 1.00, '2021-12-18 9:20:54', 1),
    (2, 2, 2.00, '2021-12-19 10:20:54', 1),
    (3, 3, 3.00, '2021-12-19 8:20:54', 1),
    (4, 1, 1.00, '2021-12-19 9:20:54', 1);

INSERT INTO Dis_Acquisto (cod_cliente, cod_distributore, cod_prodotto, data_acquisto, contabilizzato)
VALUES
    (1, 3, 4, '2021-12-14 7:20:54', 1),
    (1, 3, 2, '2021-12-14 6:20:54', 1),
    (1, 3, 3, '2021-12-14 12:20:54', 1),
    (2, 2, 2, '2021-12-14 10:20:54', 1),
    (3, 3, 3, '2021-12-14 8:20:54', 1),
    (4, 1, 4, '2021-12-14 9:20:54', 1),
    (1, 3, 4, '2021-12-15 7:20:54', 1),
    (1, 3, 2, '2021-12-15 6:20:54', 1),
    (1, 3, 3, '2021-12-15 12:20:54', 1),
    (2, 2, 4, '2021-12-15 10:20:54', 1),
    (3, 3, 3, '2021-12-15 8:20:54', 1),
    (4, 1, 1, '2021-12-15 9:20:54', 1),
    (1, 3, 4, '2021-12-16 7:20:54', 1),
    (1, 3, 2, '2021-12-16 6:20:54', 1),
    (1, 3, 3, '2021-12-16 12:20:54', 1),
    (2, 2, 2, '2021-12-16 10:20:54', 1),
    (3, 3, 1, '2021-12-16 8:20:54', 1),
    (4, 1, 3, '2021-12-16 9:20:54', 1),
    (1, 3, 4, '2021-12-17 7:20:54', 1),
    (1, 3, 2, '2021-12-17 6:20:54', 1),
    (1, 3, 3, '2021-12-17 12:20:54', 1),
    (2, 2, 4, '2021-12-17 10:20:54', 1),
    (3, 3, 2, '2021-12-17 8:20:54', 1),
    (4, 1, 3, '2021-12-17 9:20:54', 1),
    (1, 3, 4, '2021-12-18 7:20:54', 1),
    (1, 3, 2, '2021-12-18 6:20:54', 1),
    (1, 3, 3, '2021-12-18 12:20:54', 1),
    (2, 2, 1, '2021-12-18 10:20:54', 1),
    (3, 3, 2, '2021-12-18 8:20:54', 1),
    (4, 1, 4, '2021-12-18 9:20:54', 1),
    (1, 3, 4, '2021-12-19 7:20:54', 1),
    (1, 3, 2, '2021-12-19 6:20:54', 1),
    (1, 3, 3, '2021-12-19 12:20:54', 1),
    (2, 2, 3, '2021-12-19 10:20:54', 1),
    (3, 3, 2, '2021-12-19 8:20:54', 1),
    (4, 1, 1, '2021-12-19 9:20:54', 1),
    (1, 3, 4, '2021-12-20 7:20:54', 1),
    (1, 3, 2, '2021-12-20 6:20:54', 1),
    (1, 3, 3, '2021-12-20 12:20:54', 1),
    (2, 3, 1, '2021-12-20 10:20:54', 1),
    (3, 2, 2, '2021-12-20 8:20:54', 1),
    (4, 1, 4, '2021-12-20 9:20:54', 1);
