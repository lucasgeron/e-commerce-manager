/* scripts para criação do banco de dados */
DROP DATABASE IF EXISTS provweb;

CREATE DATABASE provweb;

USE provweb;
--
-- TABLES DEFAULT FOR PROVWEB
--
CREATE TABLE users (
  id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cities (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
);

CREATE TABLE clients (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  address VARCHAR(100) DEFAULT NULL,
  address_number INT(11) DEFAULT NULL,
  district VARCHAR (50) DEFAULT NULL,
  address_cep VARCHAR(10) DEFAULT NULL,
  phone VARCHAR(11) DEFAULT NULL,
  email VARCHAR(50) DEFAULT NULL,
  type INT(11)DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status INT DEFAULT 0,

  city_id INT(11) REFERENCES cities(id),
  user_id INT(11) REFERENCES users(id)
);

CREATE TABLE clients_pf (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  cpf VARCHAR(11),
  date_of_birth DATE NULL DEFAULT NULL,

  client_id INT(11) REFERENCES clients(id)
);

CREATE TABLE clients_pj(
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  cnpj VARCHAR(14),
  social_reason VARCHAR(50),

  client_id INT(11) REFERENCES clients(id)
);

CREATE TABLE product_categories (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  status INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);

CREATE TABLE products (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  amount FLOAT NOT NULL,
  selling_price DOUBLE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status INT DEFAULT 0,

  category_id INT(11) REFERENCES product_categories(id)
);

CREATE TABLE selling_orders (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  total DOUBLE DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status INT DEFAULT 0,
  closed_at TIMESTAMP NULL DEFAULT NULL,


  client_id INT(11) REFERENCES clients(id),
  user_id INT (11) REFERENCES users(id)
);

CREATE TABLE itens_selling_orders (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  amount INT(11) NOT NULL,
  item_price DOUBLE NOT NULL,

  selling_order_id INT(11) REFERENCES selling_orders(id),
  product_id INT(11) REFERENCES products (id)
);

--
-- DATABASE DEFAULT FOR PROVWEB
--

INSERT INTO users (name,email,password) VALUES -- default password: 'nome (minusculo)'
('Renan', 'renan@provweb','55b71c5fc3b4c133a3680020c8c16c10d3a5a99f'),
('Jefferson', 'jefferson@provweb','83946302e945c7aab62377f2aec7ced0f0539de4'),
('Diego', 'diego@provweb','5c0538180401fa0fe16325eec7296a1995dc8cc6'),
('Guilherme', 'guilherme@provweb','66b00b12d4693b1ad31ced931bcaa8a5051f4402'),
('Lucas', 'lucas@provweb','b2982aa45bf6bfe1f87efca5ea719075e3095cc5');



INSERT INTO cities (name) VALUES
('Guarapuava'),
('Pato Branco'),
('Curitiba'),
('Florianópolis');


INSERT INTO clients (name, address, address_number, district, address_cep, phone, email, city_id, user_id, type) VALUES
('Samuel Carlos Eduardo de Paula', 'Rua Ângelo Dalla Vechia', '852', 'Primavera', '85050250', '04295698044', 'samuel.carlos.paula@lucaslima.com', 1, 3, 1),
('Gabriel Miguel Paulo Alves', 'Rua Doutor Alfredo Stolz', '275', 'Batel', '85015475', '04238878827', 'gabriel-miguel92@granvale.com.br', 1, 3 ,1),
('Ian Rodrigo Souza', 'Rua das Guabirobeiras', '961', 'Morro Alto', '85067000', '04239011506', 'irsouza@rj.net', 1, 1, 1),
('Kaique Levi Heitor Moura', 'Rua Cambira', '875', 'Boqueirão', '85020670', '04287930685', 'kaique_levi@policiamilitar.sp.gov.br', 1, 2, 1),
('João Guilherme Oliveira', 'Travessa Jaú', '654', 'São Roque', '85507170', '04626360162', 'joao_g_oliveira@polifiltro.com.br',2 ,1 , 1),
('Paulo Pedro Oliveira', 'Rua Prudêncio Alves de Oliveira', '852', 'Cadorin', '85504590', '04637270878', 'pprodrigues@publifix.com.br', 2, 3, 1),
('Raul Heitor Renato Barbosa', 'Rua Arquimedes Cruz', '639', 'Jardim Social', '82520020', '04125554955', 'raul_heitor@oul.com.br', 3, 2, 1),
('AL Eletronica', 'Rua Sebastião Gomes Oliveira', '701', 'Pinheirinho', '81825250', '04128885093', 'suporte@aleletronica.com.br', 3, 1 , 2),
('IG Express', 'Rua Niterói', '949', 'Águas Belas', '83010600', '04135767401', 'vendas@igexpress.com.br',3 ,2, 2),
('GM Games', 'Rua Arsênio de Azevedo', '513', 'Cajuru', '82940040', '04129427406', 'vendas@gmgames.com.br',3, 1, 2);


INSERT INTO clients_pf (cpf, date_of_birth, client_id) VALUES
('68194020905', '1994-04-11', 1),
('27989238403', '1994-10-17', 2),
('07857208558', '1993-02-11', 3),
('53101294215', '1993-01-19', 4),
('72888738546', '1993-10-27', 5),
('41747734080', '1993-01-15', 6),
('39731057587', '1990-03-16', 7);


INSERT INTO clients_pj (cnpj, social_reason, client_id) VALUES
('54618560000133', 'Alana e Lorena Eletrônica ME', 8),
('27398206000278', 'Igor e Luan Entregas Expressas Ltda', 9),
('50779823000135', 'Gabriel e Melissa Comercio de Jogos Ltda', 10);


INSERT INTO product_categories (id, name) VALUES
(1, 'PS3'),
(2, 'PS4'),
(3, 'PC'),
(4, 'XBOX 360'),
(5, 'XBOX ONE');

INSERT INTO products (name, category_id, amount, selling_price) VALUES
('Gran Turismo 6 - Especial Edition', 1, 10, 34.90),
('Beyond: Two Souls', 1, 10, 39.90),
('Battlefield 3', 1, 10, 39.90),
('Call of Duty - Modern Warfare 3', 1, 10, 79.90),
('Destiny - The Taken King', 1, 10, 129.90),
('Dead Island - Riptide', 1, 10, 52.90),
("Assassin's Creed: Revelations", 1, 10, 119.90),
('BioShock Infinite', 1, 10, 58.90),
('Red Dead Redemption ', 1, 10, 79.90),
('Grand Theft Auto V', 1, 10, 184.90),
--
('UnCHARted 4 - A Thief`s End', 2, 10, 169.90),
('Dark Souls III', 2, 10, 199.90),
('The Order 1886', 2, 10, 79.90),
('Grand Theft Auto V', 2, 10, 199.90),
('Tom Clancy’s - The Division', 2, 10, 179.90),
('Dying Light', 2, 10, 149.90),
('Destiny - The Taken King', 2, 10, 199.90),
('Call of Duty: Black Ops 3', 2, 10, 162.90),
('Shadow of Mordor', 2, 10, 134.90),
('Far Cry 4', 2, 10, 119.90),
--
('Diablo III', 3, 10, 59.90),
('Far Cry 4', 3, 10, 41.90),
('The Sims 3', 3, 10, 39.90),
('Batman: Arkham Origins', 3, 10, 27.90),
('Mortal Kombat X', 3, 10, 44.90),
('BioShock 2', 3, 10, 19.90),
('Watch Dogs', 3, 10, 59.90),
('The Witcher 3', 3, 10, 149.90),
('Dead Space', 3, 10, 19.90),
('Minecraft', 3, 10, 124.90),
--
('Grand Theft Auto V', 4, 10, 149.90),
('Metal Gear Rising', 4, 10, 9.90),
('Gears of War 3', 4, 10, 49.90),
('Forza: Horizon', 4, 10, 79.90),
('Titanfall', 4, 10, 59.90),
("Assassin's Creed IV: Black Flag", 4, 10, 69.90),
('Destiny', 4, 10, 74.90),
('Thief', 4, 10, 41.90),
('SoulCalibur V', 4, 10, 59.90),
('PES 2015', 4, 10, 94.90),
--
('Halo 5: Guardians', 5, 10, 69.90),
('Grand Theft Auto V', 5, 10, 199.90),
('Tom Clancy’s - The Division', 5, 10, 179.90),
('Rise of the Tomb Raider', 5, 10, 119.90),
('Dark Souls III', 5, 10, 199.90),
('Forza: Horizon 2', 5, 10, 94.90),
('Dying Light', 5, 10, 149.90),
('Dead Rising 3', 5, 10, 84.90),
('Dragon Age: Inquisition', 5, 10, 184.90),
('Shadow of Mordor', 5, 10, 134.90);

INSERT INTO selling_orders (client_id, user_id, total, status, closed_at) VALUES
(5,5,'74.80',0, NULL),
(10,5,'134.90',1, "2016-06-04 14:25:00"),
(7,1,'179.90',1, "2016-06-07 14:02:00"),
(6,3,'279.80',0, NULL),
(4,3,'59.90',1, "2016-06-10 14:52:00"),
(9,4,'124.80',0, NULL),
(8,5,'199.90',0, NULL),
(7,1,'359.80',0, NULL),
(2,2,'129.90',0, NULL),
(3,2,'334.80',1, "2016-06-13 13:34:51"),
(5,5,'199.90',1, "2016-06-14 16:39:23"),
(6,4,'199.90',1, "2016-06-16 11:12:58"),
(3,3,'204.80',1, "2016-06-17 15:53:23"),
(1,3,'119.90',1, "2016-06-17 08:59:20"),
(10,3,'41.90',1, "2016-06-18 09:13:34");


INSERT INTO itens_selling_orders (selling_order_id, product_id, amount, item_price) VALUES
(1, 2, 1, '39.90'),
(1, 1, 1, '34.90'),
(2, 50, 1, '134.90'),
(3, 43, 1, '179.90'),
(4, 12, 1, '199.9'),
(4, 13, 1, '79.90'),
(5, 21, 1, '59.90'),
(6, 33, 1, '49.90'),
(6, 37, 1, '74.90'),
(7, 12, 1, '199.90'),
(8, 43, 2, '179.90'),
(9, 5, 1, '129.90'),
(10, 17, 1, '199.90'),
(10, 19, 1, '134.90'),
(11, 17, 1, '199.90'),
(12, 17, 1, '199.90'),
(13, 50, 1, '134.90'),
(13, 41, 1, '69.90'),
(14, 44, 1, '119.90'),
(15, 38, 1, '41.90');
