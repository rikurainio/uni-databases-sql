DROP TABLE IF EXISTS TILIT;

CREATE TABLE TILIT (
    tilinumero VARCHAR(34) PRIMARY KEY,
    omistaja   VARCHAR(100) NOT NULL,
    summa      NUMERIC(12,2) NOT NULL CHECK (summa >= 0)
);

INSERT INTO TILIT (tilinumero, omistaja, summa) VALUES
('FI11 1234 5600 0007', 'Maija Meikäläinen', 1500.00),
('FI22 2345 6700 0008', 'Matti Mallikas', 250.50),
('FI33 3456 7800 0009', 'Teemu Testaaja', 980.75),
('FI44 4567 8900 0010', 'Laura Laine', 2200.25),
('FI55 5678 9000 0011', 'Pekka Peloton', 75.00);


