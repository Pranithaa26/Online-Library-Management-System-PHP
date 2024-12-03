<?php
include('includes/config.php');
if (isset($_POST['emailid'])) {
    $email = $_POST['emailid'];
    $sql = "SELECT EmailId FROM tblstudents WHERE EmailId = :email";
    $query = $dbh->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    if ($query->rowCount() > 0) {
        echo "<span style='color:red;'>Email is already taken.</span>";
    } else {
        echo "<span style='color:green;'>Email is available.</span>";
    }
}
?>
