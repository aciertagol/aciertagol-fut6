<?php
session_start(); // Inicia la sesión
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liga de Fut 6 SSA - Información</title>
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

    <!-- Sección de Información -->
    <section class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-8 text-center">Información sobre la Liga de Fut 6 SSA</h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h3 class="text-2xl font-bold mb-4">¿Qué es la Liga de Fut 6 SSA?</h3>
            <p class="text-gray-700 mb-4">La Liga de Fut 6 SSA es una competencia emocionante donde equipos de fútbol 6
                compiten para demostrar quién es el mejor en la cancha. Con múltiples jornadas y torneos organizados,
                cada equipo tiene la oportunidad de brillar y los jugadores pueden destacarse.</p>

            <h3 class="text-2xl font-bold mb-4">Reglamento</h3>
            <p class="text-gray-700 mb-4">Todos los equipos deben seguir las reglas establecidas para asegurar un juego
                justo. Las reglas básicas incluyen el respeto hacia los compañeros de equipo, árbitros y contrincantes.
                Cualquier incumplimiento resultará en sanciones, desde amonestaciones hasta descalificaciones.</p>

            <h3 class="text-2xl font-bold mb-4">Contacto</h3>
            <p class="text-gray-700 mb-4">Para cualquier pregunta, sugerencia o comentario, no dudes en ponerte en
                contacto con nosotros. Puedes enviar un correo a <a href="mailto:contacto@liga6ssa.com"
                    class="text-blue-600 hover:text-blue-800">contacto@liga6ssa.com</a>.</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>
</body>

</html>