<?php
    // Función para obtener todos los registros de la tabla "teachers"
    function teachers($connection){
        // Consulta para seleccionar todos los registros de "teachers"
        $sql = 'SELECT * FROM teachers';

        // Ejecuta la consulta y devuelve el resultado como un recurso de MySQL
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un profesor en la tabla "teachers" según ciertos criterios
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

    // Función para obtener todos los registros de la tabla "students"
    function students($connection){
        // Consulta para seleccionar todos los registros de "students"
        $sql = 'SELECT * FROM students';

        // Ejecuta la consulta y devuelve el resultado como un recurso de MySQL
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un estudiante en la tabla "students" según ciertos criterios
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
    function getStudents($connection, $courseId){
        // Consulta para seleccionar estudiantes según el ID del curso
        $sql = 'SELECT * FROM students WHERE course_id = '.$courseId;

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    // Función para añadir un nuevo estudiante a la tabla "students"
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
    function modifyStudent($connection, $id, $name, $surnames, $gmail, $dni){
        // Consulta para actualizar los datos del estudiante
        $sql = "UPDATE students SET name = '$name', surnames = '$surnames', gmail = '$gmail', DNI = '$dni' WHERE id = $id";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para eliminar un estudiante de la tabla "students"
    function deleteStudent($connection, $id){
        // Consulta para eliminar un estudiante según su ID
        $sql = 'DELETE FROM students WHERE id = '.$id;

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    // Función para buscar un registro en cualquier tabla utilizando un ID
    function searchById($table, $id){
        // Construye la consulta para buscar por ID en la tabla especificada
        $sql = "SELECT * FROM $table WHERE id = $id";

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    // Función para buscar el título de un curso basado en su ID
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

    // Función para obtener los proyectos de un curso específico
    function getProjects($connection, $courseId){
        // Consulta para seleccionar proyectos según el ID del curso
        $sql = 'SELECT * FROM projects WHERE course_id = '.$courseId;

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    function getProject($connection,$id){

        $sql = 'SELECT * FROM projects WHERE id ='.$id;

        $query = mysqli_query($connection,$sql);

        while($row = mysqli_fetch_assoc($query)){
            if($row['id'] === $id){
                return[$row['id'],$row['title'],$row['description'],$row['finalized']];
            }
        }

        return null;
    }

    // Función para añadir un nuevo proyecto a la tabla "projects"
    function addProject($connection, $title, $description, $courseId){
        // Consulta para insertar un nuevo proyecto
        $sql = "INSERT INTO projects(title, description, finalized, course_id) VALUES('$title', '$description', 0, '$courseId')";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: index.php');
    }

    function editProject($connection,$id,$title,$description,$finalized){

        $sql = "UPDATE projects SET title = '$title', description = '$description', finalized = $finalized WHERE id = $id";

        mysqli_query($connection,$sql);

        header('Location: editProject.php?id='.$id);

    }

    // Función para eliminar un proyecto de la tabla "projects"
    function deleteProject($connection, $id){
        // Consulta para eliminar un proyecto según su ID
        $sql = 'DELETE FROM projects WHERE id = '.$id;

        // Ejecuta la consulta
        mysqli_query($connection, $sql);
    
        // Redirige a la página principal
        header('Location: index.php');
    }

    function getActivities($connection,$projectId){
        $sql = 'SELECT * FROM activities WHERE project_id = '.$projectId;
        $query = mysqli_query($connection,$sql);
        
        // Si la consulta no devuelve filas, retorna null
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
    }

    function addActivity($connection,$projectId,$title,$description,$arrItems){
        $sql = "INSERT INTO activities(title,description,finalized,project_id) VALUES('$title','$description',0,$projectId)";

        mysqli_query($connection,$sql);

        $sql = "SELECT id FROM activities WHERE description = '$description' AND title = '$title'";
        $query = mysqli_query($connection, $sql);

        // Verifica si hay resultados y devuelve el título del curso
        if($query && mysqli_num_rows($query) > 0){
            $row = mysqli_fetch_assoc($query);
            $idActivity = $row['id'];
        }

        foreach($arrItems as $value){
            addItem($connection,$idActivity,$value[0],$value[1],$value[2]);
        }
    }

    function getItems($connection,$activityId){
        $sql = 'SELECT * FROM items WHERE activity_id = '.$activityId;
        $query = mysqli_query($connection,$sql);
        
        // Si la consulta no devuelve filas, retorna null
        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }    
    }

    function addItem($connection,$activityId,$title,$value,$icon){
        $fileExtension = pathinfo($icon['name'], PATHINFO_EXTENSION);
        $nameFile = $activityId.$title.'.'.$fileExtension;

        move_uploaded_file($icon['tmp_name'],'../../images/items_icons/'.$nameFile);

        $sql = "INSERT INTO items(activity_id,title,value,icon) VALUES($activityId,'$title',$value,'$nameFile')";

        mysqli_query($connection,$sql);
    }

    function changeProfilePhoto($connection,$id,$gmail,$profilePhoto,$table,$rutaSave){

        $fileExtension = pathinfo($profilePhoto['name'], PATHINFO_EXTENSION);
        $nameFile = $gmail.'.'.$fileExtension;

        move_uploaded_file($profilePhoto['tmp_name'],$rutaSave.$nameFile);

        $sql = "UPDATE $table SET profile_picture = '$nameFile' WHERE id = $id";
        $_SESSION['user'][5] = $nameFile;
    
        mysqli_query($connection,$sql);

        header('Location: index.php');
    }
?>