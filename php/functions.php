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

    function getStudent($connection,$id){

        $sql = 'SELECT * FROM students WHERE id ='.$id;

        $query = mysqli_query($connection,$sql);

        while($row = mysqli_fetch_assoc($query)){
            if($row['id'] === $id){
                return[$row['id'],$row['name'],$row['surnames'],$row['gmail']];
            }
        }

        return null;
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

        $query = mysqli_query($connection, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
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
        $query = mysqli_query($connection, $sql);

        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        }
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

    function getActivity($connection,$activityId){
        $sql = 'SELECT * FROM activities WHERE id = '.$activityId;
        $query = mysqli_query($connection,$sql);

        while($row = mysqli_fetch_assoc($query)){
                return[$row['id'],$row['title'],$row['description'],$row['finalized']];
        }
    }

    function modifyActivity($connection,$activityId,$title,$description,$finalized){
        $sql = "UPDATE activities SET title = '$title', description = '$description', finalized = $finalized WHERE id = $activityId";

        // Ejecuta la consulta
        mysqli_query($connection, $sql);

        // Redirige a la página principal
        header('Location: editProject.php?id='.$_GET['id']);

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

    function deleteActivity($connection,$activityId){
        $sql = 'DELETE FROM activities WHERE id = '.$activityId;

        mysqli_query($connection,$sql);
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

    function modifyItems($connection,$activityId,$arrItems){

        $sql = 'DELETE FROM items WHERE activity_id = '.$activityId;

        mysqli_query($connection,$sql);

        foreach($arrItems as $value){
            addItem($connection,$activityId,$value[0],$value[1],$value[2]);
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

    function getScores($connection,$item_Id){
        $sql = 'SELECT * FROM score WHERE item_id = '.$item_Id;

        $query = mysqli_query($connection,$sql);

        if ($query && mysqli_num_rows($query) > 0) {
            return $query;
        } else {
            return null;
        } 
        
    }

    function getScore($connection,$student_id,$item_id){
        $sql = 'SELECT * FROM score WHERE student_id = '.$student_id.' AND item_id = '.$item_id;

        $query = mysqli_query($connection,$sql);

        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            return $row['score'];
        } else {
            return null;
        }
    }

    function addScore($connection,$score,$student_id,$item_id){
        $sql = "INSERT INTO score(score, student_id, item_id) VALUES($score, $student_id, $item_id)";

        mysqli_query($connection,$sql);

        header('Location: editProject.php?id='.$_GET['id']);
    }

    function modifyScore($connection,$score,$student_id,$item_id){
        $sql = "UPDATE score SET score = $score WHERE student_id = $student_id AND item_id = $item_id";

        mysqli_query($connection,$sql);

        header('Location: editProject.php?id='.$_GET['id']);
    }

    function deleteScore($connection,$score_id){
        $sql = 'DELETE FROM score WHERE id = '.$score_id;

        mysqli_query($connection, $sql);
    
        header('Location: editProject.php?id='.$_GET['id']);
    }

    function importStudents($connection,$fileRoute){
         
        // Abrir y leer el archivo CSV
        $file = fopen($fileRoute, "r");
        
        // Leer y procesar cada línea del CSV
        while (($data = fgetcsv($file)) !== FALSE) {
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

        header('Location: index.php');
    }

    function getScoreActivity($connection,$student_id,$activity_id){
        $items = getItems($connection,$activity_id);
        $score = 0;
        
        if($items && mysqli_num_rows($items) > 0){
            while($row = mysqli_fetch_assoc($items)){
                $itemScore = getScore($connection,$student_id,$row['id']);
                $score += intval($itemScore) * (intval($row['value']) / 100); 
                if(!$itemScore){
                    return null;
                }
            }
        }else{
            return null;
        }

        return $score;
    }
    
    function getScoreProyect($connection,$student_id,$project_id){
        $activities = getActivities($connection,$project_id);
        $count = 0;
        $score = 0;

        if($activities && mysqli_num_rows($activities) > 0){
            while($row = mysqli_fetch_assoc($activities)){
                $score += getScoreActivity($connection,$student_id,$row['id']);
                $count++;

                if(!getScoreActivity($connection,$student_id,$row['id'])){
                    return null;
                }
            }
        }else{
            return null;
        }

        return $score / $count;
    }
?>