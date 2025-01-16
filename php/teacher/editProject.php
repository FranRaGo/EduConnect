<?php
        include '../connection.php';
        include '../functions.php';
    
        $error ='';
    
        session_start();

        $project = getProject($connection,$_GET['id']);

        if(!$_SESSION['user'] || !searchTeacher($connection,NULL,NULL,NULL,$_SESSION['user'][3])){
            header('Location: ../login/login.php');
        }

        if(!empty($_POST['logoutButton'])){
            session_unset();
            session_destroy();
            header('Location: ../login/login.php');
            exit();
        }

        if(!empty($_POST['backButton'])){
            header('Location: index.php');
        }

        if(!empty($_POST['editApplyProjectButton'])){
            editProject($connection,$project[0],$_POST['title'],$_POST['description'],$_POST['finalized']);
        }

        if(!empty($_POST['deleteActivityButton'])){
            deleteActivity($connection,$_POST['deleteActivityButton']);
        }

        if(!empty($_POST['deleteScoreButton'])){
            deleteScore($connection,$_POST['deleteScoreButton']);
        }

        if (!empty($_POST['scoreActivityButton']) || !empty($_POST['scoreActivity'])) { 
            $students = getStudents($connection, $_SESSION['user'][6]);
            if(!empty($_POST['scoreActivityButton'])){
                $items = getItems($connection, $_POST['scoreActivityButton']);
            }else if(!empty($_POST['scoreActivity'])){
                $items = getItems($connection, $_POST['scoreActivity']);
            }
        
            if (!$students || !$items) {
                $error = 'There are no students or items available for this activity';
            }else if(!empty($_POST['scoreActivity']) && !(intval($_POST['score']) >= 0 && intval($_POST['score']) <= 10)){
                $error = 'The score is not valid';
            }else if(!empty($_POST['scoreActivity'])){
                if(getScore($connection,$_POST['student'],$_POST['item'])){
                    modifyScore($connection,$_POST['score'],$_POST['student'],$_POST['item']);
                }else{
                    addScore($connection,$_POST['score'],$_POST['student'],$_POST['item']);
                }
            }
        }

        if(!empty($_POST['addActivityButton']) || !empty($_POST['modifyActivityButton'])){

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


                    if(!empty($_POST['addActivityButton']) && !$error){
                        addActivity($connection,$project[0],$_POST['title'],$_POST['description'],$arrItems);
                    } else if(!empty($_POST['modifyActivityButton']) && !$error ){
                        modifyItems($connection,$_POST['modifyActivityButton'],$arrItems);
                        modifyActivity($connection,$_POST['modifyActivityButton'],$_POST['title'],$_POST['description'],$_POST['finalized']);
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
    <link rel="stylesheet" href="../../css/teacher/editProject.css">
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
    <div class='container'>

        <div class='containerProject'>
            <div class='projectContainer'>
                <h1><?php echo htmlspecialchars($project[1])?></h1>
                <p><?php echo ($project[3]) ? 'Finalized' : 'In Progress'; ?></p>
            </div>
            <p><?php echo htmlspecialchars($project[2]) ?></p>
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
                            <form method="post">
                                <button type='submit' name='modifyActivity' value='<?php echo htmlspecialchars($row['id']) ?>'>
                                    <img src="../../images/icons/edit_icon.svg" alt="Edit Activity">
                                </button>
                            </form>
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
                                                <img src="<?php echo htmlspecialchars('../../images/items_icons/'.$rowItem['icon'])?>" alt="Item Icon">
                                            </div>
                                            <?php
                                    }
                                }else{
                                    ?><p>There are no items</p><?php
                                }
                                ?>
                            </div>
                            <p><?php echo htmlspecialchars($row['description'])?></p>
                        </div>
                        <div class='activityRight'>
                            <p><?php echo ($row['finalized']) ? 'Finalized' : 'In Progress'; ?></p>
                            <div class='buttonContainer'>
                                <form method="post">
                                    <button type='submit' name='deleteActivityButton' value='<?php echo htmlspecialchars($row['id']) ?>'>
                                        <img src="../../images/icons/bin_icon.svg" alt="Delete Activity">
                                    </button>
                                    <button type='submit' name='scoreActivityButton' value='<?php echo htmlspecialchars($row['id']) ?>'>
                                        <img src="../../images/icons/score_icon.png" alt="Score Activity">
                                    </button>
                                </div>
                                </form>
                                <?php if ($error && !empty($_POST['scoreActivityButton']) && $_POST['scoreActivityButton'] === $row['id']): ?>
                                    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php
                }
            }else{
                ?><p>There are no activities</p><?php
            }
            ?>
        </div>
        <form method="post">
            <button type='submit' name='addActivity' value='Add'>
                <img src="../../images/icons/more_icon.png" alt="Add">
            </button>
        </form>
    </div>
    <div class='action'>
        <form method="post">
            <button type="submit" name="editProjectButton" value='edit'>
                <img src="../../images/icons/edit_icon.svg" alt="delete">
            </button>
            <button type="submit" name="backButton" value='back'>
                <img src="../../images/icons/close_icon.png" alt="Back">
            </button>
        </form>
    </div>
