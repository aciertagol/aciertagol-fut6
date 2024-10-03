<?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Manejar las diferentes acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_team':
            $nombre_equipo = $_POST['nombre_equipo'];
            try {
                $stmt = $pdo->prepare('INSERT INTO equipos (nombre) VALUES (?)');
                $stmt->execute([$nombre_equipo]);
                $message = "Equipo añadido correctamente";
            } catch (PDOException $e) {
                $message = "Error al añadir el equipo: " . $e->getMessage();
            }
            break;

        case 'delete_team':
            $equipo_id = $_POST['equipo_id'];
            try {
                $stmt = $pdo->prepare('DELETE FROM equipos WHERE id = ?');
                $stmt->execute([$equipo_id]);
                $message = "Equipo eliminado correctamente";
            } catch (PDOException $e) {
                $message = "Error al eliminar el equipo: " . $e->getMessage();
            }
            break;

        case 'add_player':
            $nombre_jugador = $_POST['nombre_jugador'];
            $posicion = $_POST['posicion'];
            $equipo_id = $_POST['equipo_id'];
            $numero_camisa = $_POST['numero_camisa'];
            try {
                $stmt = $pdo->prepare('INSERT INTO jugadores (nombre, posicion, equipo_id, numero_camisa) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nombre_jugador, $posicion, $equipo_id, $numero_camisa]);
                $message = "Jugador añadido correctamente";
            } catch (PDOException $e) {
                $message = "Error al añadir el jugador: " . $e->getMessage();
            }
            break;

        case 'delete_player':
            $jugador_id = $_POST['jugador_id'];
            try {
                $stmt = $pdo->prepare('DELETE FROM jugadores WHERE id = ?');
                $stmt->execute([$jugador_id]);
                $message = "Jugador eliminado correctamente";
            } catch (PDOException $e) {
                $message = "Error al eliminar el jugador: " . $e->getMessage();
            }
            break;

        case 'update_score':
            $equipo_id = $_POST['equipo_id'];
            $partidos_ganados = $_POST['partidos_ganados'];
            $partidos_empatados = $_POST['partidos_empatados'];
            $partidos_perdidos = $_POST['partidos_perdidos'];
            $goles_favor = $_POST['goles_favor'];
            $goles_contra = $_POST['goles_contra'];
            $puntos = $_POST['puntos'];
            try {
                $stmt = $pdo->prepare('INSERT INTO tabla_general (equipo_id, partidos_ganados, partidos_empatados, partidos_perdidos, goles_favor, goles_contra, puntos) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                                        partidos_ganados = VALUES(partidos_ganados),
                                        partidos_empatados = VALUES(partidos_empatados),
                                        partidos_perdidos = VALUES(partidos_perdidos),
                                        goles_favor = VALUES(goles_favor),
                                        goles_contra = VALUES(goles_contra),
                                        puntos = VALUES(puntos)');
                $stmt->execute([$equipo_id, $partidos_ganados, $partidos_empatados, $partidos_perdidos, $goles_favor, $goles_contra, $puntos]);
                $message = "Tabla general actualizada correctamente";
            } catch (PDOException $e) {
                $message = "Error al actualizar la tabla general: " . $e->getMessage();
            }
            break;

        case 'update_goals':
            $jugador_id = $_POST['jugador_id'];
            $goles = $_POST['goles'];
            try {
                $stmt = $pdo->prepare('UPDATE jugadores SET goles = ? WHERE id = ?');
                $stmt->execute([$goles, $jugador_id]);
                $message = "Tabla de goleo actualizada correctamente";
            } catch (PDOException $e) {
                $message = "Error al actualizar la tabla de goleo: " . $e->getMessage();
            }
            break;

        case 'add_match':
            $equipo_local = $_POST['equipo_local'];
            $equipo_visitante = $_POST['equipo_visitante'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $lugar = $_POST['lugar'];
            try {
                $stmt = $pdo->prepare('INSERT INTO partidos (equipo_local, equipo_visitante, fecha, hora, lugar) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$equipo_local, $equipo_visitante, $fecha, $hora, $lugar]);
                $message = "Partido añadido correctamente";
            } catch (PDOException $e) {
                $message = "Error al añadir el partido: " . $e->getMessage();
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Liga de Fut 6 SSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/kTcErj9gtgIJszacMs8n4Uu16R4wrSJsUpu3i+pAJFZCIQVJtwQV8Sma2XtDwvS6+LQa9XZdfzTog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .notification {
        display: none;
    }

    .notification.show {
        display: block;
    }

    .highlight:hover {
        transform: scale(1.02);
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Estilos para el modal de confirmación */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 2rem;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        text-align: center;
    }

    .modal.show {
        display: flex;
    }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">⚽ Panel de Administración - Liga de Fut 6 SSA</div>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300">Inicio</a></li>
                <li><a href="logout.php" class="hover:text-blue-300">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <!-- Notificación de Mensajes -->
    <?php if (isset($message)): ?>
    <div id="notification"
        class="notification bg-green-100 text-green-700 p-4 rounded-lg mx-auto my-6 w-3/4 text-center show">
        <?= htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs de Secciones -->
    <div class="container mx-auto mt-12">
        <div class="flex justify-center mb-8">
            <button class="tab-button mx-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                data-target="equipos-tab">Equipos</button>
            <button class="tab-button mx-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                data-target="jugadores-tab">Jugadores</button>
            <button class="tab-button mx-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                data-target="tabla-general-tab">Tabla General</button>
            <button class="tab-button mx-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                data-target="tabla-goleo-tab">Tabla de Goleo</button>
            <button class="tab-button mx-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                data-target="jornadas-tab">Jornadas</button>
        </div>

        <!-- Tab de Equipos -->
        <div id="equipos-tab" class="tab-content active bg-white p-8 rounded-lg shadow-md mb-8 highlight">
            <h3 class="text-2xl font-bold mb-4">Gestionar Equipos</h3>

            <!-- Añadir Equipo -->
            <form action="admin_panel.php" method="POST" class="mb-8">
                <input type="hidden" name="action" value="add_team">
                <div class="mb-4">
                    <label for="nombre_equipo" class="block text-gray-700 font-bold mb-2">Nombre del Equipo:</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" required
                        class="w-full p-2 border rounded-lg" />
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Añadir
                    Equipo</button>
            </form>

            <!-- Eliminar Equipo -->
            <form action="admin_panel.php" method="POST" id="deleteTeamForm">
                <input type="hidden" name="action" value="delete_team">
                <div class="mb-4">
                    <label for="equipo_id" class="block text-gray-700 font-bold mb-2">Selecciona el Equipo:</label>
                    <select id="equipo_id" name="equipo_id" required class="w-full p-2 border rounded-lg">
                        <?php
                        $equipos = $pdo->query('SELECT * FROM equipos');
                        if ($equipos->rowCount() > 0) {
                            while ($equipo = $equipos->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$equipo['id']}'>{$equipo['nombre']}</option>";
                            }
                        } else {
                            echo "<option>No hay equipos disponibles</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="button" onclick="confirmDelete('deleteTeamForm')"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar
                    Equipo</button>
            </form>
        </div>

        <!-- Modal de Confirmación -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <h3 class="text-xl font-bold mb-4">Confirmar Eliminación</h3>
                <p>¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.</p>
                <div class="flex justify-center mt-6">
                    <button id="cancelDelete"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 mr-4">Cancelar</button>
                    <button id="confirmDelete"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
                </div>
            </div>
        </div>
        <!-- Tab de Jugadores -->
        <div id="jugadores-tab" class="tab-content bg-white p-8 rounded-lg shadow-md mb-8 highlight">
            <h3 class="text-2xl font-bold mb-4">Gestionar Jugadores</h3>

            <!-- Añadir Jugador -->
            <form action="admin_panel.php" method="POST" class="mb-8">
                <input type="hidden" name="action" value="add_player">
                <div class="mb-4">
                    <label for="nombre_jugador" class="block text-gray-700 font-bold mb-2">Nombre del Jugador:</label>
                    <input type="text" id="nombre_jugador" name="nombre_jugador" required
                        class="w-full p-2 border rounded-lg" />
                </div>
                <div class="mb-4">
                    <label for="posicion" class="block text-gray-700 font-bold mb-2">Posición:</label>
                    <select id="posicion" name="posicion" required class="w-full p-2 border rounded-lg">
                        <option value="Portero">Portero</option>
                        <option value="Defensa">Defensa</option>
                        <option value="Mediocampista">Mediocampista</option>
                        <option value="Delantero">Delantero</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="equipo_id" class="block text-gray-700 font-bold mb-2">Equipo:</label>
                    <select id="equipo_id" name="equipo_id" required class="w-full p-2 border rounded-lg">
                        <?php
                $equipos = $pdo->query('SELECT * FROM equipos');
                if ($equipos->rowCount() > 0) {
                    while ($equipo = $equipos->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$equipo['id']}'>{$equipo['nombre']}</option>";
                    }
                } else {
                    echo "<option>No hay equipos disponibles</option>";
                }
                ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="numero_camisa" class="block text-gray-700 font-bold mb-2">Número de Camisa:</label>
                    <input type="number" id="numero_camisa" name="numero_camisa" required
                        class="w-full p-2 border rounded-lg" />
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Añadir Jugador
                </button>
            </form>

            <!-- Eliminar Jugador -->
            <form action="admin_panel.php" method="POST">
                <input type="hidden" name="action" value="delete_player">
                <div class="mb-4">
                    <label for="jugador_id" class="block text-gray-700 font-bold mb-2">Selecciona el Jugador:</label>
                    <select id="jugador_id" name="jugador_id" required class="w-full p-2 border rounded-lg">
                        <?php
                $jugadores = $pdo->query('SELECT jugadores.*, equipos.nombre AS equipo_nombre FROM jugadores LEFT JOIN equipos ON jugadores.equipo_id = equipos.id');
                if ($jugadores->rowCount() > 0) {
                    while ($jugador = $jugadores->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$jugador['id']}'>{$jugador['nombre']} - Equipo: {$jugador['equipo_nombre']}</option>";
                    }
                } else {
                    echo "<option>No hay jugadores disponibles</option>";
                }
                ?>
                    </select>
                </div>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Eliminar Jugador
                </button>
            </form>
        </div>
        <!-- Tab de Tabla General -->
        <div id="tabla-general-tab" class="tab-content bg-white p-8 rounded-lg shadow-md mb-8 highlight">
            <h3 class="text-2xl font-bold mb-4">Actualizar Tabla General</h3>

            <!-- Mostrar Tabla General -->
            <div class="mb-8">
                <h4 class="text-xl font-bold mb-4">Estadísticas Actuales</h4>
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="w-1/4 px-4 py-2 border">Equipo</th>
                            <th class="w-1/6 px-4 py-2 border">Partidos Ganados</th>
                            <th class="w-1/6 px-4 py-2 border">Partidos Empatados</th>
                            <th class="w-1/6 px-4 py-2 border">Partidos Perdidos</th>
                            <th class="w-1/6 px-4 py-2 border">Goles a Favor</th>
                            <th class="w-1/6 px-4 py-2 border">Goles en Contra</th>
                            <th class="w-1/6 px-4 py-2 border">Puntos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                $tabla_general = $pdo->query('SELECT tabla_general.*, equipos.nombre AS equipo_nombre FROM tabla_general LEFT JOIN equipos ON tabla_general.equipo_id = equipos.id');
                if ($tabla_general->rowCount() > 0) {
                    while ($registro = $tabla_general->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>{$registro['equipo_nombre']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['partidos_ganados']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['partidos_empatados']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['partidos_perdidos']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['goles_favor']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['goles_contra']}</td>";
                        echo "<td class='border px-4 py-2'>{$registro['puntos']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='border px-4 py-2 text-center'>No hay datos disponibles en la tabla general</td></tr>";
                }
                ?>
                    </tbody>
                </table>
            </div>

            <!-- Formulario para Actualizar Tabla General -->
            <form action="admin_panel.php" method="POST" class="mb-8">
                <input type="hidden" name="action" value="update_score">
                <div class="mb-4">
                    <label for="equipo_id" class="block text-gray-700 font-bold mb-2">Selecciona el Equipo:</label>
                    <select id="equipo_id" name="equipo_id" required class="w-full p-2 border rounded-lg">
                        <?php
                $equipos = $pdo->query('SELECT * FROM equipos');
                if ($equipos->rowCount() > 0) {
                    while ($equipo = $equipos->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$equipo['id']}'>{$equipo['nombre']}</option>";
                    }
                } else {
                    echo "<option>No hay equipos disponibles</option>";
                }
                ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="partidos_ganados" class="block text-gray-700 font-bold mb-2">Partidos
                            Ganados:</label>
                        <input type="number" id="partidos_ganados" name="partidos_ganados" required
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label for="partidos_empatados" class="block text-gray-700 font-bold mb-2">Partidos
                            Empatados:</label>
                        <input type="number" id="partidos_empatados" name="partidos_empatados" required
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label for="partidos_perdidos" class="block text-gray-700 font-bold mb-2">Partidos
                            Perdidos:</label>
                        <input type="number" id="partidos_perdidos" name="partidos_perdidos" required
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label for="goles_favor" class="block text-gray-700 font-bold mb-2">Goles a Favor:</label>
                        <input type="number" id="goles_favor" name="goles_favor" required
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label for="goles_contra" class="block text-gray-700 font-bold mb-2">Goles en Contra:</label>
                        <input type="number" id="goles_contra" name="goles_contra" required
                            class="w-full p-2 border rounded-lg" />
                    </div>
                    <div class="mb-4">
                        <label for="puntos" class="block text-gray-700 font-bold mb-2">Puntos:</label>
                        <input type="number" id="puntos" name="puntos" required class="w-full p-2 border rounded-lg" />
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Actualizar Tabla General
                </button>
            </form>
        </div>
        <!-- Tab de Tabla de Goleo -->
        <div id="tabla-goleo-tab" class="tab-content bg-white p-8 rounded-lg shadow-md mb-8 highlight">
            <h3 class="text-2xl font-bold mb-4">Tabla de Goleo</h3>

            <!-- Mostrar Tabla de Goleo -->
            <div class="mb-8">
                <h4 class="text-xl font-bold mb-4">Goleadores Actuales</h4>
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="w-1/4 px-4 py-2 border">Jugador</th>
                            <th class="w-1/4 px-4 py-2 border">Equipo</th>
                            <th class="w-1/4 px-4 py-2 border">Posición</th>
                            <th class="w-1/4 px-4 py-2 border">Goles</th>
                            <th class="w-1/4 px-4 py-2 border">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                $tabla_goleo = $pdo->query('SELECT jugadores.*, equipos.nombre AS equipo_nombre FROM jugadores LEFT JOIN equipos ON jugadores.equipo_id = equipos.id ORDER BY jugadores.goles DESC');
                if ($tabla_goleo->rowCount() > 0) {
                    while ($jugador = $tabla_goleo->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>{$jugador['nombre']}</td>";
                        echo "<td class='border px-4 py-2'>{$jugador['equipo_nombre']}</td>";
                        echo "<td class='border px-4 py-2'>{$jugador['posicion']}</td>";
                        echo "<td class='border px-4 py-2'>{$jugador['goles']}</td>";
                        echo "<td class='border px-4 py-2'>
                                <form action='admin_panel.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete_player'>
                                    <input type='hidden' name='jugador_id' value='{$jugador['id']}'>
                                    <button type='submit' class='bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700'>
                                        Eliminar
                                    </button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='border px-4 py-2 text-center'>No hay datos disponibles en la tabla de goleo</td></tr>";
                }
                ?>
                    </tbody>
                </table>
            </div>

            <!-- Formulario para Actualizar Goles -->
            <form action="admin_panel.php" method="POST" class="mb-8">
                <input type="hidden" name="action" value="update_goals">
                <div class="mb-4">
                    <label for="jugador_id" class="block text-gray-700 font-bold mb-2">Selecciona el Jugador:</label>
                    <select id="jugador_id" name="jugador_id" required class="w-full p-2 border rounded-lg">
                        <?php
                $jugadores = $pdo->query('SELECT jugadores.*, equipos.nombre AS equipo_nombre FROM jugadores LEFT JOIN equipos ON jugadores.equipo_id = equipos.id');
                if ($jugadores->rowCount() > 0) {
                    while ($jugador = $jugadores->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$jugador['id']}'>{$jugador['nombre']} - Equipo: {$jugador['equipo_nombre']}</option>";
                    }
                } else {
                    echo "<option>No hay jugadores disponibles</option>";
                }
                ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="goles" class="block text-gray-700 font-bold mb-2">Número de Goles:</label>
                    <input type="number" id="goles" name="goles" required class="w-full p-2 border rounded-lg" />
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Actualizar Goles
                </button>
            </form>
        </div>


        <!-- Formulario para Actualizar Goles -->
        <form action="admin_panel.php" method="POST" class="mb-8">
            <input type="hidden" name="action" value="update_goals">
            <div class="mb-4">
                <label for="jugador_id" class="block text-gray-700 font-bold mb-2">Selecciona el Jugador:</label>
                <select id="jugador_id" name="jugador_id" required class="w-full p-2 border rounded-lg">
                    <?php
                $jugadores = $pdo->query('SELECT jugadores.*, equipos.nombre AS equipo_nombre FROM jugadores LEFT JOIN equipos ON jugadores.equipo_id = equipos.id');
                if ($jugadores->rowCount() > 0) {
                    while ($jugador = $jugadores->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$jugador['id']}'>{$jugador['nombre']} - Equipo: {$jugador['equipo_nombre']}</option>";
                    }
                } else {
                    echo "<option>No hay jugadores disponibles</option>";
                }
                ?>
                </select>
            </div>
            <div class="mb-4">
                <label for="goles" class="block text-gray-700 font-bold mb-2">Número de Goles:</label>
                <input type="number" id="goles" name="goles" required class="w-full p-2 border rounded-lg" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Actualizar Goles
            </button>
        </form>
    </div>

    <!-- Otros Tabs... -->
    <!-- Tab de Jornadas -->
    <div id="jornadas-tab" class="tab-content bg-white p-8 rounded-lg shadow-md mb-8 highlight">
        <h3 class="text-2xl font-bold mb-4">Gestionar Jornadas</h3>

        <!-- Mostrar Jornadas Existentes -->
        <div class="mb-8">
            <h4 class="text-xl font-bold mb-4">Jornadas Actuales</h4>
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="w-1/6 px-4 py-2 border">Equipo Local</th>
                        <th class="w-1/6 px-4 py-2 border">Equipo Visitante</th>
                        <th class="w-1/6 px-4 py-2 border">Fecha</th>
                        <th class="w-1/6 px-4 py-2 border">Hora</th>
                        <th class="w-1/6 px-4 py-2 border">Lugar</th>
                        <th class="w-1/6 px-4 py-2 border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                $jornadas = $pdo->query('SELECT p.*, e1.nombre AS equipo_local, e2.nombre AS equipo_visitante 
                                         FROM partidos p 
                                         LEFT JOIN equipos e1 ON p.equipo_local = e1.id 
                                         LEFT JOIN equipos e2 ON p.equipo_visitante = e2.id 
                                         ORDER BY p.fecha, p.hora');
                if ($jornadas->rowCount() > 0) {
                    while ($jornada = $jornadas->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>{$jornada['equipo_local']}</td>";
                        echo "<td class='border px-4 py-2'>{$jornada['equipo_visitante']}</td>";
                        echo "<td class='border px-4 py-2'>{$jornada['fecha']}</td>";
                        echo "<td class='border px-4 py-2'>{$jornada['hora']}</td>";
                        echo "<td class='border px-4 py-2'>{$jornada['lugar']}</td>";
                        echo "<td class='border px-4 py-2'>
                                <form action='admin_panel.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='action' value='delete_jornada'>
                                    <input type='hidden' name='partido_id' value='{$jornada['id']}'>
                                    <button type='submit' class='bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700'>
                                        Eliminar
                                    </button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='border px-4 py-2 text-center'>No hay jornadas disponibles</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <!-- Formulario para Crear Jornada -->
        <form action="admin_panel.php" method="POST" class="mb-8">
            <input type="hidden" name="action" value="create_jornada">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="equipo_local" class="block text-gray-700 font-bold mb-2">Equipo Local:</label>
                    <select id="equipo_local" name="equipo_local" required class="w-full p-2 border rounded-lg">
                        <?php
                    $equipos = $pdo->query('SELECT * FROM equipos');
                    if ($equipos->rowCount() > 0) {
                        while ($equipo = $equipos->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$equipo['id']}'>{$equipo['nombre']}</option>";
                        }
                    } else {
                        echo "<option>No hay equipos disponibles</option>";
                    }
                    ?>
                    </select>
                </div>
                <div>
                    <label for="equipo_visitante" class="block text-gray-700 font-bold mb-2">Equipo Visitante:</label>
                    <select id="equipo_visitante" name="equipo_visitante" required class="w-full p-2 border rounded-lg">
                        <?php
                    $equipos = $pdo->query('SELECT * FROM equipos');
                    if ($equipos->rowCount() > 0) {
                        while ($equipo = $equipos->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='{$equipo['id']}'>{$equipo['nombre']}</option>";
                        }
                    } else {
                        echo "<option>No hay equipos disponibles</option>";
                    }
                    ?>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label for="fecha" class="block text-gray-700 font-bold mb-2">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required class="w-full p-2 border rounded-lg" />
            </div>
            <div class="mb-4">
                <label for="hora" class="block text-gray-700 font-bold mb-2">Hora:</label>
                <input type="time" id="hora" name="hora" required class="w-full p-2 border rounded-lg" />
            </div>
            <div class="mb-4">
                <label for="lugar" class="block text-gray-700 font-bold mb-2">Lugar:</label>
                <input type="text" id="lugar" name="lugar" required class="w-full p-2 border rounded-lg" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Crear Jornada
            </button>
        </form>
    </div>

    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>

    <script>
    // Lógica para Tabs
    const tabs = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-target');

            tabContents.forEach((content) => {
                content.classList.remove('active');
            });

            document.getElementById(target).classList.add('active');
        });
    });

    // Mostrar el modal de confirmación para eliminar
    function confirmDelete(formId) {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('show');

        document.getElementById('confirmDelete').onclick = function() {
            document.getElementById(formId).submit();
        };

        document.getElementById('cancelDelete').onclick = function() {
            modal.classList.remove('show');
        };
    }

    // Ocultar notificación después de unos segundos
    const notification = document.getElementById('notification');
    if (notification) {
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    }
    </script>
