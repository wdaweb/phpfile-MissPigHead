<?php
/****
 * 1.建立資料庫及資料表
 * 2.建立上傳圖案機制
 * 3.取得圖檔資源
 * 4.進行圖形處理
 *   ->圖形縮放
 *   ->圖形加邊框
 *   ->圖形驗證碼
 * 5.輸出檔案
 */
include_once ("base.php");

if(!empty($_FILES['photo']['tmp_name'])){  
    echo "檔名：".$_FILES['photo']['name'];
    echo "<br>";
    echo "格式：".$_FILES['photo']['type'];
    echo "<br>";
    echo "大小：".round($_FILES['photo']['size']/1024)."kb";
    echo "<br>";
    move_uploaded_file($_FILES['photo']['tmp_name'],"./uploadFiles/".$_FILES['photo']['name']);


    $filename="./uploadFiles/".$_FILES['photo']['name'];
    // $src_img=[
    //     'width'=>0,
    //     'height'=>0,
    // ];
    // $dst_img=[
    //     'width'=>0,
    //     'height'=>0,
    // ];
    
    switch ($_FILES['photo']['type']){
        case "image/jpeg":
            $src_img=imagecreatefromjpeg($filename);
        break;
        case "image/gif":
            $src_img=imagecreatefromgif($filename);
        break;
        case "image/png":
            $src_img=imagecreatefrompng($filename);
        break;
        default:
        echo "只接受圖檔";
        break;
        // exit();  // 為什麼老師這裡用break?
    }
    
    // $src_img['width']=imagesx($src_img);
    // $src_img['height']=imagesy($src_img);
    
    // $src_ratio=$src_img['width']/$src_img['height'];
    $dst_img=imagecreatetruecolor(300,300);
    $frame_w=300;
    $frame_h=300;
    $dst_w=$frame_w;
    $dst_h=$frame_h;
    $dst_x=0;
    $dst_y=0;
    $src_ratio=imagesx($src_img)/imagesy($src_img);
    if($src_ratio>1){
        $dst_h=$dst_w/$src_ratio;
        $dst_y=($dst_w-$dst_h)/2;
    }elseif($src_ratio<1){
        $dst_w=$dst_h*$src_ratio;
        $dst_x=($dst_h-$dst_w)/2;
    }

    // 這兩行加入  等比例  背景穿幫的顏色
    $bgcolor=imagecolorallocate($dst_img, 255, 230, 240);
    imagefill($dst_img,0,0,$bgcolor);

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>圖形處理練習</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- 驗證碼 函數   自定義字數 -->
<!-- 驗證碼 轉 圖形驗證碼函數   自定義字串 -->
<?php

  function captcha($length){
    for($i=0;$i<$length;$i++){
      $n=rand(48,122);
      if((57<$n && $n<65)||(90<$n && $n<97)){
        do{$n=rand(48,122);}while((57<$n && $n<65)||(90<$n && $n<97));
      }
      $captcha[]=chr($n);
    }
    $captcha=implode("",$captcha);
    return $captcha;
  } // 產生亂數驗證碼

  function captchaImg($capStr,$width,$height,$lineNum,$fontSize){
    $base_img=imagecreatetruecolor($width,$height);
    $color=imagecolorallocate($base_img,rand(200,250),rand(200,250),rand(200,250));
    imagefill($base_img,0,0,$color);  

    $fontList=['arial.ttf','arialbd.ttf','arialbi.ttf','comic.ttf','comicbd.ttf','comicz.ttf','comici.ttf','CooperBlackStd-Italic.otf','CooperBlackStd.otf'];
    $fontPath=realpath("./font/{$fontList[rand(0,7)]}");
    $fontcolor=imagecolorallocate($base_img,rand(20,100),rand(20,100),rand(20,100));
    $angle=(30-rand(0,60)); // 正負30度

    $wSpace=$width/(strlen($capStr)+2);
    $y3=($height+$fontSize)/2;

    for($i=0;$i<strlen($capStr);$i++){
      $captcha[$i]=mb_substr($capStr,$i,1);
      imagettftext($base_img,$fontSize,$angle,($wSpace*($i+2)-($fontSize/2)),$y3,$fontcolor,$fontPath,$captcha[$i]);
    } 


    $linecolor=imagecolorallocate($base_img,rand(160,180),rand(160,180),rand(160,180));
    $numLine=rand(1,$lineNum); // 線條數
    $padding=$width*10/100; // 線條內縮範圍：寬度0~10%
    for($i=0;$i<$numLine;$i++){ 
      $x1=rand(0,$padding);
      $x2=rand(($width-$padding),$width);
      $y1=rand(0,$height);
      $y2=rand(0,$height);
      imageline($base_img,$x1,$y1,$x2,$y2,$linecolor);
    }  // 畫斜橫向直線
    
    
    $dst_path="./capImg/cap_Img.png";
    imagepng($base_img,$dst_path);

    return $dst_path;
  } // 產生圖形驗證碼，字串,圖寬,圖長,線條數,字大小 注意路徑檔名！！
?>



<h2>圖形驗證碼</h2>
<hr>
<?php
$capTT=captcha(6);
$imgTT=captchaImg($capTT,180,40,8,22);
session_start();
print_r($capTT);

if(isset($_POST['ans'])){
  echo "新的的驗證碼內容為:".$capTT;
  echo "<br>";
  echo "你輸入的驗證碼為:".$_POST['ans'];
  echo "<br>";
  echo "原始的驗證碼內容為:".$_POST['src'];
  echo "<br>";
  echo "session內容為:".$_SESSION['ans'];
  if($_POST['ans']==$_SESSION['ans']){  // == 需要大小寫相同
      echo "你輸入的驗證碼正確";
  }else{        
      echo "你輸入的驗證碼錯誤";
  }
}

$_SESSION['ans']=$capTT;

?>
<form action="?" method="post">
    <?="<img src='$imgTT'>";?>
    <input type="text" name="ans" >
    <input type="hidden" name="src" value="<?=$capTT;?>">
    <input type="submit" value="送出">
</form><h3>驗證碼</h3>
<br>








<?php  // 圖形驗證碼函數

  function captchaImg2($width,$height,$lineNum,$capLen,$fontSize){
    $base_img=imagecreatetruecolor($width,$height);
    $color=imagecolorallocate($base_img,rand(200,250),rand(200,250),rand(200,250));
    imagefill($base_img,0,0,$color);  
    
    
    $linecolor=imagecolorallocate($base_img,rand(160,180),rand(160,180),rand(160,180));
    $numLine=rand(1,$lineNum); // 線條數
    $padding=$width*10/100; // 線條內縮範圍：寬度0~10%
    for($i=0;$i<$numLine;$i++){ 
      $x1=rand(0,$padding);
      $x2=rand(($width-$padding),$width);
      $y1=rand(0,$height);
      $y2=rand(0,$height);
      imageline($base_img,$x1,$y1,$x2,$y2,$linecolor);
    }  // 畫斜橫向直線

    $fontList=['arial.ttf','arialbd.ttf','arialbi.ttf','comic.ttf','comicbd.ttf','comicz.ttf','comici.ttf','CooperBlackStd-Italic.otf','CooperBlackStd.otf'];
    $fontPath=realpath("./font/{$fontList[rand(0,7)]}");
    $fontcolor=imagecolorallocate($base_img,rand(20,100),rand(20,100),rand(20,100));

    $Space=($width-$fontSize)/($capLen+2);
    $y3=($height+$fontSize)/2;
    $angle=rand(0,30);
    for($i=0;$i<$capLen;$i++){
      $nCap=rand(48,122);
      if((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97)){
        do{$nCap=rand(48,122);}while((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97));
      }
      $captcha[]=chr($nCap);
      imagettftext($base_img,$fontSize,0,($Space*($i+2)-($fontSize/2)),$y3,$fontcolor,$fontPath,$captcha[$i]);
    }  // 將亂數字元加入圖片中
    $dst_path="./capImg/capImg{$width}{$height}{$lineNum}{$capLen}{$fontSize}.png";
    imagepng($base_img,$dst_path);
  } // 產生圖形驗證碼，圖寬,圖長,線條數,字串長,字大小

  captchaImg2(180,40,8,6,22);

?>

<div class="divcen">
  <h4>圖形驗證碼</h4>
  <br>
  <img src="./capImg/capImg180408622.png">
</div>
<hr>
<br>



<hr>
<?php



$width=200;
$height=50;
$capLen=6;
$base_img=imagecreatetruecolor($width,$height);
$color=imagecolorallocate($base_img,rand(200,250),rand(200,250),rand(200,250));
imagefill($base_img,0,0,$color); 

$linecolor=imagecolorallocate($base_img,rand(160,180),rand(160,180),rand(160,180));


$numLine=rand(1,8); // 線條數
$padding=$width*15/100; // 線條內縮範圍：寬度0~15%
for($i=0;$i<$numLine;$i++){ 
  $x1=rand(0,$padding);
  $x2=rand(($width-$padding),$width);
  $y1=rand(0,$height);
  $y2=rand(0,$height);
  imageline($base_img,$x1,$y1,$x2,$y2,$linecolor);
}  // 畫斜橫向直線

$fontlist=['arial.ttf','arialbd.ttf','arialbi.ttf','comic.ttf','comicbd.ttf','comicz.ttf','comici.ttf','CooperBlackStd-Italic.otf','CooperBlackStd.otf'];
$fontpath=realpath("./font/{$fontlist[rand(0,7)]}");
$fontsize=20;
$fontcolor=imagecolorallocate($base_img,rand(20,100),rand(20,100),rand(20,100));


$Space=($width-$fontsize)/($capLen+2);
$y3=($height+$fontsize)/2;
for($i=0;$i<$capLen;$i++){
  $nCap=rand(48,122);
  if((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97)){
    do{$nCap=rand(48,122);}while((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97));
  }
  $captcha[]=chr($nCap);
  imagettftext($base_img,$fontsize,0,($Space*($i+2)-($fontsize/2)),$y3,$fontcolor,$fontpath,$captcha[$i]);
}  // 將亂數字元加入圖片中





// imagecopyresampled($img_bor,$src_img,0,0,0,0,$bor_w,$bor_h,imagesx($src_img),imagesy($src_img));
$dst_path="./capImg/base_img.png";
imagepng($base_img,$dst_path);
?>
<br>

<div class="divcen">
    <img src="<?=$dst_path;?>">
</div>
<hr>



<?php


function captchaImg1($width,$height,$lineNum,$capLen,$fontSize){
  $base_img=imagecreatetruecolor($width,$height);
  $color=imagecolorallocate($base_img,rand(200,250),rand(200,250),rand(200,250));
  imagefill($base_img,0,0,$color);  
  
  
  $linecolor=imagecolorallocate($base_img,rand(160,180),rand(160,180),rand(160,180));
  $numLine=rand(1,$lineNum); // 線條數
  $padding=$width*10/100; // 線條內縮範圍：寬度0~10%
  for($i=0;$i<$numLine;$i++){ 
    $x1=rand(0,$padding);
    $x2=rand(($width-$padding),$width);
    $y1=rand(0,$height);
    $y2=rand(0,$height);
    imageline($base_img,$x1,$y1,$x2,$y2,$linecolor);
  }  // 畫斜橫向直線

  $fontList=['comic.ttf','comicbd.ttf','comicz.ttf'];
  $fontPath=realpath("./font/{$fontList[rand(0,2)]}");
  $fontcolor=imagecolorallocate($base_img,rand(20,100),rand(20,100),rand(20,100));

  $Space=($width-$fontSize)/($capLen+2);
  $y3=($height+$fontSize)/2;
  for($i=0;$i<$capLen;$i++){
    $nCap=rand(48,122);
    if((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97)){
      do{$nCap=rand(48,122);}while((57<$nCap && $nCap<65)||(90<$nCap && $nCap<97));
    }
    $captcha[]=chr($nCap);
    imagettftext($base_img,$fontSize,0,($Space*($i+2)-($fontSize/2)),$y3,$fontcolor,$fontPath,$captcha[$i]);
  }  // 將亂數字元加入圖片中
  $dst_path="./capImg/".strtotime("now").".png";
  imagepng($base_img,$dst_path);
} // 產生圖形驗證碼，圖寬,圖長,線條數,字串長,字大小

captchaImg1(200,50,8,8,24);

?>






<hr>
<div class="divcen">
    <img src="./capImg/1606967040.png">
</div>

<div class="divcen">
    <img src="./capImg/1606967076.png">
</div>

<div class="divcen">
  <img src="./capImg/1606967087.png">
</div>

<div class="divcen">
  <img src="./capImg/1606967247.png">
</div>

<div class="divcen">
  <img src="./capImg/1606968032.png">
</div>

<div class="divcen">
  <img src="./capImg/1606968039.png">
</div>

<div class="divcen">
  <img src="./capImg/1606968050.png">
</div>

</body>
</html>