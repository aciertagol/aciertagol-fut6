<?php
// Conectar a la base de datos
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jornadas - Liga de Fut 6 SSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">

    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">⚽ Liga de Fut 6 SSA - Jornadas</div>
            <ul class="flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300">Inicio</a></li>
                <li><a href="equipos.php" class="hover:text-blue-300">Equipos</a></li>
                <li><a href="estadisticas.php" class="hover:text-blue-300">Estadísticas</a></li>
                <li><a href="informacion.php" class="hover:text-blue-300">Información</a></li>
            </ul>
        </div>
    </nav>

    <!-- Sección de Jornadas -->
    <div class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-4 text-center">Jornadas de la Liga</h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <table class="min-w-full bg-white border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border">Equipo Local</th>
                        <th class="px-4 py-2 border">Equipo Visitante</th>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Hora</th>
                        <th class="px-4 py-2 border">Lugar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Consultar las jornadas de la base de datos
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
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='border px-4 py-2 text-center'>No hay jornadas disponibles</td></tr>";
                    }
                    ?>
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