function volumeToggle(button)
{
  // prop = 属性プロパティの取得
  var muted = $(".previewVideo").prop("muted");
  $(".previewVideo").prop("muted", !muted);

  // volumeのアイコンを非表示にする
  $(button).find("i").toggleClass("fa-volume-mute");
  $(button).find("i").toggleClass("fa-volume-up");
}

function previewEnded()
{
  $(".previewVideo").toggle();
  $(".previewImage").toggle();
}