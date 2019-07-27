drop table if exists placas cascade;

create table placas
(
    id bigserial constraint pk_placas primary key,
    created_at timestamptz(0) not null default current_timestamp,
    temp numeric(3)
);
