<?php
    // =====================================================
    // FUNCIONES PARA GESTIÓN DE PROFESORES
    // =====================================================
    
    // Función para obtener todos los registros de la tabla "teachers"
    // Devuelve un recurso de MySQL con todos los profesores
    function teachers($connection){
        // Consulta para seleccionar todos los registros de "teachers"
        $sql = 'SELECT * FROM teachers';

        // Ejecuta la consulta y devuelve el resultado como un recurso de MySQL
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un profesor en la tabla "teachers" según ciertos criterios
    // Parámetros: conexión a la BD, id, nombre, apellidos, correo
    // Devuelve un array con los datos del profesor si lo encuentra
    function searchTeacher($connection, $id, $name, $surnames, $gmail){
        // Consulta para seleccionar todos los registros de "teachers"
        $sql = 'SELECT * FROM teachers';
        $query = mysqli_query($connection, $sql);

        // Itera por cada fila obtenida del resultado
        while($row = mysqli_fetch_assoc($query)){
            // Comprueba si alguno de los criterios coincide con el registro actual
            if(($id && $id === $row['id']) || 
               ($name && $name === $row['name']) || 
               ($surnames && $surnames === $row['surnames']) || 
               ($gmail && $gmail === $row['gmail'])){
                // Si hay coincidencia, retorna un array con los datos relevantes del profesor
                return [$row['id'], $row['name'], $row['surnames'], $row['gmail'], $row['password'], $row['profile_picture'], $row['course_id']];
            }
        }
        // Si no encuentra coincidencias, no retorna nada
    }

    // Función para obtener un estudiante específico por su ID
    // Devuelve un array con los datos básicos del estudiante
    function getStudent($connection,$id){
        // Consulta SQL para obtener el estudiante con el ID especificado
        $sql = 'SELECT * FROM students WHERE id ='.$id;

        $query = mysqli_query($connection,$sql);

        // Recorre los resultados buscando la coincidencia exacta
        while($row = mysqli_fetch_assoc($query)){
            if($row['id'] === $id){
                return[$row['id'],$row['name'],$row['surnames'],$row['gmail']];
            }
        }

        // Si no encuentra al estudiante, devuelve null
        return null;
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE ESTUDIANTES
    // =====================================================

    // Función para obtener todos los registros de la tabla "students"
    // Devuelve un recurso de MySQL con todos los estudiantes
    function students($connection){
        // Consulta para seleccionar todos los registros de "students"
        $sql = 'SELECT * FROM students';

        // Ejecuta la consulta y devuelve el resultado como un recurso de MySQL
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un estudiante en la tabla "students" según ciertos criterios
    // Parámetros: conexión a la BD, id, nombre, apellidos, correo
    // Devuelve un array con los datos del estudiante si lo encuentra
    function searchStudent($connection, $id, $name, $surnames, $gmail){
        // Consulta para seleccionar todos los registros de "students"
        $sql = 'SELECT * FROM students';
        $query = mysqli_query($connection, $sql);

        // Itera por cada fila obtenida del resultado
        while($row = mysqli_fetch_assoc($query)){
            // Comprueba si alguno de los criterios coincide con el registro actual
            if(($id && $id === $row['id']) || 
               ($name && $name === $row['name']) || 
               ($surnames && $surnames === $row['surnames']) || 
               ($gmail && $gmail === $row['gmail'])){
                // Si hay coincidencia, retorna un array con los datos relevantes del estudiante
                return [$row['id'], $row['name'], $row['surnames'], $row['gmail'], $row['password'], $row['profile_picture'], $row['course_id'], $row['DNI']];
            }
        }
        
        // Si no encuentra coincidencias, retorna null
        return null;
    }

    // Función para obtener los estudiantes de un curso específico
    // Parámetro: ID del curso
    // Devuelve un recurso de MySQL con los estudiantes del curso o null si no hay ninguno
    function getStudents($connection, $courseId){
        // Consulta para seleccionar estudiantes según el ID del curso
        $sql = 'SELECT * FROM students WHERE course_id = '.$courseId;

        $query = mysqli_query($connection, $sql);

        // Verifica si la consulta devolvió resultados
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    // Función para añadir un nuevo estudiante a la tabla "students"
    // Parámetros: conexión, nombre, apellidos, DNI y ID del curso
    // Genera automáticamente el email y contraseña
    function addStudent($connection, $name, $surnames, $dni, $courseId){
        // Genera un email basado en el nombre y apellidos
        $gmail = mb_strtolower($name, "UTF-8") . mb_strtolower($surnames, "UTF-8") . '@gmail.com';

        // Divide el nombre y apellidos en caracteres individuales
        $nameDelimited = implode("|", str_split($name));
        $arrName = explode("|", $nameDelimited);
        $surnameDelimited = implode("|", str_split($surnames));
        $arrSurname = explode("|", $surnameDelimited);

        // Busca el curso correspondiente al ID proporcionado
        $course = searchCourses($connection, $courseId);

        // Genera una contraseña utilizando la primera letra del nombre, apellido y título del curso
        $password = $arrName[0] . $arrSurname[0] . $course;

        // Consulta para insertar un nuevo estudiante
        $sql = "INSERT INTO students(name, surnames, dni, gmail, password, course_id) VALUES('$name', '$surnames', '$dni', '$gmail', '$password', '$courseId')";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para modificar los datos de un estudiante existente
    // Parámetros: conexión, ID, nuevo nombre, nuevos apellidos, nuevo email, nuevo DNI
    function modifyStudent($connection, $id, $name, $surnames, $gmail, $dni){
        // Consulta para actualizar los datos del estudiante
        $sql = "UPDATE students SET name = '$name', surnames = '$surnames', gmail = '$gmail', DNI = '$dni' WHERE id = $id";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para eliminar un estudiante de la tabla "students"
    // Parámetro: ID del estudiante a eliminar
    function deleteStudent($connection, $id){
        // Consulta para eliminar un estudiante según su ID
        $sql = 'DELETE FROM students WHERE id = '.$id;

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // =====================================================
    // FUNCIONES GENERALES DE BÚSQUEDA
    // =====================================================

    // Función para buscar un registro en cualquier tabla utilizando un ID
    // Parámetros: conexión, nombre de la tabla, ID a buscar
    // Devuelve un recurso de MySQL con el resultado
    function searchById($connection,$table, $id){
        // Construye la consulta para buscar por ID en la tabla especificada
        $sql = "SELECT * FROM $table WHERE id = $id";

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    // Función para buscar el título de un curso basado en su ID
    // Devuelve el título del curso o null si no lo encuentra
    function searchCourses($connection, $id){
        // Consulta para obtener el título del curso según su ID
        $sql = 'SELECT title FROM courses WHERE id = '.$id;
        $query = mysqli_query($connection, $sql);

        // Verifica si hay resultados y devuelve el título del curso
        if($query && mysqli_num_rows($query) > 0){
            $row = mysqli_fetch_assoc($query);
            return $row['title'];
        }

        // Si no hay resultados, retorna null
        return null;
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE PROYECTOS
    // =====================================================

    // Función para obtener los proyectos de un curso específico
    // Parámetro: ID del curso
    // Devuelve un recurso de MySQL con los proyectos o null si no hay ninguno
    function getProjects($connection, $courseId){
        // Consulta para seleccionar proyectos según el ID del curso
        $sql = 'SELECT * FROM projects WHERE course_id = '.$courseId;

        // Ejecuta la consulta y devuelve el resultado
        $query = mysqli_query($connection, $sql);

        // Verifica si la consulta devolvió resultados
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    // Función para obtener un proyecto específico por su ID
    // Devuelve un array con los datos básicos del proyecto
    function getProject($connection,$id){
        // Consulta SQL para obtener el proyecto con el ID especificado
        $sql = 'SELECT * FROM projects WHERE id ='.$id;

        $query = mysqli_query($connection,$sql);

        // Recorre los resultados buscando la coincidencia exacta
        while($row = mysqli_fetch_assoc($query)){
            if($row['id'] === $id){
                return[$row['id'],$row['title'],$row['description'],$row['finalized']];
            }
        }

        // Si no encuentra el proyecto, devuelve null
        return null;
    }

    // Función para añadir un nuevo proyecto a la tabla "projects"
    // Parámetros: conexión, título, descripción, ID del curso
    function addProject($connection, $title, $description, $courseId){
        // Consulta para insertar un nuevo proyecto
        // El campo finalized se inicializa a 0 (no finalizado)
        $sql = "INSERT INTO projects(title, description, finalized, course_id) VALUES('$title', '$description', 0, '$courseId')";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para editar un proyecto existente
    // Parámetros: conexión, ID, nuevo título, nueva descripción, estado de finalización
    function editProject($connection,$id,$title,$description,$finalized){
        // Consulta SQL para actualizar los datos del proyecto
        $sql = "UPDATE projects SET title = '$title', description = '$description', finalized = $finalized WHERE id = $id";

        // Ejecuta la consulta
        mysqli_query($connection,$sql);

        // Redirige a la página de edición del proyecto
        header('Location: editProject.php?id='.$id);
    }

    // Función para eliminar un proyecto de la tabla "projects"
    // Parámetro: ID del proyecto a eliminar
    function deleteProject($connection, $id){
        // Consulta para eliminar un proyecto según su ID
        $sql = 'DELETE FROM projects WHERE id = '.$id;

        // Ejecuta la consulta
        mysqli_query($connection, $sql);
    
        // Redirige a la página principal
        header('Location: index.php');
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE ACTIVIDADES
    // =====================================================

    // Función para obtener las actividades de un proyecto específico
    // Parámetro: ID del proyecto
    // Devuelve un recurso de MySQL con las actividades o null si no hay ninguna
    function getActivities($connection,$projectId){
        // Consulta SQL para obtener actividades del proyecto especificado
        $sql = 'SELECT * FROM activities WHERE project_id = '.$projectId;
        $query = mysqli_query($connection,$sql);
        
        // Si la consulta no devuelve filas, retorna null
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    // Función para obtener una actividad específica por su ID
    // Devuelve un array con los datos básicos de la actividad
    function getActivity($connection,$activityId){
        // Consulta SQL para obtener la actividad con el ID especificado
        $sql = 'SELECT * FROM activities WHERE id = '.$activityId;
        $query = mysqli_query($connection,$sql);

        // Recorre los resultados y devuelve los datos de la actividad
        while($row = mysqli_fetch_assoc($query)){
                return[$row['id'],$row['title'],$row['description'],$row['finalized']];
        }
    }

    // Función para modificar una actividad existente
    // Parámetros: conexión, ID de actividad, nuevo título, nueva descripción, estado de finalización
    function modifyActivity($connection,$activityId,$title,$description,$finalized){
        // Consulta SQL para actualizar los datos de la actividad
        $sql = "UPDATE activities SET title = '$title', description = '$description', finalized = $finalized WHERE id = $activityId";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página de edición del proyecto
        header('Location: editProject.php?id='.$_GET['id']);
    }

    // Función para añadir una nueva actividad a un proyecto
    // Parámetros: conexión, ID del proyecto, título, descripción, array de ítems
    function addActivity($connection,$projectId,$title,$description,$arrItems){
        // Consulta para insertar una nueva actividad
        // El campo finalized se inicializa a 0 (no finalizada)
        $sql = "INSERT INTO activities(title,description,finalized,project_id) VALUES('$title','$description',0,$projectId)";

        // Ejecuta la consulta
        mysqli_query($connection,$sql);

        // Obtiene el ID de la actividad recién creada
        $sql = "SELECT id FROM activities WHERE description = '$description' AND title = '$title'";
        $query = mysqli_query($connection, $sql);

        // Verifica si hay resultados y obtiene el ID
        if($query && mysqli_num_rows($query) > 0){
            $row = mysqli_fetch_assoc($query);
            $idActivity = $row['id'];
        }

        // Añade cada ítem a la actividad
        foreach($arrItems as $value){
            addItem($connection,$idActivity,$value[0],$value[1],$value[2]);
        }
    }

    // Función para eliminar una actividad
    // Parámetro: ID de la actividad a eliminar
    function deleteActivity($connection,$activityId){
        // Consulta SQL para eliminar la actividad
        $sql = 'DELETE FROM activities WHERE id = '.$activityId;

        // Ejecuta la consulta
        mysqli_query($connection,$sql);
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE ÍTEMS
    // =====================================================

    // Función para obtener los ítems de una actividad específica
    // Parámetro: ID de la actividad
    // Devuelve un recurso de MySQL con los ítems o null si no hay ninguno
    function getItems($connection,$activityId){
        // Consulta SQL para obtener ítems de la actividad especificada
        $sql = 'SELECT * FROM items WHERE activity_id = '.$activityId;
        $query = mysqli_query($connection,$sql);
        
        // Si la consulta no devuelve filas, retorna null
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }    
    }

    // Función para modificar los ítems de una actividad
    // Parámetros: conexión, ID de actividad, array de nuevos ítems
    // Elimina los ítems existentes y añade los nuevos
    function modifyItems($connection,$activityId,$arrItems){
        // Elimina todos los ítems existentes de la actividad
        $sql = 'DELETE FROM items WHERE activity_id = '.$activityId;
        mysqli_query($connection,$sql);

        // Añade cada nuevo ítem a la actividad
        foreach($arrItems as $value){
            addItem($connection,$activityId,$value[0],$value[1],$value[2]);
        }
    }

    // Función para añadir un nuevo ítem a una actividad
    // Parámetros: conexión, ID de actividad, título, valor (peso), icono (archivo)
    function addItem($connection,$activityId,$title,$value,$icon){
        // Obtiene la extensión del archivo de icono
        $fileExtension = pathinfo($icon['name'], PATHINFO_EXTENSION);
        // Crea un nombre único para el archivo
        $nameFile = $activityId.$title.'.'.$fileExtension;

        // Mueve el archivo subido a la carpeta de iconos
        move_uploaded_file($icon['tmp_name'],'../../images/items_icons/'.$nameFile);

        // Consulta para insertar un nuevo ítem
        $sql = "INSERT INTO items(activity_id,title,value,icon) VALUES($activityId,'$title',$value,'$nameFile')";

        // Ejecuta la consulta
        mysqli_query($connection,$sql);
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE PERFILES
    // =====================================================

    // Función para cambiar la foto de perfil de un usuario
    // Parámetros: conexión, ID, email, archivo de foto, tabla, ruta para guardar
    function changeProfilePhoto($connection,$id,$gmail,$profilePhoto,$table,$rutaSave){
        // Obtiene la extensión del archivo de foto
        $fileExtension = pathinfo($profilePhoto['name'], PATHINFO_EXTENSION);
        // Crea un nombre único para el archivo basado en el email
        $nameFile = $gmail.'.'.$fileExtension;

        // Mueve el archivo subido a la carpeta especificada
        move_uploaded_file($profilePhoto['tmp_name'],$rutaSave.$nameFile);

        // Actualiza la foto de perfil en la base de datos
        $sql = "UPDATE $table SET profile_picture = '$nameFile' WHERE id = $id";
        // Actualiza la sesión con la nueva foto
        $_SESSION['user'][5] = $nameFile;
    
        // Ejecuta la consulta
        mysqli_query($connection,$sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // =====================================================
    // FUNCIONES PARA GESTIÓN DE PUNTUACIONES
    // =====================================================

    // Función para obtener las puntuaciones de un ítem específico
    // Parámetro: ID del ítem
    // Devuelve un recurso de MySQL con las puntuaciones o null si no hay ninguna
    function getScores($connection,$item_Id){
        // Consulta SQL para obtener puntuaciones del ítem especificado
        $sql = 'SELECT * FROM score WHERE item_id = '.$item_Id;

        $query = mysqli_query($connection,$sql);

        // Verifica si la consulta devolvió resultados
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        } 
    }

    // Función para obtener la puntuación de un estudiante en un ítem específico
    // Parámetros: conexión, ID del estudiante, ID del ítem
    // Devuelve la puntuación o null si no existe
    function getScore($connection,$student_id,$item_id){
        // Consulta SQL para obtener la puntuación específica
        $sql = 'SELECT * FROM score WHERE student_id = '.$student_id.' AND item_id = '.$item_id;

        $query = mysqli_query($connection,$sql);

        // Verifica si hay resultados y devuelve la puntuación
        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            return $row['score'];
        } else {
            return null;
        }
    }

    // Función para añadir una nueva puntuación
    // Parámetros: conexión, puntuación, ID del estudiante, ID del ítem
    function addScore($connection,$score,$student_id,$item_id){
        // Consulta para insertar una nueva puntuación
        $sql = "INSERT INTO score(score, student_id, item_id) VALUES($score, $student_id, $item_id)";

        // Ejecuta la consulta
        mysqli_query($connection,$sql);

        // Redirige a la página de edición del proyecto
        header('Location: editProject.php?id='.$_GET['id']);
    }

    // Función para modificar una puntuación existente
    // Parámetros: conexión, nueva puntuación, ID del estudiante, ID del ítem
    function modifyScore($connection,$score,$student_id,$item_id){
        // Consulta SQL para actualizar la puntuación
        $sql = "UPDATE score SET score = $score WHERE student_id = $student_id AND item_id = $item_id";

        // Ejecuta la consulta
        mysqli_query($connection,$sql);

        // Redirige a la página de edición del proyecto
        header('Location: editProject.php?id='.$_GET['id']);
    }

    // Función para eliminar una puntuación
    // Parámetro: ID de la puntuación a eliminar
    function deleteScore($connection,$score_id){
        // Consulta SQL para eliminar la puntuación
        $sql = 'DELETE FROM score WHERE id = '.$score_id;

        // Ejecuta la consulta
        mysqli_query($connection, $sql);
    
        // Redirige a la página de edición del proyecto
        header('Location: editProject.php?id='.$_GET['id']);
    }

    // =====================================================
    // FUNCIONES DE IMPORTACIÓN Y CÁLCULO
    // =====================================================

    // Función para importar estudiantes desde un archivo CSV
    // Parámetro: ruta del archivo CSV
    function importStudents($connection,$fileRoute){
         
        // Abrir y leer el archivo CSV
        $file = fopen($fileRoute, "r");
        
        // Leer y procesar cada línea del CSV
        while (($data = fgetcsv($file)) !== FALSE) {
            // Extrae los datos de cada columna
            $id = $data[0];
            $name = $data[1];
            $surnames = $data[2];
            $password = $data[3];
            $course_id = $data[5];
            $gmail = $data[6];
            $DNI = $data[7];
    
            // Insertar datos en la base de datos
            $sql = "INSERT INTO students (name, surnames, password, course_id, gmail, DNI) VALUES ('$name', '$surnames', '$password', $course_id, '$gmail', '$DNI')";
            mysqli_query($connection,$sql);
        }
    
        // Cerrar archivo
        fclose($file);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para calcular la puntuación de un estudiante en una actividad
    // Parámetros: conexión, ID del estudiante, ID de la actividad
    // Devuelve la puntuación ponderada o null si faltan datos
    function getScoreActivity($connection,$student_id,$activity_id){
        // Obtiene los ítems de la actividad
        $items = getItems($connection,$activity_id);
        $score = 0;
        
        // Si hay ítems, calcula la puntuación ponderada
        if($items && mysqli_num_rows($items) > 0){
            while($row = mysqli_fetch_assoc($items)){
                // Obtiene la puntuación del estudiante en este ítem
                $itemScore = getScore($connection,$student_id,$row['id']);
                // Calcula la puntuación ponderada según el valor del ítem
                $score += intval($itemScore) * (intval($row['value']) / 100); 
                // Si falta alguna puntuación, devuelve null
                if(!$itemScore){
                    return null;
                }
            }
        }else{
            return null;
        }

        return $score;
    }
    
    // Función para calcular la puntuación media de un estudiante en un proyecto
    // Parámetros: conexión, ID del estudiante, ID del proyecto
    // Devuelve la puntuación media o null si faltan datos
    function getScoreProyect($connection,$student_id,$project_id){
        // Obtiene las actividades del proyecto
        $activities = getActivities($connection,$project_id);
        $count = 0;
        $score = 0;

        // Si hay actividades, calcula la puntuación media
        if($activities && mysqli_num_rows($activities) > 0){
            while($row = mysqli_fetch_assoc($activities)){
                // Suma la puntuación de cada actividad
                $score += getScoreActivity($connection,$student_id,$row['id']);
                $count++;

                // Si falta alguna puntuación, devuelve null
                if(!getScoreActivity($connection,$student_id,$row['id'])){
                    return null;
                }
            }
        }else{
            return null;
        }

        // Calcula y devuelve la media
        return $score / $count;
    }
?>