<?php
session_start();

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Obtener los datos de la tabla general y la tabla de goleo
$tabla_general = $pdo->query('SELECT equipos.nombre, tabla_general.* FROM tabla_general JOIN equipos ON tabla_general.equipo_id = equipos.id ORDER BY puntos DESC')->fetchAll(PDO::FETCH_ASSOC);
$tabla_goleo = $pdo->query('SELECT jugadores.nombre, equipos.nombre as equipo_nombre, jugadores.goles FROM jugadores JOIN equipos ON jugadores.equipo_id = equipos.id ORDER BY goles DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Liga de Fut 6 SSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">⚽ Liga de Fut 6 SSA</div>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300">Inicio</a></li>
                <li><a href="equipos.php" class="hover:text-blue-300">Equipos</a></li>
                <li><a href="jornadas.php" class="hover:text-blue-300">Jornadas</a></li>
                <li><a href="estadisticas.php" class="hover:text-blue-300">Estadísticas</a></li>
                <li><a href="informacion.php" class="hover:text-blue-300">Información</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="hover:text-blue-300">Cerrar Sesión</a></li>
                <?php else: ?>
                <li><a href="login.php" class="hover:text-blue-300">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Contenido de Estadísticas -->
    <div class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-8 text-center">Estadísticas</h2>

        <!-- Tabla General -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-2xl font-bold mb-4">Tabla General</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2">Equipo</th>
                        <th class="border-b p-2">Ganados</th>
                        <th class="border-b p-2">Empatados</th>
                        <th class="border-b p-2">Perdidos</th>
                        <th class="border-b p-2">Goles a Favor</th>
                        <th class="border-b p-2">Goles en Contra</th>
                        <th class="border-b p-2">Puntos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabla_general as $fila): ?>
                    <tr>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['partidos_ganados']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['partidos_empatados']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['partidos_perdidos']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['goles_favor']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['goles_contra']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['puntos']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabla de Goleo -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h3 class="text-2xl font-bold mb-4">Tabla de Goleo</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2">Jugador</th>
                        <th class="border-b p-2">Equipo</th>
                        <th class="border-b p-2">Goles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tabla_goleo as $fila): ?>
                    <tr>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['equipo_nombre']); ?></td>
                        <td class="border-b p-2"><?php echo htmlspecialchars($fila['goles']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>

</body>

</html>