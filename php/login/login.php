<?php
// Incluye el archivo de conexión a la base de datos
include '../connection.php';
// Incluye el archivo con las funciones de consulta
include '../functions.php';

// Inicia la sesión
session_start();
$error = null; // Inicializa la variable para almacenar errores

// Verifica si se ha enviado un formulario con el botón 'loginButton' o 'classButton'
if (!empty($_POST['loginButton']) || !empty($_POST['classButton'])) {
    
    // Determina qué botón fue presionado y asigna el valor a $clickedValue
    if (!empty($_POST['classButton'])) {
        $clickedValue = $_POST['classButton'];
    } else {
        $clickedValue = $_POST['loginButton'];
    }

    // Verifica si ambos campos (usuario y contraseña) están completos
    if (!empty($_POST['pass']) && !empty($_POST['user'])) {

        // Realiza la búsqueda del usuario según el tipo (Teacher o Student)
        if ($clickedValue === 'Teacher') {
            // Busca al profesor por su correo electrónico
            $user = searchTeacher($connection, NULL, NULL, NULL, $_POST['user']);
        } else if ($clickedValue === 'Student') {
            // Busca al estudiante por su correo electrónico
            $user = searchStudent($connection, NULL, NULL, NULL, $_POST['user']);
        }

        // Verifica si el correo y la contraseña ingresados coinciden con los datos del usuario encontrado
        if ($user && $user[3] === $_POST['user'] && $user[4] === $_POST['pass']) {
            // Almacena los datos del usuario en la sesión
            $_SESSION['user'] = $user;

            // Redirige según el tipo de usuario
            if ($clickedValue === 'Teacher') {
                header('Location: ../teacher/index.php');
            } else if ($clickedValue === 'Student') {
                header('Location: ../student/index.php');
            }
        }

        // Si las credenciales no coinciden, asigna un mensaje de error
        $error = 'Incorrect user';

    } else if (!empty($_POST['user'])) {
        // Si el campo contraseña está vacío
        $error = 'The password field is required';
    } else if (!empty($_POST['pass'])) {
        // Si el campo usuario está vacío
        $error = 'The user field is required';
    } else if (!empty($_POST['loginButton'])) {
        // Si ambos campos están vacíos
        $error = 'All fields are required';
    }

    // Muestra el formulario de inicio de sesión junto con los mensajes de error (si los hay)
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="../../css/login/login.css">
    </head>
    <body>
        <div class="container_login">
            <!-- Formulario de inicio de sesión -->
            <form method="post">
                <div class="logo">
                    <img src="../../images/logo/logoWhite.png" alt="logo">
                </div>
                <h1>Welcome to EduConnect!</h1>
                <p>Please login to your account</p>
                <input type="text" name="user" placeholder="Gmail">
                <input type="password" name="pass" placeholder="Password"> 
                <!-- Botón para enviar el formulario, mantiene el valor del tipo de usuario seleccionado -->
                <button type="submit" name="loginButton" value="<?php echo htmlspecialchars($clickedValue); ?>">Login</button>
            </form>
            <?php
            // Muestra el mensaje de error si existe
            if ($error) {
                echo "<p style='color:red;'>$error</p>";
            }
            ?>
        </div>
    </body>
    </html>
    <?php
} else {
    // Muestra la página inicial para seleccionar el tipo de usuario (Teacher o Student)
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PreLogin</title>
        <link rel="stylesheet" href="../../css/login/preLogin.css">
    </head>
    <body>
    <div class="container_prelogin">
        <div class="form-container">
            <img src="../../images/logo/logoBlue.png" alt="logo" class="logo">
            <div class="buttons-container">
                <!-- Formulario para estudiantes -->
                <form method="post">
                    <button type="submit" name="classButton" value="Student">
                        <div class="card">
                            <div class="img-student"></div>
                            <span>Student</span>
                        </div>
                    </button>
                </form>
                <!-- Formulario para profesores -->
                <form method="post">
                    <button type="submit" name="classButton" value="Teacher">
                        <div class="card">
                            <div class="img-teacher"></div>
                            <span>Teacher</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
}
?>