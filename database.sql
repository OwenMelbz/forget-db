PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE table_one(user_id INT(10) PRIMARY KEY UNIQUE, user_name VARCHAR(100), user_email VARCHAR(200));
INSERT INTO table_one VALUES(1,'Prof. Sherwood Grant MD','maudie83@wilkinson.com');
CREATE TABLE table_two(user_id INT(10) PRIMARY KEY UNIQUE, user_name VARCHAR(100), user_email VARCHAR(200));
INSERT INTO table_two VALUES(1,'Marcella Homenick II','kwindler@hotmail.com');
CREATE TABLE table_three(user_id INT(10) PRIMARY KEY UNIQUE, user_name VARCHAR(100), user_email VARCHAR(200), table_two_id INTEGER(3));
INSERT INTO table_three VALUES(1,'Felipa Trantow','gschaden@yahoo.com', 1);
COMMIT;
