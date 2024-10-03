<?php
session_start();

// Verificar si el usuario ya está logueado y redirigirlo si es así
if (isset($_SESSION['user_id'])) {
    // Si el usuario ya inició sesión, redirigir según el rol
    if ($_SESSION['rol'] === 'admin') {
        header('Location: admin_panel.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

// Conexión a la base de datos con manejo de errores
try {
    $pdo = new PDO('mysql:host=localhost;dbname=fut6_liga', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}

// Manejar el envío del formulario
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Buscar al usuario en la base de datos
    try {
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña
        if ($user && password_verify($password, $user['password'])) {
            // Guardar la sesión del usuario
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rol'] = $user['rol'];
            $_SESSION['username'] = $user['username'];

            // Redirigir según el rol del usuario
            if ($user['rol'] == 'admin') {
                header('Location: admin_panel.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'Usuario o contraseña incorrectos';
        }
    } catch (PDOException $e) {
        $error = "Error en la consulta a la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fut6 Liga - Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/kTcErj9gtgIJszacMs8n4Uu16R4wrSJsUpu3i+pAJFZCIQVJtwQV8Sma2XtDwvS6+LQa9XZdfzTog=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body {
        background: linear-gradient(135deg, #00f0e5, #00e1f0, #00d3f0);
        background-size: 400% 400%;
        animation: gradientBG 12s ease infinite;
    }

    @keyframes gradientBG {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .btn-efecto:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
    }

    .btn-efecto:active {
        transform: scale(0.98);
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
    }

    .input-container {
        position: relative;
    }

    .input-container i {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #10b981;
    }

    .input-field {
        padding-left: 2.5rem;
        /* Espacio para el icono */
    }
    </style>
</head>

<body class="bg-gradient-to-r from-green-600 via-green-400 to-green-200">

    <!-- Contenedor Principal Centrado -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white shadow-2xl rounded-lg overflow-hidden w-full max-w-md p-8">

            <!-- Agregar el logotipo de la marca -->
            <div class="flex justify-center mb-6">
                <img src="images/tu_logotipo.png" alt="Tu Marca" class="h-16">
            </div>

            <h2 class="text-4xl font-bold text-green-600 text-center mb-8">Iniciar Sesión</h2>

            <!-- Mostrar mensaje de error -->
            <?php if ($error): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Formulario de Inicio de Sesión -->
            <form action="login.php" method="POST">
                <div class="mb-6 input-container">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" id="username"
                        class="input-field w-full p-3 border border-green-300 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200 ease-in-out"
                        placeholder="Nombre de usuario" required>
                </div>
                <div class="mb-6 input-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password"
                        class="input-field w-full p-3 border border-green-300 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-green-500 transition duration-200 ease-in-out"
                        placeholder="Contraseña" required>
                </div>

                <!-- Mostrar Contraseña -->
                <div class="mb-4 flex items-center">
                    <input type="checkbox" id="showPassword" onclick="togglePassword()" class="mr-2">
                    <label for="showPassword" class="text-gray-700">Mostrar Contraseña</label>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white py-3 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 ease-in-out btn-efecto">Iniciar
                    Sesión</button>
            </form>

            <!-- Enlace para ir a la página principal (index.php) -->
            <div class="mt-6 text-center">
                <p class="text-gray-600">¿No tienes una cuenta? <a href="register.php"
                        class="text-green-600 hover:underline">Regístrate aquí</a>.</p>
            </div>

            <div class="mt-6 text-center">
                <p class="text-gray-600">¿Volver a la página principal? <a href="index.php"
                        class="text-green-600 hover:underline">Ir al Inicio</a>.</p>
            </div>

        </div>
    </div>

    <script>
    // Función para mostrar/ocultar la contraseña
    function togglePassword() {
        var passwordField = document.getElementById('password');
        if (passwordField.type === 'password') {
            passwordField.type = 'text'; // Mostrar contraseña
        } else {
            passwordField.type = 'password'; // Ocultar contraseña
        }
    }
    </script>

</body>

</html>