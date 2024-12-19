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

        if(!empty($_POST['addActivityButton'])){

            if(!empty($_POST['title']) && !empty($_POST['description'])){

                $arrItems = [];
                $totalValue = 0;
                if(empty($_POST['item1']) && empty($_POST['value1']) && $_FILES['icon1']['error'] === UPLOAD_ERR_NO_FILE){
                    $error = 'First item is requiered';
                }else if(empty($_POST['item1']) || empty($_POST['value1']) || $_FILES['icon1']['error'] === UPLOAD_ERR_NO_FILE){
                    $error = 'All fields of an item are required';
                }else{

                    $error ='';
                    $totalValue += intval($_POST['value1']);
                    array_push($arrItems,[$_POST['item1'],intval($_POST['value1']),$_FILES['icon1']]);

                    if(!empty($_POST['item2']) || !empty($_POST['value2']) || $_FILES['icon2']['error'] != UPLOAD_ERR_NO_FILE){
                        if(!empty($_POST['item2']) && !empty($_POST['value2']) && $_FILES['icon2']['error'] != UPLOAD_ERR_NO_FILE){
                            $totalValue += intval($_POST['value2']);
                            array_push($arrItems,[$_POST['item2'],intval($_POST['value2']),$_FILES['icon2']]);     
                        }else{
                            $error = 'All fields of an item are required';
                        }
                    }

                    if(!empty($_POST['item3']) || !empty($_POST['value3']) || $_FILES['icon3']['error'] != UPLOAD_ERR_NO_FILE){
                        if(!empty($_POST['item3']) && !empty($_POST['value3']) && $_FILES['icon3']['error'] != UPLOAD_ERR_NO_FILE){
                            $totalValue += intval($_POST['value3']);
                            array_push($arrItems,[$_POST['item3'],intval($_POST['value3']),$_FILES['icon3']]);     
                        }else{
                            $error = 'All fields of an item are required';
                        }
                    }

                    if(!empty($_POST['item4']) || !empty($_POST['value4']) || $_FILES['icon4']['error'] != UPLOAD_ERR_NO_FILE){
                        if(!empty($_POST['item4']) && !empty($_POST['value4']) && $_FILES['icon4']['error'] != UPLOAD_ERR_NO_FILE){
                            $totalValue += intval($_POST['value4']);
                            array_push($arrItems,[$_POST['item4'],intval($_POST['value4']),$_FILES['icon4']]);     
                        }else{
                            $error = 'All fields of an item are required';
                        }
                    }

                    if($totalValue > 100){
                        $error = 'The total value is more than 100';
                    }else if($totalValue < 100){
                        $error = 'the total value is less than 100';
                    }

                    if(!$error){
                        addActivity($connection,$project[0],$_POST['title'],$_POST['description'],$arrItems);
                    }
                }

            }else{
                $error = 'Title and description are required';
            }

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
        <div class='projectContainer'>
            <div>
                <h1><?php echo htmlspecialchars($project[1])?></h1>
                <p><?php echo ($project[3]) ? 'Finalizado' : 'En progreso'; ?></p>
            </div>
            <p><?php echo htmlspecialchars($project[2]) ?></p>
        </div>
        <div class='activitiesContainer'>
            <div class='activities'>
            <?php
                $activities = getActivities($connection,$project[0]);

                if($activities != null){
                    while($row = mysqli_fetch_assoc($activities)){
                        ?>
                        <div class='activity'>
                            <div class='activityLeft'>
                                <h2><?php echo htmlspecialchars($row['title'])?></h2>
                                <img src="../../images/icons/edit_icon.svg" alt="Edit Activity" style="width:20px;">
                            </div>
                            <div class='activityCenter'>
                                <div class='items'>
                                <?php
                                    $items = getItems($connection,$row['id']);

                                    if($items != null){
                                        while($rowItem = mysqli_fetch_assoc($items)){
                                            ?>
                                                <div class='item'>
                                                    <p><?php echo htmlspecialchars($rowItem['title']."(".$rowItem['value'].")")?></p>
                                                    <img src="<?php echo htmlspecialchars('../../images/items_icons/'.$rowItem['icon'])?>" alt="Item Icon" style="width:20px;">
                                                </div>
                                            <?php
                                        }
                                    }else{
                                        ?>
                                        <p>There are no items</p>
                                        <?php
                                    }
                                ?>
                                </div>
                                <p><?php echo htmlspecialchars($row['description'])?></p>
                            </div>
                            <div class='ActivityRight'>
                                <img src="../../images/icons/bin_icon.svg" alt="Delete Activity" style="width:20px;">
                                <img src="../../images/icons/score_icon.png" alt="Score Activity" style="width:20px;">
                            </div>
                        </div>
                        <?php
                    }
                }else{
                    ?>
                    <p>There are no activities</p>
                    <?php
                }
            ?>
            </div>
            <form method="post">
                <button type='submit' name='addActivity' value='Add'>
                    <img src="../../images/icons/more_icon.png" alt="Add" style="width:20px;">
                </button>
            </form>
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
    <?php } else if(!empty($_POST['addActivity']) || (!empty($_POST['addActivityButton']) && $error)){ ?>
        <div class='addContainer'>
            <form method="post" enctype="multipart/form-data">               
                <input type="text" name='title' placeholder='Title'>
                <textarea name="description" placeholder='Description'></textarea>
                <table>
                    <tr>
                        <td>Name</td>
                        <td>Value</td>
                        <td>Icon</td>
                    </tr>
                    <tr>
                        <td><input type="text" name='item1'></td>
                        <td><input type="number" name='value1' placeholder='%'></td>
                        <td><input type="file" name='icon1' accept="image/*"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name='item2'></td>
                        <td><input type="number" name='value2' placeholder='%'></td>
                        <td><input type="file" name='icon2' accept="image/*"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name='item3'></td>
                        <td><input type="number" name='value3' placeholder='%'></td>
                        <td><input type="file" name='icon3' accept="image/*"></td>
                    </tr>
                    <tr>
                        <td><input type="text" name='item4'></td>
                        <td><input type="number" name='value4' placeholder='%'></td>
                        <td><input type="file" name='icon4' accept="image/*"></td>
                    </tr>
                    <input type="submit" name='addActivityButton' value='Add'>
                    <input type="submit" name="cancelButton" value='Cancel'>
                </table>
                <?php if ($error): ?>
                        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
        </div>
    <?php } ?>
</body>
</html>