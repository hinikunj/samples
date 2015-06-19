<body>
    <!--<link rel="stylesheet" type="text/css" href="_styles.css" media="screen">-->
    <?php
    mysql_connect('localhost', 'root');
    mysql_select_db('test_job');


    $qry = "SELECT * FROM treeview_items";
    $result = mysql_query($qry);


    $arrayCategories = array();

    while ($row = mysql_fetch_assoc($result)) {
        $arrayCategories[$row['id']] = array("parent_id" => $row['parent_id'], "name" =>
            $row['name']);
    }

//createTree($arrayCategories, 0);

    function createTree($array, $currentParent, $currLevel = 0, $prevLevel = -1) {

        foreach ($array as $categoryId => $category) {

            if ($currentParent == $category['parent_id']) {
                
                if ($currLevel > $prevLevel)
                    echo " <ol class='tree'> ";

                if ($currLevel == $prevLevel)
                    echo " </li> ";

                echo '<li> <label for="subfolder2">' . $category['name'] . '</label>';

                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }

                $currLevel++;

                createTree($array, $categoryId, $currLevel, $prevLevel);

                $currLevel--;
            }
        }

        if ($currLevel == $prevLevel)
            echo " </li>  </ol> ";
    }
    

    function display_children($parent, $level) { 

        // retrieve all children of $parent 
        $result = mysql_query('SELECT * FROM treeview_items  WHERE parent_id="'.$parent.'";'); 
        while ($row = mysql_fetch_array($result)) { 
            echo "<option value='".$row['id']."'>".str_repeat('--',$level).$row['name']."</option>" ."<br>"; 
            display_children($row['id'], $level+1); 

        } 

    } 
    
    ?>
    <div id="content" class="general-style1" style="width: 60%">
        <div style="float: right"><a href="treeview.php?flag=add">ADD</a></div>
        <?php
        if(!empty($_GET['flag']) && $_GET['flag'] == 'add'){
            if(!empty($_POST)){
                $name = $_POST['name'];
                $parent_id = $_POST['parent_id'];
                
                $sql = "INSERT INTO treeview_items (`name`, `parent_id`)
                        VALUES ('$name', '$parent_id')";
                if (mysql_query($sql)) {
                    header("Location: http://localhost/treeview.php");
                } else {
                    echo "Error: " . $sql . "<br>" . mysql_error($conn);
                }
            }
            ?>
        <fieldset style="width:300px;">
            <form method="post" action="">
                Parent: <select name="parent_id">
                    <option value="0">--- select parent ---</option>
                    <?php 
                        echo display_children(0,0);
                    ?>
                </select><br>
                Name: <input type="text" name="name"><br>
                Age: <input type="text" name="age"><br>
                <br>
                <input type="submit" name="submit">
            </form>
        </fieldset>
        <?php
        } else {
            if (mysql_num_rows($result) != 0) {
                createTree($arrayCategories, 0);
            }
        }
        ?>

    </div>
</body>
</html>