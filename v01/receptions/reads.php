<?php include "../lib/mysqli.php";?>
<?php

function fetchApprovisionnements( $date, $activityId){
    $query = "SELECT TransferReceptionLub.*, ReceptionStatus.label as statusLabel, destinationStation.stationName as destinationStation, destinationActivity.activityName as destinationActivity,
    sourceStation.stationName as sourceStation, sourceActivity.activityName as sourceActivity,
    sourceUser.firstName as sourceUserFirstName,
    sourceUser.lastName as sourceUserLastName,
    destinationUser.firstName as destinationUserFirstName,
    destinationUser.lastName as destinationUserLastName,
    Product.productName
    FROM TransferReceptionLub
    INNER JOIN StationActivity as transferDestination on transferDestination.stationActivityId = TransferReceptionLub.toStationActivity 
    INNER JOIN Station as destinationStation on transferDestination.stationId = destinationStation.stationId
    INNER JOIN Activity as destinationActivity on transferDestination.activityId = destinationActivity.activityId
    INNER JOIN StationActivity as transferSource on transferSource.stationActivityId = TransferReceptionLub.fromStationActivityId 
    INNER JOIN Station as sourceStation on transferSource.stationId = sourceStation.stationId
    INNER JOIN Activity as sourceActivity on transferSource.activityId = sourceActivity.activityId

    INNER JOIN Employees as sourceUser on sourceUser.employeeId = TransferReceptionLub.fromResponsable
    INNER JOIN Employees as destinationUser on destinationUser.employeeId = TransferReceptionLub.receivedBy

    INNER JOIN Product on Product.productId = TransferReceptionLub.productId
    INNER JOIN ReceptionStatus on ReceptionStatus.receptionStatusId = TransferReceptionLub.receptionStatus
    

    WHERE transferReceptionDate= :dateValue AND (fromStationActivityId= :activityId or toStationActivity = :activityId)";

    return PDOHelper::get_result($query, array("dateValue"=>$date, "activityId"=>$activityId));
}

function readReceptions(){
    $date = $_GET['date'];
    $activityId = $_GET['activityId'];
    $response = fetchApprovisionnements( $date, $activityId);
    $json = json_encode($response);
    echo($json);
}
?>