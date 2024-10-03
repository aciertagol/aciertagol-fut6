<?php
session_start(); // Inicia la sesión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aquí deberías manejar la inscripción del equipo
    $nombre_equipo = $_POST['nombre_equipo'];
    $nombre_entrenador = $_POST['nombre_entrenador'];
    $contacto = $_POST['contacto'];

    // Guardar la información (esto debería ir a la base de datos)
    // Por ahora, mostramos un mensaje de confirmación.
    echo "<script>alert('¡Inscripción completada para el equipo $nombre_equipo!');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liga de Fut 6 SSA - Inscripción</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-900">
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-white text-lg font-bold">⚽ Liga de Fut 6 SSA</div>
            <ul class="hidden md:flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300">Inicio</a></li>
                <li><a href="equipos.php" class="hover:text-blue-300">Equipos</a></li>
                <li><a href="jornadas.php" class="hover:text-blue-300">Jornadas</a></li>
                <li><a href="estadisticas.php" class="hover:text-blue-300">Estadísticas</a></li>
                <li><a href="informacion.php" class="hover:text-blue-300">Información</a></li>
                <li><a href="inscripcion_torneo.php" class="hover:text-blue-300">Inscripción</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="hover:text-blue-300">Cerrar Sesión</a></li>
                <?php else: ?>
                <li><a href="login.php" class="hover:text-blue-300">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Sección de Inscripción -->
    <section class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-8 text-center">Inscripción al Torneo</h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <form action="inscripcion_torneo.php" method="POST">
                <div class="mb-4">
                    <label for="nombre_equipo" class="block text-gray-700 font-bold mb-2">Nombre del Equipo:</label>
                    <input type="text" id="nombre_equipo" name="nombre_equipo" required
                        class="w-full p-2 border rounded-lg" />
                </div>

                <div class="mb-4">
                    <label for="nombre_entrenador" class="block text-gray-700 font-bold mb-2">Nombre del
                        Entrenador:</label>
                    <input type="text" id="nombre_entrenador" name="nombre_entrenador" required
                        class="w-full p-2 border rounded-lg" />
                </div>

                <div class="mb-4">
                    <label for="contacto" class="block text-gray-700 font-bold mb-2">Teléfono/Correo de
                        Contacto:</label>
                    <input type="text" id="contacto" name="contacto" required class="w-full p-2 border rounded-lg" />
                </div>

                <button type="submit" class="btn-primary w-full mt-4">Enviar Inscripción</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>
</body>

</html>