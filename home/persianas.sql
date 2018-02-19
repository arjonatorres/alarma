drop table if exists persianas cascade;

create table persianas
(
    id bigserial constraint pk_persianas primary key,
    codigo numeric(3),
    posicion1 numeric(3),
    posicion2 numeric(3),
    posicion3 numeric(3),
    posicion4 numeric(4)
);

insert into persianas (codigo, posicion1, posicion2, posicion3, posicion4)
values (14, 0, 0, 0, 0),
       (15, 0, 0, 0, 0),
       (16, 0, 0, 0, 0),
       (17, 0, 0, 0, 0),
       (18, 0, 0, 0, 0),
       (19, 0, 0, 0, 0);

