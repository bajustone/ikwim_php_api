<?php



    function updateReception($body, $rowId){
        $updatedVales = json_decode($body);
        $tableName = "TransferReceptionLub";
        $whereClause = array("transferReceptionId"=> $rowId );
        $queryResult = PDOHelper::updateTable($tableName, $updatedVales, $whereClause);
        $resp = array(
          "status" => $queryResult["status"],
        );
        if($queryResult["status"] == false){
          $resp["error"] = $queryResult["error"];
        }
        echo(json_encode($resp));

    }
?>