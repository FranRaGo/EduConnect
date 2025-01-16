<?php
    include '../connection.php';
    include '../functions.php';

    $error ='';

    session_start();

    if(!$_SESSION['user'] || !searchTeacher($connection,NULL,NULL,NULL,$_SESSION['user'][3])){
        header('Location: ../login/login.php');
    }

    if(!empty($_POST['logoutButton'])){
        session_unset();
        session_destroy();
        header('Location: ../login/login.php');
        exit();
    }

    $profilePicture = '../../images/profile_images/default.png';

    if(!empty($_SESSION['user'][5])){
        $profilePicture = '../../images/profile_images/'.$_SESSION['user'][5];
    }

    if(!empty($_POST['importSubmit'])){
        if($_FILES['studentsFile']['name'] != ''){
            importStudents($connection,$_FILES['studentsFile']['tmp_name']);
        }else{
            $error = 'File is required';
        }
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
    <link rel="stylesheet" href="../../css/teacher/index.css">
</head>
<body>
    <header>
        <img class='logo' src="../../images/logo/logoWhite.png" alt="icon">
        <form method="post">
            <button type='submit' name='logoutButton' value='logout'>
                <img src="../../images/icons/logout_icon.png" alt="Logout">
            </button>
        </form>
    </header>
    <div class='profileInfo'>
        <div class='profilePhotoSection'>
            <img class='profilePhoto' src="<?php echo htmlspecialchars($profilePicture); ?>" alt="profilePicture">
            <form method="post">
                <button type="submit" name="changeProfilePhotoShow" value="1">
                    <img src="../../images/icons/edit_icon.svg" alt="Change">
                </button>
            </form>
        </div>
        <div class='userInfo'>
            <h1><?php echo htmlspecialchars($_SESSION['user'][1].' '.$_SESSION['user'][2]); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['user'][3]); ?></p>
        </div>
        <p><?php echo htmlspecialchars(searchCourses($connection,$_SESSION['user'][6])) ?></p>
    </div>
    <div class='container'>
        <div class="projects">
            <h2>Projects</h2>
            <form method='post'>
                <div class='scroll'>
                <?php
                $projects = getProjects($connection,$_SESSION['user'][6]);

                if($projects != null){
                    while($row = mysqli_fetch_assoc($projects)){
                        echo "<div class='project'><h3>".$row['title']."</h3>";
                        if($row['finalized']){
                            echo '<div class="sub">
                                    <button type="submit" name="deleteProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;">
                                    </button>
                                    <p>Finalized</p>
                                    <button type="submit" name="editProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/edit_icon.svg" alt="edit" style="width:20px;">
                                    </button>
                                    </div>';
                        } else {
                            echo '<div class="sub">
                                    <button type="submit" name="deleteProjectButton" value='.$row['id'].'>
                                        <img src="../../images/icons/bin_icon.svg" alt="delete" style="width:20px;">
                                    </button>
                                    <p>In Progress</p>
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
            </div>
            </form>
            <form method="post">
                <input class='button' type="submit" name='addProjectShow' value='+'>
            </form>
        </div>
        
        <!--<div class='actions'> -->
                <?php
                if(!empty($_POST['addStudentButton']) || !empty($_POST['addStudentShow'])){
                ?>
                <div class='actions'>
                    <h2>Create Student</h2>
                    <div class='action'>
                        <form method='post'>
                            <input type="text" placeholder='Name' name='name'>
                            <input type="text" placeholder='Surname' name='surnames'>
                            <input type="text" placeholder='DNI' name='dni'>
                            <?php if ($error): ?>
                                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                            <?php endif; ?>
                    </div>
                            <div>
                                <input class='button' type="submit" value='ADD' name='addStudentButton'>
                                <input class='button' type='submit' name='cancelButton' value='X'>
                            </div>
                        </form>
                </div>
                <?php
                }else if(!empty($_POST['addProjectButton']) || !empty($_POST['addProjectShow'])){
                ?>
                    <div class='actions'>
                        <h2>Create Project</h2>
                        <div class='action'>
                            <form method='post'>
                                <input type="text" placeholder='Title' name='title'>
                                <textarea name="description" placeholder='Description'></textarea>
                                <?php if ($error): ?>
                                    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                                <?php endif; ?>
                        </div>
                            <div>
                                <input class='button' type="submit" value='ADD' name='addProjectButton'>
                                <input class='button' type='submit' name='cancelButton' value='X'>
                            </div>
                        </form>
                    </div>
                <?php
                }else if(!empty($_POST['editStudentButton']) || !empty($_POST['editStudentShow'])){
                ?>
                    <div class='actions'>
                        <h2>Edit Student</h2>
                        <div class='action'>
                            <form method='post'>
                                <?php $student = searchStudent($connection, $_POST['editStudentShow'], null, null, null); ?>                                
                                <input type="text" value="<?php echo htmlspecialchars($student[1])?>" placeholder="Name" name="name">
                                <input type="text" value="<?php echo htmlspecialchars($student[2])?>" placeholder="Surname" name="surnames">
                                <input type="text" value="<?php echo htmlspecialchars($student[3])?>" placeholder="Gmail" name="gmail">
                                <input type="text" value="<?php echo htmlspecialchars($student[7])?>" placeholder="DNI" name="dni">
                                <?php if ($error): ?>
                                    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                                <?php endif; ?>
                        </div>
                        <div>
                                <button class="button" type="submit" value="<?php echo htmlspecialchars($_POST['editStudentShow'])?>"  name="editStudentButton">SAVE</button>
                                <input class="button" type="submit" name="cancelButton" value="X">
                        </div>
                        </form>
                    </div>
                <?php
                }else if(!empty($_POST['changeProfilePhotoShow']) || !empty($_POST['changeProfilePhotoButton'])){
                ?>
                    <div class='actions'>
                        <h2>Change Profile Photo</h2>
                        <div class='action'>
                            <form method="post" enctype="multipart/form-data">                          
                            <label for="file-upload" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="file-upload" type="file" name="profilePhoto" accept="image/*">                        </div>
                                <div>
                                    <input class='button' type="submit" name="changeProfilePhotoButton" value='SAVE'>
                                    <input class='button' type='submit' name='cancelButton' value='X'>                          
                                </div>
                                <?php if ($error): ?>
                                        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                                <?php endif; ?>
                            </form>
                    </div>
                <?php
               }else if(!empty($_POST['importButton']) || !empty($_POST['importSubmit'])){
                ?>
                    <div class='actions'>
                        <h2>Import File</h2>
                        <div class='action'>
                            <form method="post" enctype="multipart/form-data">
                            <label for="file-upload" class="custom-file-upload">
                                    Upload File
                                </label>
                            <input id="file-upload" type="file" name="studentsFile" accept=".csv">  
                        </div>
                        <div>
                            <input class='button import' type="submit" name="importSubmit" value='Import Students'>
                            <input class='button' type='submit' name='cancelButton' value='X'>
                        </div>
                        <?php if ($error): ?>
                                        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                        </form>
                    </div>
                <?php
                }
                ?>
        <!--</div>-->
        <div class="students">
            <h2>Students</h2>
            <form method='post'>
                <div class='scroll'>
                <?php
                $students = getStudents($connection,$_SESSION['user'][6]);
                
                if($students != null){
                    while($row = mysqli_fetch_assoc($students)){
                        echo "<div class='student'>
                        <h3>".$row['name'].' '.$row['surnames'].'</h3>
                        <div class="sub">
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
            </div>
        </form>
        <form method="post">
            <div>
                <input class='button' type="submit" name='addStudentShow' value='+' >
                <input class='button import' type="submit" name='importButton' value='Import Students'>
            </div>
        </form>
    </div>
    </div>
</body>
</html>