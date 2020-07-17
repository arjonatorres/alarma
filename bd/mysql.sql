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
  nombre    VARCHAR(255)  PRIMARY KEY,
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
  id         SERIAL     PRIMARY KEY,
  created_at DATETIME   NOT NULL DEFAULT CURRENT_TIMESTAMP,
  temp       NUMERIC(3)
);

DROP TABLE IF EXISTS habitaciones CASCADE;
CREATE TABLE habitaciones
(
  id     SERIAL        PRIMARY KEY,
  nombre VARCHAR(255),
  icono  VARCHAR(255)
);

DROP TABLE IF EXISTS arduinos CASCADE;
CREATE TABLE arduinos
(
  id     SERIAL                      PRIMARY KEY,
  codigo VARCHAR(2),
  tipo   VARCHAR(10)
);

DROP TABLE IF EXISTS persianas CASCADE;
CREATE TABLE persianas
(
  id            SERIAL             PRIMARY KEY,
  nombre        VARCHAR(255),
  posicion1     NUMERIC(3),
  posicion2     NUMERIC(3),
  posicion3     NUMERIC(3),
  posicion4     NUMERIC(4),
  habitacion_id BIGINT(20) unsigned,
  arduino_id    BIGINT(20) unsigned,
  CONSTRAINT fk_per_hab_id         FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id),
  CONSTRAINT fk_per_ard_id         FOREIGN KEY (arduino_id) REFERENCES arduinos(id)
);

DROP TABLE IF EXISTS dispositivos CASCADE;
CREATE TABLE dispositivos
(
  id            SERIAL             PRIMARY KEY,
  nombre        VARCHAR(255),
  tipo          VARCHAR(10),
  switch        NUMERIC(2),
  icono         VARCHAR(255),
  habitacion_id BIGINT(20) unsigned,
  arduino_id    BIGINT(20) unsigned,
  CONSTRAINT fk_dis_hab_id         FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id),
  CONSTRAINT fk_dis_ard_id         FOREIGN KEY (arduino_id) REFERENCES arduinos(id)
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

DROP TABLE IF EXISTS horarios CASCADE;
CREATE TABLE horarios
(
  id      SERIAL       PRIMARY KEY,
  codigo  VARCHAR(2),
  orden   VARCHAR(10),
  repetir BOOLEAN,
  dias    VARCHAR(20),
  tipo    VARCHAR(10),
  hora    TIME(0),
  activo  BOOLEAN
);



-- Insertamos algunos valores
INSERT INTO parametros (nombre, valor, adicional)
  VALUES ('estado_alarma', '0', DEFAULT),
         ('per_normal', '0A', DEFAULT),
         ('per_subiendo', '0B', DEFAULT),
         ('per_bajando', '0C', DEFAULT),
         ('per_all', '60 10 11 12 13 14 15', 'todas'),
         ('per_pbaja', '61 10 11', 'planta baja'),
         ('per_palta', '62 12 13 14 15', 'planta alta'),
         ('per_onorte', '63 11 14 15', 'norte'),
         ('per_osur', '64 10 12 13', 'sur'),
         ('per_paonorte', '65 14 15', 'planta alta norte'),
         ('per_paosur', '66 12 13', 'planta alta sur'),
         ('per_bajar', '70', 'bajar'),
         ('per_pos1', '71', 'posicion 1'),
         ('per_pos2', '72', 'posicion 2'),
         ('per_pos3', '73', 'posicion 3'),
         ('per_subir', '74', 'subir'),
         ('per_grabar', '75', 'grabar'),
         ('per_solicitar', '76', 'solicitar'),
         ('per_solicitar_eeprom', '77', 'solicitar eeprom'),
         ('per_parar', '78', 'parar'),
         ('per_switch_pulsador', '79', 'pulsador'),
         ('per_encender', '7A', 'encender'),
         ('per_apagar', '7B', 'apagar'),
         ('per_switch_all', '80', DEFAULT),
         ('per_switch1', '81', DEFAULT),
         ('per_switch2', '82', DEFAULT),
         ('per_switch3', '83', DEFAULT),
         ('per_switch4', '84', DEFAULT),
         ('per_switch5', '85', DEFAULT),
         ('per_switch6', '86', DEFAULT),
         ('per_switch7', '87', DEFAULT),
         ('per_switch8', '88', DEFAULT);

INSERT INTO habitaciones (nombre, icono)
  VALUES ('Salón', 'salon'),
         ('Cuarto ordenador', 'ordenador'),
         ('Dormitorio matrimonio', 'dormitorio1'),
         ('Baño matrimonio', 'bano'),
         ('Dormitorio derecha', 'dormitorio2'),
         ('Dormitorio izquierda', 'dormitorio3'),
         ('Patio', 'patio');

INSERT INTO arduinos (codigo, tipo)
  VALUES ('10', 'P2'),
         ('11', 'P2'),
         ('12', 'P2'),
         ('13', 'P2'),
         ('14', 'P2'),
         ('15', 'P2'),
         ('16', 'R8');

INSERT INTO persianas (nombre, posicion1, posicion2, posicion3, posicion4, habitacion_id, arduino_id)
VALUES ('Persiana', 6, 14, 20, 28, 1, 1),
       ('Persiana', 4, 11, 14, 18, 2, 2),
       ('Persiana', 9, 20, 28, 35, 3, 3),
       ('Persiana', 5, 12, 18, 22, 4, 4),
       ('Persiana', 7, 12, 19, 27, 5, 5),
       ('Persiana', 10, 18, 28, 37, 6, 6);

INSERT INTO dispositivos (nombre, tipo, switch, icono, habitacion_id, arduino_id)
VALUES ('Lamparita', 'I', 1, 'lamp', 1, 1),
       ('Lampara', 'I', 1, 'lamparita', 2, 2);

INSERT INTO sensores (pin, nombre)
  VALUES (5, 'salón'),
         (6, 'cuarto ordenador'),
         (13, 'distribuidor arriba'),
         (19, 'puerta principal');

INSERT INTO rfid (usuario_id, code)
  VALUES (1, 'a16c1624ff0d0a'),
         (2, '1133e42bed0d0a');

-- tipo: hora(hora normal), alba+-(amanecer), ocaso+-(anochecer)
INSERT INTO horarios (codigo, orden, repetir, dias, tipo, hora, activo)
  VALUES ('15', '7A81', true, '0,1,2,3,4,5,6', 'hora', '15:00:00', true);