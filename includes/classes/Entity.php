<?php

class Entity {

  private $con,$sqlData;

  public function __construct($con,$input)
  {
    $this->con = $con;

    // is_array = 配列かどうかの確認
    if(is_array($input)){
      $this->sqlData = $input;
    }else{
      // URLに入力されているid番号とentitiesテーブルのIDが一致しているデータの取得
      $query = $this->con->prepare("SELECT * FROM entities WHERE id=:id");
      $query->bindValue(":id",$input);
      $query->execute();

      $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }
  }

  public function getId()
  {
    return $this->sqlData['id'];
  }

  public function getName()
  {
    return $this->sqlData['name'];
  }

  public function getThumbnail()
  {
    return $this->sqlData['thumbnail'];
  }

  public function getPreview()
  {
    return $this->sqlData['preview'];
  }

  public function getCategoryId()
  {
    return $this->sqlData['categoryId'];
  }

  public function getSeasons()
  {
    // seasonとepisodeを昇順に並び替えて取得する
    $query = $this->con->prepare("SELECT * FROM videos WHERE entityId=:id
    AND isMovie=0 ORDER BY season,episode ASC");

    $query->bindValue(":id", $this->getId());
    $query->execute();

    $seasons = array();
    $videos = array();
    $currentSeason = null;

    // FETCH_ASSOC → データを連想配列で取得する
    while($row = $query->fetch(PDO::FETCH_ASSOC)){

      if($currentSeason != null && $currentSeason != $row["season"]){
        $seasons[] = new Season($currentSeason, $videos);
        $videos = array();
      }

      $currentSeason = $row["season"];
      $videos[] = new Video($this->con, $row);
    }

    // sizeof → 配列の要素数を調べる
    if(sizeof($videos) != 0){
      $seasons[] = new Season($currentSeason, $videos);
    }

    return $seasons;
  }

}

?>