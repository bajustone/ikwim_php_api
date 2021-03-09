<?php header("Content-Type:application/json; charset=utf-8"); 
include "./reads.php";
include "./update.php";

?>

<?php 
$requestType = $_SERVER['REQUEST_METHOD'];
if( $requestType === "PUT"){
    $body = file_get_contents("php://input");
    $rowId = $_GET["id"];
    return updateReception($body, $rowId);
}
elseif($requestType === "GET"){
    readReceptions();
}



?>