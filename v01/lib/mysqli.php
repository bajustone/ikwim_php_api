<?php
// $MsqlConn = new mysqli("localhost", "rustuser", "rustuserpass", "db_ikwim");
// $MsqlConn = new mysqli("23.229.233.105", "rustuser", "rustuserpass", "db_ikwim");

class PDOHelper {
    // const DSN ='mysql:dbname=db_ikwim;host=localhost';//41.138.85.121:4407
    const DSN ='mysql:dbname=db_ikwim;host=23.229.233.105';//41.138.85.121:4407

  /**
  * @var USERNAME
  */
  const USERNAME = 'rustuser';

  /**
  * @var PASSWORD
  */
  const PASSWORD = 'rustuserpass';//

  /**
  * Returns a PDO singleton
  *
  * @return PDO
  */
  public static function getInstance(){
    static $dbh;
    if(!isset($dbh))
      $dbh = new PDO(self::DSN, self::USERNAME, self::PASSWORD);
    return $dbh;
  }
  public static function get_result($query, $queryValue=array()){
    $db = self::getInstance();
    $sh=$db->prepare($query);
    $result=array();
    if($sh->execute($queryValue)){
      while ($row=$sh->fetch(PDO::FETCH_ASSOC)) {
        $result[]=$row;
      }
      return $result;//$sh->fetch(PDO::FETCH_ASSOC);
    }else {
      return false;
    }
  }
  public static function updateTable($tableName, $v=array(), $where ){
    $db = self::getInstance();

    if(count($where) < 1 ){
      return array(
        'status' => false,
        'error' => "update query must have a complete where clause",
      );
    }
    
    
    $setPart = "";
    $wherePart = "";
    foreach ($v as $key => $value) {
      $setPart = $setPart . " " . $key . " = :$key,";
      $columsValues[$key] = $value;
    }
    foreach ($where as $key => $value) {
      $wherePart = $wherePart . " $key=:$key AND ";
      $columsValues[$key] = $value;
    }
  
    $updateQuery = "UPDATE " . $tableName . " SET " . rtrim($setPart, ',') . " WHERE " .  rtrim($wherePart, 'AND ') ;
    $sh = $db->prepare($updateQuery);
    if($sh->execute($columsValues)){
      return  array(
        'status' => true,
        'error' => $sh->errorInfo(),
        'qerry' => $updateQuery,
        'param' => $columsValues
      );
    }else {
      $error = $sh->errorInfo();
      $message = "";
      foreach ($sh->errorInfo() as $key => $value) {
        $message = $key . " : " . $value . ", ";
      }
      error_log($message);
      return  array(
        'error' => $error,
        'qerry' => $updateQuery,
        'param' => $columsValues
      );
    }
  }
}
