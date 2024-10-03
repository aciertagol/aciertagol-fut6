<?php
session_start(); // Inicia la sesión
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liga de Fut 6 SSA - Inicio Mejorado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/kTcErj9gtgIJszacMs8n4Uu16R4wrSJsUpu3i+pAJFZCIQVJtwQV8Sma2XtDwvS6+LQa9XZdfzTog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body {
        background-color: #f3f4f6;
        color: #121212;
    }

    .navbar {
        background-color: #003366;
    }

    .navbar a {
        color: #ffcc00;
        font-weight: bold;
    }

    .carousel-item {
        min-width: 100%;
        transition: transform 0.5s ease-in-out;
    }

    .carousel-buttons {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
    }

    .carousel-button {
        background-color: rgba(0, 0, 0, 0.5);
        border: none;
        color: #ffcc00;
        padding: 1rem;
        cursor: pointer;
    }

    .indicator {
        width: 12px;
        height: 12px;
        margin: 0 5px;
        background-color: rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        cursor: pointer;
    }

    .indicator.active {
        background-color: #ffcc00;
    }

    .btn-primary {
        background-color: #ffcc00;
        color: #003366;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #ffa500;
    }

    .sponsor-carousel {
        display: flex;
        overflow: hidden;
        animation: slide 20s infinite linear;
    }

    .sponsor-item {
        min-width: 25%;
        text-align: center;
        padding: 1rem;
    }

    @keyframes slide {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar text-white p-4 shadow-md sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <!-- Logotipo -->
            <div class="flex items-center">
                <img src="images/tu_logotipo.png" alt="Logotipo Liga de Fut 6 SSA" class="h-12 mr-4">
                <div class="text-white text-lg font-bold">⚽ Liga de Fut 6 SSA</div>
            </div>
            <ul class="hidden md:flex space-x-4">
                <li><a href="index.php" class="hover:text-blue-300 transition duration-300">Inicio</a></li>
                <li><a href="equipos.php" class="hover:text-blue-300 transition duration-300">Equipos</a></li>
                <li><a href="jornadas.php" class="hover:text-blue-300 transition duration-300">Jornadas</a></li>
                <li><a href="estadisticas.php" class="hover:text-blue-300 transition duration-300">Estadísticas</a></li>
                <li><a href="informacion.php" class="hover:text-blue-300 transition duration-300">Información</a></li>
                <li><a href="inscripcion_torneo.php" class="hover:text-blue-300 transition duration-300">Inscripción</a>
                </li>

                <!-- Mostrar el Panel de Administración si el usuario es administrador -->
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <li><a href="admin_panel.php"
                        class="hover:text-blue-300 transition duration-300 text-red-500 font-bold">
                        Panel de Administración</a></li>
                <?php endif; ?>

                <!-- Mostrar el enlace de iniciar o cerrar sesión -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php" class="hover:text-blue-300 transition duration-300">Cerrar Sesión</a></li>
                <?php else: ?>
                <li><a href="login.php" class="hover:text-blue-300 transition duration-300">Iniciar Sesión</a></li>
                <?php endif; ?>
            </ul>
            <button id="menuToggle" class="md:hidden text-2xl focus:outline-none">&#9776;</button>
        </div>
    </nav>

    <!-- Menú móvil desplegable -->
    <div id="mobileMenu" class="bg-blue-600 text-white md:hidden p-4 hidden">
        <a href="index.php" class="block py-2 hover:text-blue-300">Inicio</a>
        <a href="equipos.php" class="block py-2 hover:text-blue-300">Equipos</a>
        <a href="jornadas.php" class="block py-2 hover:text-blue-300">Jornadas</a>
        <a href="estadisticas.php" class="block py-2 hover:text-blue-300">Estadísticas</a>
        <a href="informacion.php" class="block py-2 hover:text-blue-300">Información</a>
        <a href="inscripcion_torneo.php" class="block py-2 hover:text-blue-300">Inscripción</a>

        <!-- Mostrar el Panel de Administración si el usuario es administrador en el menú móvil -->
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
        <a href="admin_panel.php" class="block py-2 hover:text-red-400 font-bold">Panel de Administración</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="block py-2 hover:text-blue-300">Cerrar Sesión</a>
        <?php else: ?>
        <a href="login.php" class="block py-2 hover:text-blue-300">Iniciar Sesión</a>
        <?php endif; ?>
    </div>

    <!-- Sección de Hero con Carrusel -->
    <section id="inicio" class="relative h-screen bg-cover bg-center"
        style="background-image: url('images/hero_futbol.jpg');">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative container mx-auto h-full flex flex-col justify-center text-white text-center">
            <h2 class="text-4xl font-bold mb-4">¡Bienvenidos a la Liga de Fut 6 SSA!</h2>
            <p class="text-lg mb-8">La liga donde el fútbol 6 hace historia.</p>
            <a href="informacion.php"
                class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-400 transition duration-300">Descubre más</a>
        </div>
    </section>

    <!-- Sección de Videos Destacados -->
    <section class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-4 text-center">Videos Destacados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-4 shadow-md hover:shadow-lg transition-shadow duration-300">
                <iframe class="w-full h-56" src="https://www.youtube.com/embed/VIDEO_ID1" frameborder="0"
                    allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <h3 class="mt-4 text-xl font-bold">Resumen del Partido A vs B</h3>
            </div>
            <div class="bg-white p-4 shadow-md hover:shadow-lg transition-shadow duration-300">
                <iframe class="w-full h-56" src="https://www.youtube.com/embed/VIDEO_ID2" frameborder="0"
                    allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <h3 class="mt-4 text-xl font-bold">Goles más Destacados</h3>
            </div>
            <div class="bg-white p-4 shadow-md hover:shadow-lg transition-shadow duration-300">
                <iframe class="w-full h-56" src="https://www.youtube.com/embed/VIDEO_ID3" frameborder="0"
                    allow="autoplay; encrypted-media" allowfullscreen></iframe>
                <h3 class="mt-4 text-xl font-bold">Entrevista al Goleador del Torneo</h3>
            </div>
        </div>
    </section>

    <!-- Sección de Patrocinadores con carrusel -->
    <section class="container mx-auto mt-12">
        <h2 class="text-3xl font-bold mb-4 text-center">Nuestros Patrocinadores</h2>
        <div class="overflow-hidden sponsor-carousel">
            <div class="sponsor-item">
                <img src="images/patrocinador1.png" alt="Patrocinador 1" class="h-24 mx-auto">
            </div>
            <div class="sponsor-item">
                <img src="images/patrocinador2.png" alt="Patrocinador 2" class="h-24 mx-auto">
            </div>
            <div class="sponsor-item">
                <img src="images/patrocinador3.png" alt="Patrocinador 3" class="h-24 mx-auto">
            </div>
            <div class="sponsor-item">
                <img src="images/patrocinador4.png" alt="Patrocinador 4" class="h-24 mx-auto">
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-12">
        <p>&copy; 2024 Liga de Fut 6 SSA. Todos los derechos reservados.</p>
    </footer>

    <script>
    // Lógica del menú móvil
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
    </script>
</body>

</html>