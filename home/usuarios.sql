DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id       BIGSERIAL     PRIMARY KEY,
    usuario  VARCHAR(255),
    password VARCHAR(255)  NOT NULL,
    token    VARCHAR(255),
    intentos SMALLINT      DEFAULT 0,
    admin    BOOL          DEFAULT false
);
