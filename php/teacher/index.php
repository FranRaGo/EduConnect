<?php
    include '../connection.php';
    include '../functions.php';

    session_start();

    if(!$_SESSION['user'] || !searchTeacher($connection,NULL,NULL,NULL,$_SESSION['user'][3])){
        header('Location: ../login/login.php');
    }

    $profilePicture = '../../images/default.png';

    if(!empty($_SESSION['user'][5])){
        $profilePicture = '../../images/'.$_SESSION['user'][5];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeacherHome</title>
</head>
<body>
    <div>
        <div>
            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="profilePicture" style='width:40px;'>
        </div>
        <div>
            <h1><?php echo htmlspecialchars($_SESSION['user'][1].' '.$_SESSION['user'][2]); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['user'][3]); ?></p>
        </div>
        <p><?php echo htmlspecialchars(searchCourses($connection,$_SESSION['user'][6])) ?></p>
    </div>
</body>
</html>