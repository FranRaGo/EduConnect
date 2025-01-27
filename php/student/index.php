<?php
include '../connection.php';
include '../functions.php';

$error = '';

session_start();

if (!$_SESSION['user'] || !searchStudent($connection, NULL, NULL, NULL, $_SESSION['user'][3])) {
    header('Location: ../login/login.php');
}

if (!empty($_POST['logoutButton'])) {
    session_unset();
    session_destroy();
    header('Location: ../login/login.php');
    exit();
}

$profilePicture = '../../images/profile_images/default.png';

if (!empty($_SESSION['user'][5])) {
    $profilePicture = '../../images/profile_images/' . $_SESSION['user'][5];
}

if (!empty($_POST['cancelButton'])) {
    header('Location: index.php');
}

if (!empty($_POST['changeProfilePhotoButton'])) {
    if ($_FILES['profilePhoto']['name'] != '') {
        changeProfilePhoto($connection, $_SESSION['user'][0], $_SESSION['user'][3], $_FILES['profilePhoto'], 'students', '../../images/profile_images/');
    } else {
        $error = 'File is required';
    }
}

if (!empty($_POST['viewProject'])) {
    header('Location: viewProject.php?id=' . $_POST['viewProject']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHome</title>
    <link rel="stylesheet" href="../../css/student/index.css">
</head>

<body>
    <header>
        <img src="../../images/logo/logoWhite.png" alt="icon" class='logo'>
        <form method="post">
            <button type='submit' name='logoutButton' value='logout'>
                <img src="../../images/icons/logout_icon.png" alt="Logout">
            </button>
        </form>
    </header>
    <div class='profileInfo'>
        <div class='profilePhotoSection'>
            <img src="<?php echo htmlspecialchars($profilePicture); ?>" alt="profilePicture" class='profilePhoto'>
            <form method="post">
                <button type="submit" name="changeProfilePhotoShow" value="1">
                    <img src="../../images/icons/edit_icon.svg" alt="Change" style='width:20px;'>
                </button>
            </form>
        </div>
        <div class='userInfo'>
            <h1><?php echo htmlspecialchars($_SESSION['user'][1] . ' ' . $_SESSION['user'][2]); ?></h1>
            <p><?php echo htmlspecialchars($_SESSION['user'][3]); ?></p>
        </div>
        <p><?php echo htmlspecialchars(searchCourses($connection, $_SESSION['user'][6])) ?></p>
    </div>
    <div>
        <div class='container'>
            <div class="projects">
                <form method='post'>
                    <?php
                    $projects = getProjects($connection, $_SESSION['user'][6]);

                    if ($projects != null) {
                        while ($row = mysqli_fetch_assoc($projects)) {
                    ?>
                            <button type='submit' name='viewProject' value='<?php echo htmlspecialchars($row['id']) ?>' class='project'>
                                <div class="topProject">
                                    <h3><?php echo htmlspecialchars($row['title']) ?></h3>
                                    <p><?php echo htmlspecialchars($row['description']) ?></p>
                                </div>
                                <div class="subProject">
                                    <p><?php if ($row['finalized']) {
                                            echo htmlspecialchars('Finalized');
                                        } else {
                                            echo htmlspecialchars('In Progress');
                                        } ?></p>
                                    <p><?php if (getScoreProyect($connection, $_SESSION['user'][0], $row['id'])) {
                                            echo htmlspecialchars(getScoreProyect($connection, $_SESSION['user'][0], $row['id']));
                                        } ?></p>
                                </div>
                            </button>
                        <?php
                        }
                    } else {
                        ?><p>There are no projects</p>
                        <?php } ?>
                </form>
            </div>
            <?php if (!empty($_POST['changeProfilePhotoShow']) || !empty($_POST['changeProfilePhotoButton'])) { ?>
                <div class='actions'>
                    <h2>Change Profile Photo</h2>
                    <div class="action">
                    
                        <form method="post" enctype="multipart/form-data">
                            <label for="file-upload" class="custom-file-upload">
                                Upload File
                            </label>
                            <input id="file-upload" type="file" name="profilePhoto" accept="image/*"> 
                        </div>
                            <div class="buttonContainer">
                            <button type="submit" name="changeProfilePhotoButton" value='SAVE'>
                                <img src="../../images/icons/save_icon.png" alt="Save">
                            </button>
                            <button type="submit" name="cancelButton" value='cancel'>
                                <img src="../../images/icons/close_icon.png" alt="Back">
                            </button>
                        </div>
                        <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                        <?php endif; ?>
                    </form>
                </div>
            <?php } ?>
        </div>
    </div>

    <footer>
        <p>Todos los derechos reservados &copy; Fran Gonzalez & Alex Muñoz</p>
    </footer>
</body>
</html>