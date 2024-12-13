<?php
    include '../connection.php';
    include '../functions.php';

    $error ='';

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

    if(!empty($_POST['addStudentButton'])){
        if(!empty($_POST['name']) && !empty($_POST['surnames']) && !empty($_POST['dni'])){
            addStudent($connection,$_POST['name'],$_POST['surnames'],$_POST['dni'],$_SESSION['user'][6]);
        }else{
            $error = 'All fields are required';
        }
    }

    if(!empty($_POST['addProjectButton'])){
        if(!empty($_POST['title']) && !empty($_POST['description'])){
            addProject($connection,$_POST['title'],$_POST['description'],$_SESSION['user'][6]);
        }else{
            $error = 'All fields are required';
        }
    }

    if(!empty($_POST['deleteUserButton'])){
        deleteStudent($connection,intval($_POST['deleteUserButton']));
    }

    if(!empty($_POST['deleteProjectButton'])){
        deleteProject($connection,intval($_POST['deleteProjectButton']));
        echo 'hola';
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
            <form method='post'>
            <?php
                while($row = mysqli_fetch_assoc($projects)){
                    echo "<div class='project'><h2>".$row['title']."</h2>";
                    if($row['finalized']){
                        echo '<div class="subProject"><p>Finalized</p><button type="submit" name="deleteProjectButton" value='.$row['id'].'><img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;"></button><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div>';
                    } else {
                        echo '<div class="subProject"><p>In Progress</p><button type="submit" name="deleteProjectButton" value='.$row['id'].'><img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;"></button><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div>';
                    }
                    echo "</div>";
                }
            ?>
                <input type="submit" name='addProjectShow' value='+'>
            </form>
        </div>
        <div class="students">
            <form method='post'>
            <?php
                while($row = mysqli_fetch_assoc($students)){
                    echo "<div class='student'><h2>".$row['name'].' '.$row['surnames'].'</h2><div class="subProject"><button type="submit" name="deleteUserButton" value='.$row['id'].'><img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;"></button><img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></div></div>';
                }
            ?>
                <input type="submit" name='addStudentShow' value='+'>
            </form>
            <button>Import Students</button>
        </div>
        <div class='action'>
                <?php
                if(!empty($_POST['addStudentButton']) || !empty($_POST['addStudentShow'])){
                ?>
                <div class='addStudent'>
                    <h2>Create student</h2>
                    <form method='post'>
                        <input type="text" placeholder='Name' name='name'>
                        <input type="text" placeholder='Surname' name='surnames'>
                        <input type="text" placeholder='DNI' name='dni'>
                        <input type="submit" value='ADD' name='addStudentButton'>
                    </form>
                    <?php if ($error): ?>
                        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </div>
                <?php
                }else if(!empty($_POST['addProjectButton']) || !empty($_POST['addProjectShow'])){
                ?>
                    <div class='addStudent'>
                        <h2>Create project</h2>
                        <form method='post'>
                            <input type="text" placeholder='Title' name='title'>
                            <textarea name="description" placeholder='Description'></textarea>
                            <input type="submit" value='ADD' name='addProjectButton'>
                        </form>
                        <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    </div>
                <?php
                }
                ?>
        </div>
    </div>
</body>
</html>