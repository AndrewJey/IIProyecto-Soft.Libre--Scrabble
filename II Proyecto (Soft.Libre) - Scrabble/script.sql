drop table if exists 
letra, juego, juego_usuario, juego_tablero_usuario, 
usuario, inventario_letra_juego, diccionario;
--Representa la entida letra y sus atributos propios
--ESta tabla debe ser llenada con la información del documento
create table letra(
	id serial not null primary key,
	letra varchar not null,
	puntaje integer not null,
	cantidad integer not null
);

--Define las palabras válidas para todos los juegos de scrable
create table diccionario(
	id serial not null primary key,
	palabra varchar not null unique
);

--Define las cantidades restantes por cada letra para cada juego
create table inventario_letra_juego(
	id serial not null primary key,
	letra_id integer not null,
	cantidad integer not null,
	juego_id integer not null,
	unique(letra_id, cantidad)
);

-- Entidad juego, se crea un registro por cada juego nuevo
create table juego(
	id serial not null primary key,
	activo boolean not null default true
);

-- Asocia a los usuarios con los juegos existentes
create table juego_usuario(
	id serial not null primary key,
	juego_id integer not null,
	orden integer not null,--útil para saber el orden de los turnos
	usuario_id integer not null,
	tiene_turno boolean not null default true,
	a_salido boolean not null default false,
	a_ganado boolean not null default false
);

-- Define que letra a puesto cada usuario para cada juego en el tablero
create table juego_tablero_usuario(
	id serial not null primary key,
	x integer not null,
	y integer not null,
	letra_id integer not null,
	juego_usuario_id integer not null
);

create table usuario(
	id serial not null primary key,
	nombre varchar not null,
	clave varchar not null
);

alter table juego_tablero_usuario add constraint fk_juego_tablero_usuario_letra foreign key (letra_id) references letra(id);
alter table inventario_letra_juego add constraint fk_inventario_letra_juego_letra foreign key (letra_id) references letra(id);

alter table inventario_letra_juego add constraint fk_inventario_letra_juego_juego foreign key (juego_id) references juego(id);
alter table juego_usuario add constraint fk_juego_usuario_juego foreign key (juego_id) references juego(id);

alter table juego_usuario add constraint fk_juego_usuario_usuario foreign key (usuario_id) references usuario(id);
alter table juego_tablero_usuario add constraint fk_juego_tablero_usuario_juego_usuario foreign key (juego_usuario_id) references juego_usuario(id);