<?php
    include '../connection.php';
    include '../functions.php';

    session_start();

    if(!$_SESSION['user'] || !searchTeacher($connection,NULL,NULL,NULL,$_SESSION['user'][3])){
        header('Location: ../login/login.php');
    }

    $profilePicture = '../../images/profile_images/default.png';

    if(!empty($_SESSION['user'][5])){
        $profilePicture = '../../images/profile_images/'.$_SESSION['user'][5];
    }

    $projects = getProjects($connection,$_SESSION['user'][6]);
    $students = getStudents($connection,$_SESSION['user'][6]);
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
            <img src="../../images/icons/edit_icon.svg" alt="edit" style='width:20px;'>
        </div>
        <div>
            <h1><?php echo htmlspecialchars($_SESSION['user'][1].' '.$_SESSION['user'][2]); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['user'][3]); ?></p>
        </div>
        <p><?php echo htmlspecialchars(searchCourses($connection,$_SESSION['user'][6])) ?></p>
    </div>
    <div>
        <div class="projects">
            <?php
                while($row = mysqli_fetch_assoc($projects)){
                    echo "<div class='project'><h2>".$row['title']."</h2>";
                    if($row['finalized']){
                        echo '<div class="subProject"><p>Finalized</p><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div>';
                    } else {
                        echo '<div class="subProject"><p>In Progress</p><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div>';
                    }
                    echo "</div>";
                }
            ?>
            <button>+</button>
        </div>
        <div class="students">
        <?php
                while($row = mysqli_fetch_assoc($students)){
                    echo "<div class='student'><h2>".$row['name'].' '.$row['surnames'].'</h2><div class="subProject"><img src="../../images/icons/bin_icon.svg" alt="edit" style="width:20px;"><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div></div>';
                }
            ?>
            <button>+</button>
            <button>Import Students</button>
        </div>
    </div>
</body>
</html>