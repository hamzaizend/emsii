<?php
include("connect.php");

if(isset($_POST["post"])){

if(isset($_POST["subject"]))

{



$subject = mysqli_real_escape_string($con, $_POST["subject"]);

$comment = mysqli_real_escape_string($con, $_POST["comment"]);

$query = "INSERT INTO comments(comment_subject, comment_text)VALUES ('$subject', '$comment')";

mysqli_query($con, $query);

}
}

?>