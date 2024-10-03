<?php
session_start();

// Verificar que se haya recibido el ID del equipo
if (!isset($_GET['id'])) {
    die('Error: Equipo no especificado.');
}

// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Obtener el equipo y sus jugadores
$equipo_id = $_GET['id'];
$stmt = $pdo->prepare('SELECT * FROM equipos WHERE id = ?');
$stmt->execute([$equipo_id]);
$equipo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$equipo) {
    die('Error: Equipo no encontrado.');
}

// Obtener los jugadores del equipo
$jugadores = $pdo->prepare('SELECT * FROM jugadores WHERE equipo_id = ?');
$jugadores->execute([$equipo_id]);
$jugadores = $jugadores->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Equipo - <?php echo htmlspecialchars($equipo['nombre']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">⚽ Liga de Fut 6 SSA - Detalles del Equipo</div>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300">Inicio</a></li>
                <li><a href="equipos.php" class="hover:text-blue-300">Equipos</a></li>
                <li><a href="jornadas.php" class="hover:text-blue-300">Jornadas</a></li>
                <li><a href="estadisticas.php" class="hover:text-blue-300">Estadísticas</a></li>
                <li><a href="informacion.php" class="hover:text-blue-300">Información</a></li>
            </ul>
        </div>
    </nav>

    <!-- Detalles del Equipo -->
    <div class="container mx-auto mt-12">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex items-center mb-6">
                <div class="flex-shrink-0">
                    <!-- Imagen del equipo (opcional) -->
                    <?php if (!empty($equipo['imagen'])): ?>
                    <img src="images/<?php echo htmlspecialchars($equipo['imagen']); ?>"
                        alt="Logo de <?php echo htmlspecialchars($equipo['nombre']); ?>" class="h-24 w-24 rounded-full">
                    <?php else: ?>
                    <i class="fas fa-shield-alt text-blue-600 text-6xl"></i>
                    <?php endif; ?>
                </div>
                <div class="ml-4">
                    <h2 class="text-4xl font-bold text-blue-600"><?php echo htmlspecialchars($equipo['nombre']); ?></h2>
                </div>
            </div>

            <!-- Mostrar los jugadores del equipo -->
            <h3 class="text-2xl font-semibold mb-4">Jugadores del Equipo:</h3>
            <ul class="list-disc list-inside mb-4">
                <?php
                if (count($jugadores) > 0) {
                    foreach ($jugadores as $jugador) {
                        echo '<li>' . htmlspecialchars($jugador['nombre']) . ' - Número: ' . htmlspecialchars($jugador['numero_camisa']) . '</li>';
                    }
                } else {
                    echo '<li>No hay jugadores registrados en este equipo.</li>';
                }
                ?>
            </ul>

            <!-- Información Adicional -->
            <h3 class="text-2xl font-semibold mb-4">Información Adicional:</h3>
            <p>Por ejemplo, podrías incluir aquí estadísticas del equipo, historia, etc.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>

</body>

</html>