-- TABLAS
DROP TABLE IF EXISTS usuarios CASCADE;
CREATE TABLE usuarios
(
  id             SERIAL        PRIMARY KEY,
  usuario        VARCHAR(255),
  password       VARCHAR(255)  NOT NULL,
  token          VARCHAR(255),
  mail           VARCHAR(255),
  telefono       VARCHAR(255),
  notificaciones BOOLEAN       DEFAULT 1,
  intentos       SMALLINT      DEFAULT 0,
  admin          BOOLEAN       DEFAULT 0
);

DROP TABLE IF EXISTS parametros CASCADE;
CREATE TABLE parametros
(
  nombre VARCHAR(16) PRIMARY KEY,
  valor  VARCHAR(255)
);

DROP TABLE IF EXISTS logs CASCADE;
CREATE TABLE logs
(
  id           SERIAL        PRIMARY KEY,
  mensaje      VARCHAR(255),
  created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS placas CASCADE;
CREATE TABLE placas
(
  id         SERIAL   PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  temp       NUMERIC(3)
);

DROP TABLE IF EXISTS habitaciones CASCADE;
CREATE TABLE habitaciones
(
  id     SERIAL       PRIMARY KEY,
  nombre VARCHAR(255),
  icono  VARCHAR(255)
);

DROP TABLE IF EXISTS persianas CASCADE;
CREATE TABLE persianas
(
  id            SERIAL             PRIMARY KEY,
  codigo        VARCHAR(10),
  habitacion_id BIGINT(20) unsigned,
  posicion1     NUMERIC(3),
  posicion2     NUMERIC(3),
  posicion3     NUMERIC(3),
  posicion4     NUMERIC(4),
  CONSTRAINT fk_per_hab_id         FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id)
);

DROP TABLE IF EXISTS actuadores CASCADE;
CREATE TABLE actuadores
(
  id            SERIAL             PRIMARY KEY,
  nombre        VARCHAR(255),
  codigo        VARCHAR(10),
  tipo          VARCHAR(10),
  switch        VARCHAR(255),
  icono         VARCHAR(255),
  habitacion_id BIGINT(20) unsigned,
  CONSTRAINT fk_act_hab_id         FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id)
);

DROP TABLE IF EXISTS sensores CASCADE;
CREATE TABLE sensores
(
  pin    TINYINT       PRIMARY KEY,
  nombre VARCHAR(255),
  activo BOOLEAN       DEFAULT 1,
  icono  VARCHAR(255)
);

DROP TABLE IF EXISTS rfid CASCADE;
CREATE TABLE rfid
(
  usuario_id BIGINT(20) unsigned,
  code       VARCHAR(255),
  CONSTRAINT fk_rfid_usu_id       FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);



-- Insertamos algunos valores
INSERT INTO parametros (nombre, valor)
  VALUES ('estado_alarma', '0'),
         ('per_all', '0x1A 0x14 0x15 0x16 0x17 0x18 0x19'),
         ('per_central', '0x1B'),
         ('per_pbaja', '0x1D 0x14 0x15'),
         ('per_palta', '0x1E 0x16 0x17 0x18 0x19'),
         ('per_paonorte', '0x1F 0x18 0x19'),
         ('per_paosur', '0x20 0x16 0x17'),
         ('per_switch1', '0x62'),
         ('per_switch2', '0x63'),
         ('per_subir', '0x64'),
         ('per_bajar', '0x65'),
         ('per_pos1', '0x66'),
         ('per_pos2', '0x67'),
         ('per_pos3', '0x68'),
         ('per_grabar', '0x69'),
         ('per_solicitar', '0x6A'),
         ('per_parar', '0x6B'),
         ('per_onorte', '0x6C 0x15 0x18 0x19'),
         ('per_osur', '0x6D 0x14 0x16 0x17'),
         ('per_switch3', '0x70'),
         ('per_switch4', '0x71'),
         ('per_switch5', '0x72'),
         ('per_switch6', '0x73'),
         ('per_switch7', '0x74'),
         ('per_switch8', '0x75');

INSERT INTO habitaciones (nombre, icono)
  VALUES ('Salón', 'salon'),
         ('C Ordenador', 'ordenador'),
         ('Dormitorio matr.', 'dormitorio1'),
         ('Bano matr.', 'bano'),
         ('Dormitorio der.', 'dormitorio2'),
         ('Dormitorio izq.', 'dormitorio3'),
         ('Patio', 'patio');

INSERT INTO persianas (codigo, habitacion_id, posicion1, posicion2, posicion3, posicion4)
VALUES ('0x14', 1, 6, 14, 20, 28),
       ('0x15', 2, 4, 11, 14, 18),
       ('0x16', 3, 9, 20, 28, 35),
       ('0x17', 4, 5, 12, 18, 22),
       ('0x18', 5, 7, 12, 19, 27),
       ('0x19', 6, 10, 18, 28, 37);

INSERT INTO actuadores (nombre, codigo, tipo, switch, icono, habitacion_id)
VALUES ('Lamparita', '0x14', 'I', 'per_switch1', 'lamparita', 1);

INSERT INTO sensores (pin, nombre)
  VALUES (5, 'salón'),
         (6, 'cuarto ordenador'),
         (13, 'distribuidor arriba'),
         (19, 'puerta principal');

INSERT INTO rfid (usuario_id, code)
  VALUES (1, 'a16c1624ff0d0a'),
         (2, '1133e42bed0d0a');