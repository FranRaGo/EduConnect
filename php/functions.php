<?php
    // Función para obtener todos los registros de la tabla "teachers"
    function teachers($connection){
        // Consulta para seleccionar todos los registros de "teachers"
        $sql = 'SELECT * FROM teachers';

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un profesor en la tabla "teachers" según ciertos criterios
    function searchTeacher($connection, $id, $name, $surnames, $gmail){
        // Consulta para seleccionar todos los registros de "teachers"
        $sql = 'SELECT * FROM teachers';
        $query = mysqli_query($connection, $sql);

        // Itera por cada fila obtenida de la consulta
        while($row = mysqli_fetch_assoc($query)){
            // Comprueba si alguno de los criterios coincide con el registro actual
            if(($id && $id === $row['id']) || 
               ($name && $name === $row['name']) || 
               ($surnames && $surnames === $row['surnames']) || 
               ($gmail && $gmail === $row['gmail'])){
                // Si hay coincidencia, retorna un array con los datos relevantes del profesor
                return [$row['id'], $row['name'], $row['surnames'], $row['gmail'], $row['password'],$row['profile_picture'],$row['course_id']];
            }
        }
        // Si no encuentra coincidencias, no retorna nada
    }

    // Función para obtener todos los registros de la tabla "students"
    function students($connection){
        // Consulta para seleccionar todos los registros de "students"
        $sql = 'SELECT * FROM students';

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    // Función para buscar un estudiante en la tabla "students" según ciertos criterios
    function searchStudent($connection, $id, $name, $surnames, $gmail){
        // Consulta para seleccionar todos los registros de "students"
        $sql = 'SELECT * FROM students';
        $query = mysqli_query($connection, $sql);

        // Itera por cada fila obtenida de la consulta
        while($row = mysqli_fetch_assoc($query)){
            // Comprueba si alguno de los criterios coincide con el registro actual
            if(($id && $id === $row['id']) || 
               ($name && $name === $row['name']) || 
               ($surnames && $surnames === $row['surnames']) || 
               ($gmail && $gmail === $row['gmail'])){
                // Si hay coincidencia, retorna un array con los datos relevantes del estudiante
                return [$row['id'], $row['name'], $row['surnames'], $row['gmail'], $row['password'],$row['profile_picture'],$row['course_id']];
            }
        }

        // Si no encuentra coincidencias, retorna null
        return null;
    }

    // Función para buscar un registro en cualquier tabla utilizando un ID
    function searchById($table, $id){
        // Construye la consulta para buscar por ID en la tabla especificada
        $sql = "SELECT * FROM $table WHERE id = $id";

        // Ejecuta la consulta y devuelve el resultado
        return mysqli_query($connection, $sql);
    }

    function searchCourses($connection,$id){
        $sql = 'SELECT title FROM courses WHERE id = '.$id;
        $query = mysqli_query($connection,$sql);

        if($query && mysqli_num_rows($query) > 0){
            $row = mysqli_fetch_assoc($query);
            return $row['title'];
        }

        return null;
    }
?>