<?php
include '../connection.php';
include '../functions.php';

session_start();

$project = getProject($connection, $_GET['id']);

if (!$_SESSION['user'] || !searchStudent($connection, NULL, NULL, NULL, $_SESSION['user'][3])) {
    header('Location: ../login/login.php');
}

if (!empty($_POST['logoutButton'])) {
    session_unset();
    session_destroy();
    header('Location: ../login/login.php');
    exit();
}

if (!empty($_POST['backButton'])) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Project</title>
    <link rel="stylesheet" href="../../css/student/viewProject.css">
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
    <div class='container'>

        <div class='projectContainer'>
            <div class="projectHeader">
                <h1><?php echo htmlspecialchars($project[1]) ?></h1>
                <p><?php echo ($project[3]) ? 'Finalizado' : 'En progreso'; ?></p>
            </div>
            <p><?php echo htmlspecialchars($project[2]) ?></p>
            <div class='activitiesContainer'>
                <div class='activities'>
                    <?php
                    $activities = getActivities($connection, $project[0]);

                    if ($activities != null) {
                        while ($row = mysqli_fetch_assoc($activities)) {
                    ?>
                            <div class='activity'>
                                <div class='activityLeft'>
                                    <h2><?php echo htmlspecialchars($row['title']) ?></h2>
                                </div>
                                <div class='activityCenter'>
                                    <div class='items'>
                                        <?php
                                        $items = getItems($connection, $row['id']);

                                        if ($items != null) {
                                            while ($rowItem = mysqli_fetch_assoc($items)) {
                                        ?>
                                                <div class='item'>
                                                    <p><?php echo htmlspecialchars($rowItem['title'] . "(" . $rowItem['value'] . ")");
                                                        if (getScore($connection, $_SESSION['user'][0], $rowItem['id'])) {
                                                            echo "(" . getScore($connection, $_SESSION['user'][0], $rowItem['id']) . "/10)";
                                                        } ?></p>
                                                    <img src="<?php echo htmlspecialchars('../../images/items_icons/' . $rowItem['icon']) ?>" alt="Item Icon">
                                                </div>
                                            <?php
                                            }
                                        } else {
                                            ?><p>There are no items</p><?php
                                                                    }
                                                                        ?>
                                    </div>
                                    <p><?php echo htmlspecialchars($row['description']) ?></p>
                                </div>
                                <div class='activityRight'>
                                    <p>
                                        <?php
                                        if (getScoreActivity($connection, $_SESSION['user'][0], $row['id'])) {
                                            echo htmlspecialchars(getScoreActivity($connection, $_SESSION['user'][0], $row['id']) . '/10');
                                        } ?>
                                    </p>
                                    <p><?php if ($row['finalized']) {
                                            echo htmlspecialchars('Finalized');
                                        } else {
                                            echo htmlspecialchars('In Progress');
                                        } ?></p>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        ?><p>There are no activities</p><?php
                                                        }
                                                            ?>
                </div>
                <div class='action'>
                    <form method="post">
                        <button type="submit" name="backButton" value='back'>
                            <img src="../../images/icons/close_icon.png" alt="Back">
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>Todos los derechos reservados &copy; Fran Gonzalez & Alex Muñoz</p>
    </footer>
</body>
</html>