</div>
<?php if(!empty($_POST['editProjectButton'])){ ?>
    <div class='containerAction'>
        <h2>Edit Project</h2>
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
                    <div class='buttonContainer'>
                        <input class='button' type="submit" name="editApplyProjectButton" value='Apply'>
                        <input class='button' type="submit" name="cancelButton" value='Cancel'>
                    </div>
                </form>
            </div>
            <?php } else if(!empty($_POST['addActivity']) || (!empty($_POST['addActivityButton']) && $error)){ ?>
                <div class='containerAction'>
                    <h2>Make Activity</h2>
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
                                <td>
                                    <label for="file-upload1" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="file-upload1" type="file" name="icon1" accept="image/*"> 
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" name='item2'></td>
                                <td><input type="number" name='value2' placeholder='%'></td>
                                <td>
                                    <label for="file-upload2" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="file-upload2" type="file" name="icon2" accept="image/*"> 
                                </td>                            
                            </tr>
                            <tr>
                                <td><input type="text" name='item3'></td>
                                <td><input type="number" name='value3' placeholder='%'></td>
                                <td>
                                    <label for="file-upload3" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="file-upload3" type="file" name="icon3" accept="image/*"> 
                                </td>                            
                            </tr>
                            <tr>
                                <td><input type="text" name='item4'></td>
                                <td><input type="number" name='value4' placeholder='%'></td>
                                <td>
                                    <label for="file-upload4" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="file-upload4" type="file" name="icon4" accept="image/*"> 
                                </td>                            
                            </tr>
                            <div class='buttonContainer'>
                                <input class='button' type="submit" name='addActivityButton' value='Add'>
                                <input class='button' type="submit" name="cancelButton" value='Cancel'>
                            </div>
                        </table>
                        <?php if ($error): ?>
                            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                            <?php endif; ?>
                        </form>
                    </div>
                    <?php } else if(!empty($_POST['modifyActivity']) || (!empty($_POST['modifyActivityButton']) && $error)){ 
                        if(!empty($_POST['modifyActivity'])){
                            $activity = getActivity($connection,$_POST['modifyActivity']);
                        }else if(!empty($_POST['modifyActivityButton'])){
                            $activity = getActivity($connection,$_POST['modifyActivityButton']);
                        }
                        
                        $items = getItems($connection,$activity[0]);
                        ?>
    <div class='containerAction'>
        <h2>Edit Activity</h2>
        <form method="post" enctype="multipart/form-data">               
            <input type="text" name='title' placeholder='Title' value='<?php echo htmlspecialchars($activity[1]) ?>'>
            <textarea name="description" placeholder='Description'><?php echo htmlspecialchars($activity[2]) ?></textarea>
            <?php if($activity[3]){ ?>
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
                    <table>
                        <tr>
                            <td>Name</td>
                            <td>Value</td>
                            <td>Icon</td>
                        </tr>
                        <?php
                    $index = 0;
                    
                    if($items){
                        while ($row = mysqli_fetch_assoc($items)) {
                            ?>
                            <tr>
                                <td><input type="text" name="item<?php echo $index + 1; ?>" value='<?php echo htmlspecialchars($row['title']) ?>'></td>
                                <td><input type="number" name="value<?php echo $index + 1; ?>" placeholder="%" value='<?php echo htmlspecialchars($row['value']) ?>'></td>
                                <td>
                                <label for="<?php echo htmlspecialchars("file-upload".$index + 1) ?>" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="<?php echo htmlspecialchars("file-upload".$index + 1) ?>" type="file" name="icon<?php echo $index + 1; ?>" accept="image/*"> 
                            </td>
                            </tr>
                            <?php
                            $index++;
                        }
                    }
                    for($i = $index ; $i < 4 ; $i++){
                        ?>
                        <tr>
                            <td><input type="text" name="item<?php echo $i + 1; ?>"></td>
                            <td><input type="number" name="value<?php echo $i + 1; ?>" placeholder="%"></td>
                            <td>
                                <label for="<?php echo htmlspecialchars("file-upload".$i + 1) ?>" class="custom-file-upload">
                                    Upload File
                                </label>
                                <input id="<?php echo htmlspecialchars("file-upload".$i + 1) ?>" type="file" name="icon<?php echo $i + 1; ?>" accept="image/*"> 
                            </td>
                        </tr>
                        <?php   
                    }
                    ?> <div class='buttonContainer'> <?php
                    if(!empty($_POST['modifyActivity'])){
                        ?>
                            <button class='button' type="submit" name='modifyActivityButton' value='<?php echo htmlspecialchars($_POST['modifyActivity']) ?>'>
                                Apply
                            </button>
                            <?php
                    }else if(!empty($_POST['modifyActivityButton'])){
                        ?>
                            <button class='button' type="submit" name='modifyActivityButton' value='<?php echo htmlspecialchars($_POST['modifyActivityButton']) ?>'>
                                Apply
                            </button>
                            <?php                        
                    }
                    ?>
                <input class='button' type="submit" name="cancelButton" value='Cancel'>
                </div>
            </table>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
        </div>
        <?php } else if ((!empty($_POST['scoreActivityButton']) && !$error) || (!empty($_POST['scoreActivity']) && $error)){ 
            if(!empty($_POST['scoreActivityButton'])){
                $activity_id = $_POST['scoreActivityButton'];
            }else if(!empty($_POST['scoreActivity'])){
                $activity_id = $_POST['scoreActivity'];
            } 
            $items = getItems($connection, $activity_id);
            ?>
        <div class='containerAction'>
            <h2>Score Activity</h2>
            <form method="post">
                <select name="student">
                    <?php while($row = mysqli_fetch_assoc($students)){ ?>
                        <option value="<?php echo htmlspecialchars($row['id']) ?>"><?php echo htmlspecialchars($row['name'].' '.$row['surnames']) ?></option>
                        <?php } ?>
                    </select>
                    <select name="item">
                        <?php while($row = mysqli_fetch_assoc($items)){ ?>
                            <option value="<?php echo htmlspecialchars($row['id']) ?>"><?php echo htmlspecialchars($row['title']) ?></option>
                            <?php } ?>
                        </select>
                        <input type="number" name='score' placeholder='0-10' required min="0" max="10">
                        <div class='buttonContainer'>
                            <button class='button' type="submit" name='scoreActivity' value='<?php if(!empty($_POST['scoreActivityButton'])){echo htmlspecialchars($_POST['scoreActivityButton']);
                }else if(!empty($_POST['scoreActivity'])){echo htmlspecialchars($_POST['scoreActivity']);} ?>'>
                    Score
                </button>
                <button class='button' type="submit" name="backButton" value='back'>
                    Cancel
                </button>
            </div>
            <?php if ($error && !empty($_POST['scoreActivity'])): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>    
            </div>
            </form>
            <div class='scoreList'>
                <?php
            $items = getItems($connection, $activity_id);
            ?>
            <h2>Score List</h2>
            <?php
            while($row = mysqli_fetch_assoc($items)){
            $scores = getScores($connection,$row['id']);
            ?>
                <div class='scoreItemTitle'>
                    <h3><?php echo htmlspecialchars($row['title']) ?></h3>
                    <img src="<?php echo htmlspecialchars('../../images/items_icons/'.$row['icon'])?>" alt="item">
                </div>
                <?php
                if($scores){
                    while($rowScores =  mysqli_fetch_assoc($scores)){
                        
                        $student = getStudent($connection, $rowScores['student_id']);
                        
                        ?>
                        <table>
                            <tr>
                                <td><?php echo htmlspecialchars($student[1].' '.$student[2]) ?></td>
                                <td><?php echo htmlspecialchars($rowScores['score']) ?></td>
                                <td>
                                    <form method="post">
                                        <button type='submit' name='deleteScoreButton' value='<?php echo htmlspecialchars($rowScores['id']) ?>'>
                                            <img src="../../images/icons/bin_icon.svg" alt="Delete Activity">
                                        </button>
                                    </form>
                                </td>
                            </tr>    
                        </table>
                        <?php
                    }
                }else{
                    ?>
                    <p>There are no scores assigned to this item</p>
                    <?php
                }
            }
            ?>
            </div>
        </div>
    <?php } ?>
</body>
</html>