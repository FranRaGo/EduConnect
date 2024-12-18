<?php
        include '../connection.php';
        include '../functions.php';
    
        $error ='';
    
        session_start();

        $project = getProject($connection,$_GET['id']);

        echo $project[0].' -- '.$project[1].' -- '.$project[2].' -- '.$project[3];

        if(!empty($_POST['backButton'])){
            header('Location: index.php');
        }

        if(!empty($_POST['editApplyProjectButton'])){
            editProject($connection,$project[0],$_POST['title'],$_POST['description'],$_POST['finalized']);
        }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
</head>
<body>
    <div class='container'>
        <div class='project'>
            <div>
                <h1><?php echo htmlspecialchars($project[1]) ?></h1>
                <p><?php echo ($project[3]) ? 'Finalizado' : 'En progreso'; ?></p>
            </div>
            <p><?php echo htmlspecialchars($project[2]) ?></p>
        </div>
        <div class='activities'>

        </div>
        <div class='action'>
            <form method="post">
                <button type="submit" name="editProjectButton" value='edit'>
                    <img src="../../images/icons/edit_icon.svg" alt="delete" style="width:20px;">
                </button>
                <button type="submit" name="backButton" value='back'>
                    <img src="../../images/icons/close_icon.png" alt="Back" style="width:20px;">
                </button>
            </form>
        </div>
    </div>
    <?php if(!empty($_POST['editProjectButton'])){ ?>
        <div class='editContainer'>
            <form method="post">
                <input type="text" name="title" value='<?php echo $project[1] ?>' placeholder='Project title'>
                <textarea name="description" placeholder='Project title'><?php echo $project[2] ?></textarea>
                <?php if($project[3]){ ?>
                    <select name="finalized">
                        <option value=1>Finalized</option>
                        <option value=0>In Progres</option>
                    </select>
                <?php }else{ ?>
                    <select name="finalized">
                        <option value=0>In Progres</option>
                        <option value=1>Finalized</option>
                    </select>
                <?php } ?>
                <input type="submit" name="editApplyProjectButton" value='Apply'>
                <input type="submit" name="cancelButton" value='Cancel'>
            </form>
        </div>
    <?php } ?>
</body>
</html>