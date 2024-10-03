-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS fut6_liga;

USE fut6_liga;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario'
);

CREATE TABLE equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de jugadores
CREATE TABLE IF NOT EXISTS jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    posicion ENUM(
        'Portero',
        'Defensa',
        'Mediocampista',
        'Delantero'
    ) NOT NULL,
    equipo_id INT NOT NULL,
    numero_camisa INT NOT NULL,
    goles INT DEFAULT 0,
    FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON DELETE CASCADE
);

-- Tabla de partidos
CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_local INT NOT NULL,
    equipo_visitante INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    lugar VARCHAR(100) NOT NULL,
    FOREIGN KEY (equipo_local) REFERENCES equipos (id) ON DELETE CASCADE,
    FOREIGN KEY (equipo_visitante) REFERENCES equipos (id) ON DELETE CASCADE
);

-- Tabla de la tabla general
CREATE TABLE IF NOT EXISTS tabla_general (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    partidos_ganados INT DEFAULT 0,
    partidos_empatados INT DEFAULT 0,
    partidos_perdidos INT DEFAULT 0,
    goles_favor INT DEFAULT 0,
    goles_contra INT DEFAULT 0,
    puntos INT DEFAULT 0,
    FOREIGN KEY (equipo_id) REFERENCES equipos (id) ON DELETE CASCADE
);

-- Tabla de goleo
CREATE TABLE IF NOT EXISTS goleo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT NOT NULL,
    goles INT DEFAULT 0,
    FOREIGN KEY (jugador_id) REFERENCES jugadores (id) ON DELETE CASCADE
);

-- Insertar un administrador por defecto
INSERT INTO
    usuarios (username, password, rol)
VALUES (
        'admin',
        '$2y$10$eImiTXuWVxfM37uY4JANjQeFbO.uyF1AVaWjEVLaX/wHjFUrQSB2G',
        'admin'
    );
-- Nota: La contraseña es 'admin' cifrada con bcrypt
-- Insertar equipos de ejemplo
INSERT INTO
    equipos (nombre)
VALUES ('Equipo A'),
    ('Equipo B'),
    ('Equipo C'),
    ('Equipo D'),
    ('Equipo E'),
    ('Equipo F'),
    ('Equipo G'),
    ('Equipo H');

-- Insertar jugadores de ejemplo
INSERT INTO
    jugadores (
        nombre,
        posicion,
        equipo_id,
        numero_camisa
    )
VALUES ('Jugador 1', 'Portero', 1, 1),
    ('Jugador 2', 'Defensa', 1, 2),
    (
        'Jugador 3',
        'Mediocampista',
        2,
        3
    ),
    (
        'Jugador 4',
        'Delantero',
        2,
        4
    ),
    ('Jugador 5', 'Portero', 3, 1),
    ('Jugador 6', 'Defensa', 3, 2),
    (
        'Jugador 7',
        'Mediocampista',
        4,
        3
    ),
    (
        'Jugador 8',
        'Delantero',
        4,
        4
    ),
    (
        'Jugador 9',
        'Delantero',
        5,
        10
    ),
    (
        'Jugador 10',
        'Mediocampista',
        5,
        8
    ),
    ('Jugador 11', 'Defensa', 6, 5),
    ('Jugador 12', 'Portero', 6, 1),
    (
        'Jugador 13',
        'Delantero',
        7,
        9
    ),
    (
        'Jugador 14',
        'Mediocampista',
        8,
        8
    ),
    ('Jugador 15', 'Defensa', 7, 4),
    ('Jugador 16', 'Portero', 8, 1);

-- Insertar partidos de ejemplo (jornadas)
INSERT INTO
    partidos (
        equipo_local,
        equipo_visitante,
        fecha,
        hora,
        lugar
    )
VALUES (
        1,
        2,
        '2024-10-15',
        '18:00:00',
        'Estadio Principal'
    ),
    (
        3,
        4,
        '2024-10-16',
        '20:00:00',
        'Campo Secundario'
    ),
    (
        1,
        3,
        '2024-11-01',
        '19:00:00',
        'Estadio Los Ángeles'
    ),
    (
        2,
        4,
        '2024-11-02',
        '20:30:00',
        'Estadio Nacional'
    ),
    (
        5,
        6,
        '2024-11-03',
        '18:00:00',
        'Campo Olimpico'
    ),
    (
        3,
        6,
        '2024-11-04',
        '17:30:00',
        'Estadio Monumental'
    );

-- Insertar registros en la tabla general para todos los equipos
INSERT INTO
    tabla_general (
        equipo_id,
        partidos_ganados,
        partidos_empatados,
        partidos_perdidos,
        goles_favor,
        goles_contra,
        puntos
    )
VALUES (1, 2, 1, 0, 8, 3, 7),
    (2, 1, 0, 1, 5, 4, 3),
    (3, 1, 1, 1, 6, 5, 4),
    (4, 0, 0, 2, 2, 6, 0),
    (5, 1, 0, 0, 3, 1, 3),
    (6, 0, 1, 1, 2, 3, 1),
    (7, 2, 0, 0, 7, 1, 6),
    (8, 0, 0, 2, 1, 5, 0);

-- Insertar registros en la tabla de goleo para los jugadores
INSERT INTO
    goleo (jugador_id, goles)
VALUES (1, 3),
    (2, 1),
    (3, 2),
    (4, 4),
    (5, 0),
    (6, 1),
    (7, 1),
    (8, 2),
    (9, 3),
    (10, 1),
    (13, 5),
    (14, 2),
    (15, 0),
    (16, 0);