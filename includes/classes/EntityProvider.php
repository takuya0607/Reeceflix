<?php
class EntityProvider
{

  public static function getEntities($con, $categoryId, $limit)
  {

    // entitiesテーブルの全データを取得
    $sql = "SELECT * FROM entities ";

    // 第二引数の$categoryIdがnull以外だった場合の処理
    if($categoryId != null){
      $sql .= "WHERE categoryId=:categoryId ";
    }

    $sql .= "ORDER BY RAND() LIMIT :limit";

    $query = $con->prepare($sql);

    if($categoryId != null){
      $query->bindValue(":categoryId", $categoryId);
    }

    $query->bindValue(":limit", $limit, PDO::PARAM_INT);
    $query->execute();

    $result = array();
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
      $result[] = new Entity($con, $row);
    }

    return $result;
  }

}


?>