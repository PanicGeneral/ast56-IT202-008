<?php
require_once(__DIR__ . "/../../../lib/db.php"); ?>

<?php
// don't edit - this
$expected_fields = ["task", "due", "assigned"];
$diff = array_diff($expected_fields, array_keys($_GET));

if (empty($diff)) {

    // data variables, don't edit
    $task = $_GET["task"];
    $due = $_GET["due"]; //hint: must be a valid MySQL date format
    $assigned = $_GET["assigned"]; // Must be "self" or a valid format (not empty or equivalent)

    $is_valid = true;
    // TODO Validate the incoming data for correct format based on the SQL table definition.
    // When not valid, provide a user-friendly message of what specifically was wrong and set $is_valid to false.
    // Assigned should check for "self" if a valid format/value isn't provided.
    // Start validations
if(empty($task)){
    echo "Please provide a Task!";
    $is_valid = false;
}    

$date = DateTime::createFromFormat('Y-m-d', $due);

if (!$date || $date->format('Y-m-d') !== $due){
    echo "Please Enter Valid Date Format YYYY-MM-DD;";
    $is_valid = false;
}


if(empty($assigned)){
    $assigned = "self";
}
elseif(!ctype_alnum($assigned)){
    echo "Invalid Format! Please use only Alphanumeric characters";
    $is_valid = false;
}

    // End validations

    
    if ($is_valid) {
        /*
        Design a query to insert the incoming data to the proper columns.
        Ensure valid and proper PDO named placeholders are used.
        https://phpdelusions.net/pdo
        */
        $query = "INSERT INTO todos (task,due,assigned) VALUES (:task,:due,:assigned)"; // edit this
        $params = [":task"=>$task,":due"=>$due,":assigned"=>$assigned]; // Apply the proper PDO placeholder to variable mapping here
        try {
            $db = getDB();
            $stmt = $db->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
                echo "Inserted new Todo with id " . $db->lastInsertId();
            } else {
                echo "Failed to insert";
            }
        } catch (PDOException $e) {
            // extra credit
            // check if the exception was related to a unique constraint
            // provide an appropriate user-friendly message for this scenario
            // Otherwise show the default message below
            if ($e->getCode()==23000){
                echo "Duplication Error! Entry already exists";
            }
            else{
            echo "There was an error inserting the record; check the logs (terminal)";
            }
            error_log("Insert Error: " . var_export($e, true)); // shows in the terminal
        }
    } else {
        error_log("Creation input wasn't valid");
    }
}
?>
<html>

<body>
    <?php require_once(__DIR__ . "/../nav.php"); ?>
    <section>
        <h2>Create ToDo </h2>
        <form>
            <!-- design the form with proper labels and input fields with the correct types based on the SQL table.
             Wrap each label/input pair in a div tag.
             For "Assigned" ensure the default value is "self". -->
            <div>
                <label for ="task">TASK</label>
                <input type = "text" name="task" required/>
            </div>

            <div>
                <label for ="due">DUE</label>
                <input type= "date" name="due"/>
            </div>
             
            <div>
                <label for ="assigned">ASSIGNED</label>
                <input type="text" name="assigned" value="self"/>
            </div>

            <div>
                <input type="submit" />
            </div>
        </form>
    </section>
</body>
</body>

</html>