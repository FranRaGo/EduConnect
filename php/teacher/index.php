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

    if(!empty($_POST['cancelButton'])){
        header('Location: index.php');
    }

    if(!empty($_POST['addStudentButton'])){
        if(!empty($_POST['name']) && !empty($_POST['surnames']) && !empty($_POST['dni'])){
            addStudent($connection,$_POST['name'],$_POST['surnames'],$_POST['dni'],$_SESSION['user'][6]);
        }else{
            $error = 'All fields are required';
        }
    }

    if(!empty($_POST['editStudentButton'])){
        modifyStudent($connection,$_POST['editStudentButton'],$_POST['name'],$_POST['surnames'],$_POST['gmail'],$_POST['dni']);
    }

    if(!empty($_POST['addProjectButton'])){
        if(!empty($_POST['title']) && !empty($_POST['description'])){
            addProject($connection,$_POST['title'],$_POST['description'],$_SESSION['user'][6]);
        }else{
            $error = 'All fields are required';
        }
    }

    if(!empty($_POST['editProjectButton'])){
        header('Location: editProject.php?id='.$_POST['editProjectButton']);
    }

    if(!empty($_POST['deleteStudentButton'])){
        deleteStudent($connection,intval($_POST['deleteStudentButton']));
    }

    if(!empty($_POST['deleteProjectButton'])){
        deleteProject($connection,intval($_POST['deleteProjectButton']));
    }

    if(!empty($_POST['changeProfilePhotoButton'])){
        if($_FILES['profilePhoto']['name'] != ''){
            changeProfilePhoto($connection,$_SESSION['user'][0],$_SESSION['user'][3],$_FILES['profilePhoto'],'teachers','../../images/profile_images/');
        }else{
            $error = 'File is required';
        }
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
            <form method="post">
                <button type="submit" name="changeProfilePhotoShow" value="1">
                    <img src="../../images/icons/edit_icon.svg" alt="Change" style='width:20px;'>
                </button>
            </form>
        </div>
        <div>
            <h1><?php echo htmlspecialchars($_SESSION['user'][1].' '.$_SESSION['user'][2]); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['user'][3]); ?></p>
        </div>
        <p><?php echo htmlspecialchars(searchCourses($connection,$_SESSION['user'][6])) ?></p>
    </div>
    <div>
        <div class="projects">
            <h2>Projects</h2>
            <form method='post'>
            <?php
                $projects = getProjects($connection,$_SESSION['user'][6]);

                if($projects != null){
                    while($row = mysqli_fetch_assoc($projects)){
                        echo "<div class='project'><h3>".$row['title']."</h3>";
                        if($row['finalized']){
                            echo '<div class="subProject">
                                    <p>Finalized</p>
                                    <button type="submit" name="deleteProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;">
                                    </button>
                                    <button type="submit" name="editProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;">
                                    </button>
                                    </div>';
                        } else {
                            echo '<div class="subProject">
                                    <p>In Progress</p>
                                    <button type="submit" name="deleteProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;">
                                    </button>
                                    <button type="submit" name="editProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;">
                                    </button>
                                    </div>';
                        }
                        echo "</div>";
                    }
                }else{
                    ?><p>There are no projects</p><?php
                }

            ?>
                <input type="submit" name='addProjectShow' value='+'>
            </form>
        </div>
        <div class="students">
            <h2>Students</h2>
            <form method='post'>
            <?php
                $students = getStudents($connection,$_SESSION['user'][6]);

                if($students != null){
                    while($row = mysqli_fetch_assoc($students)){
                        echo "<div class='student'>
                                <h3>".$row['name'].' '.$row['surnames'].'</h3>
                                <div class="subProject">
                                    <button type="submit" name="deleteStudentButton" value='.$row['id'].'>
                                        <img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;">
                                    </button>
                                    <button type="submit" name="editStudentShow" value='.$row['id'].'>
                                        <img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;"></button>
                                    </div>
                                </div>';
                    }               
                }else{
                    ?><p>There are no students</p><?php
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
                    <h2>Create Student</h2>
                    <form method='post'>
                        <input type="text" placeholder='Name' name='name'>
                        <input type="text" placeholder='Surname' name='surnames'>
                        <input type="text" placeholder='DNI' name='dni'>
                        <input type="submit" value='ADD' name='addStudentButton'>
                        <button type='submit' name='cancelButton'><img src="../../images/icons/close_icon.png" alt="Cancel" style="width:20px;"></button>
                    </form>
                    <?php if ($error): ?>
                        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                </div>
                <?php
                }else if(!empty($_POST['addProjectButton']) || !empty($_POST['addProjectShow'])){
                ?>
                    <div class='addProject'>
                        <h2>Create Project</h2>
                        <form method='post'>
                            <input type="text" placeholder='Title' name='title'>
                            <textarea name="description" placeholder='Description'></textarea>
                            <input type="submit" value='ADD' name='addProjectButton'>
                            <button type='submit' name='cancelButton'><img src="../../images/icons/close_icon.png" alt="Cancel" style="width:20px;"></button>
                        </form>
                        <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    </div>
                <?php
                }else if(!empty($_POST['editStudentButton']) || !empty($_POST['editStudentShow'])){
                ?>
                    <div class='editStudent'>
                        <h2>Edit Student</h2>
                        <form method='post'>
                            <?php
                                $student = searchStudent($connection, $_POST['editStudentShow'], null, null, null);

                                echo '<input type="text" value="'.$student[1].'" placeholder="Name" name="name">';
                                echo '<input type="text" value="'.$student[2].'" placeholder="Surname" name="surnames">';
                                echo '<input type="text" value="'.$student[3].'" placeholder="Gmail" name="gmail">';
                                echo '<input type="text" value="'.$student[7].'" placeholder="DNI" name="dni">';
                                echo '<button type="submit" value="'.$_POST['editStudentShow'].'"  name="editStudentButton">SAVE</button>';
                            ?>                            
                            <button type='submit' name='cancelButton'><img src="../../images/icons/close_icon.png" alt="Cancel" style="width:20px;"></button>
                        </form>
                        <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    </div>
                <?php
                }else if(!empty($_POST['changeProfilePhotoShow']) || !empty($_POST['changeProfilePhotoButton'])){
                ?>
                    <div class='changeProfilePhoto'>
                        <h2>Change Profile Photo</h2>
                        <form method="post" enctype="multipart/form-data">
                            <input type="file" name="profilePhoto" accept="image/*">
                            <button type="submit" name="changeProfilePhotoButton" value='SAVE'>SAVE</button>
                            <button type='submit' name='cancelButton'><img src="../../images/icons/close_icon.png" alt="Cancel" style="width:20px;"></button>
                        </form>
                    </div>
                    <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>
                <?php
                }
                ?>
        </div>
    </div>
</body>
</html>