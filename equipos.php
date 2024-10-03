<?php
session_start();

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Obtener los equipos
$equipos = $pdo->query('SELECT * FROM equipos')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos - Liga de Fut 6 SSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/kTcErj9gtgIJszacMs8n4Uu16R4wrSJsUpu3i+pAJFZCIQVJtwQV8Sma2XtDwvS6+LQa9XZdfzTog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    .team-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .player-icon {
        margin-right: 8px;
    }

    .player-list-item {
        transition: background-color 0.2s, color 0.2s;
    }

    .player-list-item:hover {
        background-color: #3b82f6;
        color: #ffffff;
    }
    </style>
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

    <!-- Contenido de Equipos -->
    <div class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-8 text-center text-blue-600">Equipos Registrados</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($equipos as $equipo): ?>
            <div class="team-card bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0">
                        <?php if (!empty($equipo['imagen'])): ?>
                        <img src="images/<?php echo htmlspecialchars($equipo['imagen']); ?>"
                            alt="Imagen de <?php echo htmlspecialchars($equipo['nombre']); ?>"
                            class="h-24 w-24 rounded-full mb-4">
                        <?php else: ?>
                        <i class="fas fa-shield-alt text-blue-600 text-4xl"></i>
                        <?php endif; ?>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($equipo['nombre']); ?>
                        </h3>
                    </div>
                </div>

                <!-- Mostrar los jugadores del equipo -->
                <h4 class="text-xl font-semibold mb-2 text-gray-700">Jugadores:</h4>
                <ul class="list-none">
                    <?php
                    $jugadores = $pdo->prepare('SELECT * FROM jugadores WHERE equipo_id = ?');
                    $jugadores->execute([$equipo['id']]);
                    $jugadores = $jugadores->fetchAll(PDO::FETCH_ASSOC);

                    if (count($jugadores) > 0) {
                        foreach ($jugadores as $jugador) {
                            echo '<li class="player-list-item px-3 py-2 rounded flex items-center mb-1">';
                            echo '<i class="fas fa-futbol player-icon text-blue-600"></i>';
                            echo htmlspecialchars($jugador['nombre']) . ' - <span class="ml-auto text-sm text-gray-600">#' . htmlspecialchars($jugador['numero_camisa']) . '</span>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li class="player-list-item px-3 py-2 rounded">No hay jugadores registrados en este equipo.</li>';
                    }
                    ?>
                </ul>

                <div class="mt-6 text-center">
                    <a href="equipo_detalles.php?id=<?php echo $equipo['id']; ?>"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                        Ver Detalles del Equipo
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>

</body>

</html>