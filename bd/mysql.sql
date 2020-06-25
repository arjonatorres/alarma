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
  nombre    VARCHAR(255) PRIMARY KEY,
  valor     VARCHAR(255),
  adicional VARCHAR(255)
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
  id     SERIAL        PRIMARY KEY,
  codigo VARCHAR(2),
  nombre VARCHAR(255),
  icono  VARCHAR(255),
  tipo   VARCHAR(10)
);

DROP TABLE IF EXISTS persianas CASCADE;
CREATE TABLE persianas
(
  id            SERIAL             PRIMARY KEY,
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
  tipo          VARCHAR(10),
  switch        NUMERIC(2),
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
INSERT INTO parametros (nombre, valor, adicional)
  VALUES ('estado_alarma', '0', DEFAULT),
         ('per_normal', '0A', DEFAULT),
         ('per_subiendo', '0B', DEFAULT),
         ('per_bajando', '0C', DEFAULT),
         ('per_all', '60 10 11 12 13 14 15', DEFAULT),
         ('per_pbaja', '61 10 11', 'planta baja'),
         ('per_palta', '62 12 13 14 15', 'planta alta'),
         ('per_onorte', '63 11 14 15', 'norte'),
         ('per_osur', '64 10 12 13', 'sur'),
         ('per_paonorte', '65 14 15', 'planta alta norte'),
         ('per_paosur', '66 12 13', 'planta alta sur'),
         ('per_bajar', '70', 'bajando'),
         ('per_pos1', '71', 'posicion 1'),
         ('per_pos2', '72', 'posicion 2'),
         ('per_pos3', '73', 'posicion 3'),
         ('per_subir', '74', 'subiendo'),
         ('per_grabar', '75', 'grabar'),
         ('per_solicitar', '76', 'solicitar'),
         ('per_solicitar_eeprom', '77', 'solicitar eeprom'),
         ('per_parar', '78', 'parar'),
         ('per_pulsador', '79', 'pulsador'),
         ('per_switch1', '80', DEFAULT),
         ('per_switch2', '81', DEFAULT),
         ('per_switch3', '82', DEFAULT),
         ('per_switch4', '83', DEFAULT),
         ('per_switch5', '84', DEFAULT),
         ('per_switch6', '85', DEFAULT),
         ('per_switch7', '86', DEFAULT),
         ('per_switch8', '87', DEFAULT);

INSERT INTO habitaciones (codigo, nombre, icono, tipo)
  VALUES ('10', 'Salón', 'salon', 'P2'),
         ('11', 'Cuarto ordenador', 'ordenador', 'P2'),
         ('12', 'Dormitorio matrimonio', 'dormitorio1', 'P2'),
         ('13', 'Baño matrimonio', 'bano', 'P2'),
         ('14', 'Dormitorio derecha', 'dormitorio2', 'P2'),
         ('15', 'Dormitorio izquierda', 'dormitorio3', 'P2'),
         ('16', 'Patio', 'patio', 'R8');

INSERT INTO persianas (habitacion_id, posicion1, posicion2, posicion3, posicion4)
VALUES (1, 6, 14, 20, 28),
       (2, 4, 11, 14, 18),
       (3, 9, 20, 28, 35),
       (4, 5, 12, 18, 22),
       (5, 7, 12, 19, 27),
       (6, 10, 18, 28, 37);

INSERT INTO actuadores (nombre, tipo, switch, icono, habitacion_id)
VALUES ('Lamparita', 'I', 1, 'lamp', 1),
       ('Lampara', 'I', 1, 'lamparita', 2);

INSERT INTO sensores (pin, nombre)
  VALUES (5, 'salón'),
         (6, 'cuarto ordenador'),
         (13, 'distribuidor arriba'),
         (19, 'puerta principal');

INSERT INTO rfid (usuario_id, code)
  VALUES (1, 'a16c1624ff0d0a'),
         (2, '1133e42bed0d0a');