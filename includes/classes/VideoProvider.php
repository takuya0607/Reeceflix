<?php

class VideoProvider
{

  public static function getUpNext($con, $currentVideo)
  {
    // videoテーブルからentityIdが一致しているデータを検索 → 関連動画のため
    $query = $con->prepare("SELECT * FROM videos WHERE entityId=:entityId
    -- 現在視聴しているvideoIdと一致しないvideoIdを検索 → 同じ動画を表示しないため
    AND id != :videoId
    -- seasonが一致していて、現在のepisodeよりも大きいepisodeを検索
    AND ((season = :season AND episode > :episode)
    -- もしくは今のseasonよりも大きいseasonを検索(そのseasonの最終話の対応)
    OR season > :season)
    -- 昇順でseasonとepisodeのデータを1件取得する
    ORDER BY season,episode ASC LIMIT 1");

    $query->bindValue(":entityId", $currentVideo->getEntityId());
    $query->bindValue(":season", $currentVideo->getSeasonNumber());
    $query->bindValue(":episode", $currentVideo->getEpisodeNumber());
    $query->bindValue(":videoId", $currentVideo->getId());

    $query->execute();

    // 1話完結ものでデータが無かった場合の処理
    if($query->rowCount() == 0){
      $query = $con->prepare("SELECT * FROM videos
      -- seasonが1以下かつepisodeが1以下
      WHERE season <=1 AND episode <=1
      -- 現在のvideoIdと一致しないid
      AND id != :videoId
      -- 視聴回数が最も多い、人気の動画を1件取得する
      ORDER BY views DESC LIMIT 1 ");

      $query->bindData(":videoId", $currentVideo->getId());
      $query->execute();
    }

    // $rowに取得したデータを連想配列で取得する
    $row = $query->fetch(PDO::FETCH_ASSOC);
    return new Video($con, $row);
  }

  public static function getEntityVideoForUser($con, $entityId, $username)
  {
    // videoProgressテーブルのvideoIdが対象
    $query = $con->prepare("SELECT videoId FROM `videoProgress`
    -- videosテーブルから、videoProgress.videoIdとvideos.idが一致したデータを取得する
    INNER JOIN videos ON videoProgress.videoId = videos.id
    -- 検索条件はvideos.entityIdとvideoProgress.usernameを使用する
    WHERE videos.entityId = :entityId
    AND videoProgress.username = :username
    -- 日付が最も新しいデータを1件取得する
    ORDER BY videoProgress.dateModified
    DESC LIMIT 1");

    $query->bindValue(":entityId", $entityId);
    $query->bindValue(":username", $username);
    $query->execute();


    // 視聴履歴のデータが無かった場合の処理
    if($query->rowCount() == 0){
      $query = $con->prepare("SELECT id FROM videos
      WHERE entityId = :entityId
      ORDER BY season,episode
      ASC LIMIT 1");

      $query->bindValue(":entityId", $entityId);
      $query->execute();
    }
    return $query->fetchColumn();
  }
}


?>