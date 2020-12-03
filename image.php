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
<h1 class="header">圖形處理練習</h1>
<!---建立檔案上傳機制--->
<form action="?" method="post" enctype="multipart/form-data">
    <input type="file" name="photo">
    <input type="submit" value="Submit">
</form>
<hr>

<!----原始圖形----->
<h3>原始圖形</h3>
<br>

<div class="divcen">
    <img src="<?="./uploadFiles/".$_FILES['photo']['name'];?>" alt="" width="500px">
</div>
<hr>

<!----縮放圖形----->

<h3>縮放圖形</h3>
<br>

<?php
if(isset($src_img) && isset($dst_img)){
    imagecopyresampled($dst_img,$src_img,$dst_x,$dst_y,0,0,$dst_w,$dst_h,imagesx($src_img),imagesy($src_img));
    imagejpeg($dst_img,"./dst/".$_FILES['photo']['name']);
}
?>
<div class="divcen">
    <img src="<?="./dst/".$_FILES['photo']['name'];?>">
</div>
<hr>


<!----圖形加邊框----->

<h3>圖形加邊框</h3>
<br>
<?php
$border=5;
$padding=10;

$bor_w=($frame_w-($padding+$border)*2);
$bor_h=($frame_h-($padding+$border)*2);


if($src_ratio>1){
    $bor_h=($dst_w-($padding+$border)*2)/$src_ratio;
}elseif($src_ratio<1){
    $bor_w=($dst_h-$padding*2)*$src_ratio;
}



$img_bor=imagecreatetruecolor($bor_w,$bor_h);
imagecopyresampled($img_bor,$src_img,0,0,0,0,$bor_w,$bor_h,imagesx($src_img),imagesy($src_img));
imagejpeg($img_bor,"./dst/bor_".$_FILES['photo']['name']);
?>
<div class="divcen">
    <img src="<?="./dst/bor_".$_FILES['photo']['name'];?>" style="border: 5px solid #f00; padding:10px;">
</div>
<hr>



<!----產生圖形驗證碼----->

<h3>圖形加邊框</h3>
<br>
<hr>
H2y66

<?php

$num=4; // 定義驗證碼字數

$captcha[]='';

for($i=0;$i<$num;$i++){
    $n=rand(48,122);
    if((57<$n && $n<65)||(90<$n && $n<97)){
        do{$n=rand(48,122);}while((57<$n && $n<65)||(90<$n && $n<97));
    }
    $captcha[]=$n;
}
$captcha=implode("",$captcha);








?>


</body>
</html>