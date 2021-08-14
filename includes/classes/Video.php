<?php
class Video
{
  private $con, $sqlData, $entity;

  public function __construct($con, $input)
  {
    $this->con = $con;

    // is_array = 配列かどうかの確認
    if (is_array($input)) {
      $this->sqlData = $input;
    } else {
      $query = $this->con->prepare("SELECT * FROM videos WHERE id=:id");
      $query->bindValue(":id", $input);
      $query->execute();

      $this->sqlData = $query->fetch(PDO::FETCH_ASSOC);
    }

    $this->entity = new Entity($con, $this->sqlData["entityId"]);
  }

  public function getId()
  {
    return $this->sqlData["id"];
  }

  public function getTitle()
  {
    return $this->sqlData["title"];
  }

  public function getDescription()
  {
    return $this->sqlData["description"];
  }

  public function getFilePath()
  {
    return $this->sqlData["filePath"];
  }

  public function getEpisodeNumber()
  {
    return $this->sqlData["episode"];
  }

  public function getSeasonNumber()
  {
    return $this->sqlData["season"];
  }

  public function getEntityId()
  {
    return $this->sqlData["entityId"];
  }

  public function getThumbnail()
  {
    return $this->entity->getThumbnail();
  }

  public function incrementViews()
  {
    $query = $this->con->prepare("UPDATE videos SET views=views+1 WHERE id=:id");
    $query->bindValue(":id", $this->getId());
    $query->execute();
  }

  public function getSeasonAndEpisode()
  {
    // falseだった場合処理終了
    if($this->isMovie()){
      return;
    }

    $season = $this->getSeasonNumber();
    $episode = $this->getEpisodeNumber();

    return "Season $season, Episode $episode";
  }

  public function isMovie()
  {
    /*isMovieが1かの確認
    正常であれば0のはずなので、ここではfalseが返る*/
    return $this->sqlData["isMovie"] == 1;
  }

  // 視聴履歴があればContinue watchingを表示する処理
  public function isInProgress($username)
  {
    $query = $this->con->prepare("SELECT * FROM videoProgress
    WHERE videoId=:videoId
    AND username=:username");

    $query->bindValue(":videoId",$this->getId());
    $query->bindValue(":username",$username);
    $query->execute();

    /*0じゃなければtrueを返す
    0だったらfalseを返す*/
    return $query->rowCount() != 0;
  }

  public function hasSeen($username)
  {

    $query = $this->con->prepare("SELECT * FROM videoProgress
    WHERE videoId=:videoId
    AND username=:username
    AND finished=1");

    $query->bindValue(":videoId", $this->getId());
    $query->bindValue(":username", $username);
    $query->execute();

    /*0じゃなければtrueを返す
    0だったらfalseを返す*/
    return $query->rowCount() != 0;
    // $test = $query->rowCount();
    // var_dump($test);
  }

}


?>