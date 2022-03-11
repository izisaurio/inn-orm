CREATE DATABASE tests
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE tests;

/* Tables */

CREATE TABLE users(
	id int AUTO_INCREMENT NOT NULL,
	name varchar(155) NOT NULL,
	email varchar(155) NOT NULL,
	phone bigint NOT NULL,
	CONSTRAINT PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE tasks(
	id int AUTO_INCREMENT NOT NULL,
	name varchar(155) NOT NULL,
	idUser int NOT NULL,
	CONSTRAINT PRIMARY KEY (id),
	FOREIGN KEY (idUser) REFERENCES users (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE subjects(
	id int AUTO_INCREMENT NOT NULL,
	name varchar(155) NOT NULL,
	CONSTRAINT PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE users_subjects(
	idUser int NOT NULL,
	idSubject int NOT NULL,
	CONSTRAINT PRIMARY KEY (idUser, idSubject),
	FOREIGN KEY (idUser) REFERENCES users (id),
	FOREIGN KEY (idSubject) REFERENCES subjects (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

INSERT INTO users VALUES (0, 'izisaurio', 'izi.isaac@gmail.com', 1234567890);
INSERT INTO tasks VALUES (0, 'develop', 1);
INSERT INTO subjects VALUES (0, 'first');
INSERT INTO subjects VALUES (0, 'second');
INSERT INTO subjects VALUES (0, 'third');
INSERT INTO users_subjects VALUES (1, 1);
INSERT INTO users_subjects VALUES (1, 2);
INSERT INTO users_subjects VALUES (1, 3);