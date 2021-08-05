<?php

class PreviewProvider
{

  private $con;
  private $username;

  public function __construct($con,$username)
  {
    $this->con = $con;
    $this->username = $username;
  }

  public function createPreviewVideo($entity)
  {
    if($entity == null){
      $entity = $this->getRandomEntity();
    }

    $id = $entity->getId();
    $name = $entity->getName();
    $preview = $entity->getPreview();
    $thumbnail = $entity->getThumbnail();

    echo
    "<div class='previewContainer'>
      <img src='$thumbnail' class='previewImage' hidden>
      <video autoplay muted class='previewVideo' onended='previewEnded()'>
        <source src='$preview' type='video/mp4'>
      </video>
      <div class='previewOverlay'>
        <div class='mainDetails'>
          <h3>$name</h3>
          <div class='buttons'>
            <button><i class='fas fa-play'></i> Play</button>
            <button onclick='volumeToggle(this)'><i class='fas fa-volume-mute'></i></button>
          </div>
        </div>
      </div>
    </div>";
    // echo $name;
  }

  private function getRandomEntity()
  {
    // entitiesテーブルからランダムに1つデータを取得する
    $query = $this->con->prepare("SELECT * FROM entities ORDER BY RAND() LIMIT 1");
    $query->execute();

    // 連想配列としてデータを格納する
    $row = $query->fetch(PDO::FETCH_ASSOC);
    // echo $row["name"];

    return new Entity($this->con,$row);
  }

}

?>