<?php

include("connection.php");

if($conn){
    echo "✅ Database Connected Successfully!";
}else{
    echo "❌ Database Connection Failed!";
}

?>

<form method="POST">
    <input type="text" name="name" placeholder="Enter Name" required><br><br>
    <input type="number" name="sleep" placeholder="Sleep Hours" required><br><br>

    <button type="submit" name="predict">Predict</button>
</form>

<?php
if (isset($prediction)) {
    echo "<h3>Prediction: $prediction</h3>";
}
?>