</body>

</html><?php
session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Manejar las diferentes acciones del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'add_team':
            $nombre_equipo = $_POST['nombre_equipo'];
            $imagen = null;

            // Manejar la subida de la imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagen_tmp = $_FILES['imagen']['tmp_name'];
                $imagen_nombre = basename($_FILES['imagen']['name']);
                $imagen_destino = 'images/' . $imagen_nombre;

                // Mover el archivo a la carpeta de destino
                if (move_uploaded_file($imagen_tmp, $imagen_destino)) {
                    $imagen = $imagen_nombre;
                } else {
                    $message = "Error al subir la imagen.";
                }
            }

            // Insertar el equipo en la base de datos
            try {
                $stmt = $pdo->prepare('INSERT INTO equipos (nombre, imagen) VALUES (?, ?)');
                $stmt->execute([$nombre_equipo, $imagen]);
                $message = "Equipo añadido correctamente";
            } catch (PDOException $e) {
                $message = "Error al añadir el equipo: " . $e->getMessage();
            }
            break;

        case 'delete_team':
            $equipo_id = $_POST['equipo_id'];
            try {
                $stmt = $pdo->prepare('DELETE FROM equipos WHERE id = ?');
                $stmt->execute([$equipo_id]);
                $message = "Equipo eliminado correctamente";
            } catch (PDOException $e) {
                $message = "Error al eliminar el equipo: " . $e->getMessage();
            }
            break;

        case 'add_player':
            $nombre_jugador = $_POST['nombre_jugador'];
            $posicion = $_POST['posicion'];
            $equipo_id = $_POST['equipo_id'];
            $numero_camisa = $_POST['numero_camisa'];
            try {
                $stmt = $pdo->prepare('INSERT INTO jugadores (nombre, posicion, equipo_id, numero_camisa) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nombre_jugador, $posicion, $equipo_id, $numero_camisa]);
                $message = "Jugador añadido correctamente";
            } catch (PDOException $e) {
                $message = "Error al añadir el jugador: " . $e->getMessage();
            }
            break;

        case 'delete_player':
            $jugador_id = $_POST['jugador_id'];
            try {
                $stmt = $pdo->prepare('DELETE FROM jugadores WHERE id = ?');
                $stmt->execute([$jugador_id]);
                $message = "Jugador eliminado correctamente";
            } catch (PDOException $e) {
                $message = "Error al eliminar el jugador: " . $e->getMessage();
            }
            break;

        case 'update_score':
            $equipo_id = $_POST['equipo_id'];
            $partidos_ganados = $_POST['partidos_ganados'];
            $partidos_empatados = $_POST['partidos_empatados'];
            $partidos_perdidos = $_POST['partidos_perdidos'];
            $goles_favor = $_POST['goles_favor'];
            $goles_contra = $_POST['goles_contra'];
            $puntos = $_POST['puntos'];
            try {
                $stmt = $pdo->prepare('INSERT INTO tabla_general (equipo_id, partidos_ganados, partidos_empatados, partidos_perdidos, goles_favor, goles_contra, puntos) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE 
                                       partidos_ganados = VALUES(partidos_ganados),
                                       partidos_empatados = VALUES(partidos_empatados),
                                       partidos_perdidos = VALUES(partidos_perdidos),
                                       goles_favor = VALUES(goles_favor),
                                       goles_contra = VALUES(goles_contra),
                                       puntos = VALUES(puntos)');
                $stmt->execute([$equipo_id, $partidos_ganados, $partidos_empatados, $partidos_perdidos, $goles_favor, $goles_contra, $puntos]);
                $message = "Tabla general actualizada correctamente";
            } catch (PDOException $e) {
                $message = "Error al actualizar la tabla general: " . $e->getMessage();
            }
            break;

        case 'update_goals':
            $jugador_id = $_POST['jugador_id'];
            $goles = $_POST['goles'];
            try {
                $stmt = $pdo->prepare('UPDATE jugadores SET goles = ? WHERE id = ?');
                $stmt->execute([$goles, $jugador_id]);
                $message = "Tabla de goleo actualizada correctamente";
            } catch (PDOException $e) {
                $message = "Error al actualizar la tabla de goleo: " . $e->getMessage();
            }
            break;

        case 'create_jornada':
            $equipo_local = $_POST['equipo_local'];
            $equipo_visitante = $_POST['equipo_visitante'];
            $fecha = $_POST['fecha'];
            $hora = $_POST['hora'];
            $lugar = $_POST['lugar'];
            try {
                $stmt = $pdo->prepare('INSERT INTO partidos (equipo_local, equipo_visitante, fecha, hora, lugar) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$equipo_local, $equipo_visitante, $fecha, $hora, $lugar]);
                $message = "Jornada creada correctamente";
            } catch (PDOException $e) {
                $message = "Error al crear la jornada: " . $e->getMessage();
            }
            break;

        case 'delete_jornada':
            $partido_id = $_POST['partido_id'];
            try {
                $stmt = $pdo->prepare('DELETE FROM partidos WHERE id = ?');
                $stmt->execute([$partido_id]);
                $message = "Jornada eliminada correctamente";
            } catch (PDOException $e) {
                $message = "Error al eliminar la jornada: " . $e->getMessage();
            }
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Liga de Fut 6 SSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/kTcErj9gtgIJszacMs8n4Uu16R4wrSJsUpu3i+pAJFZCIQVJtwQV8Sma2XtDwvS6+LQa9XZdfzTog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background-color: white;
        padding: 2rem;
        border-radius: 10px;
        width: 90%;
        max-width: 500px;
    }

    .show-modal {
        display: flex !important;
    }
    </style>
</head>

<body class="bg-gray-100 text-gray-900">
    <!-- Contenido del Panel de Administración -->
    <div class="container mx-auto mt-12">
        <!-- Aquí se pueden agregar formularios y funcionalidades para gestionar equipos, jugadores, etc. -->

        <div class="mb-4">
            <label for="imagen" class="block text-gray-700 font-bold mb-2">Logo del Equipo:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full p-2 border rounded-lg">
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Añadir
            Equipo</button>
        </form>

        <!-- Modales para Confirmación -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <h3 class="text-lg font-bold mb-4">¿Estás seguro que deseas eliminar?</h3>
                <p>Esta acción no se puede deshacer.</p>
                <div class="flex justify-end mt-6">
                    <button id="cancelDelete" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-700 mr-2">
                        Cancelar</button>
                    <button id="confirmDelete" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para Modales -->
    <script>
    // Lógica para el Modal de Confirmación de Eliminación
    const deleteButtons = document.querySelectorAll('.delete-button');
    const deleteModal = document.getElementById('deleteModal');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', () => {
            deleteModal.classList.add('show-modal');
        });
    });

    cancelDelete.addEventListener('click', () => {
        deleteModal.classList.remove('show-modal');
    });

    confirmDelete.addEventListener('click', () => {
        // Acción de eliminación confirmada aquí
        deleteModal.classList.remove('show-modal');
    });
    </script>

</body>

</html>