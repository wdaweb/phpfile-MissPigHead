<?php
/**
 * 1.建立表單
 * 2.建立處理檔案程式
 * 3.搬移檔案
 * 4.顯示檔案列表
 */

// $_FILES['img (form表單的name)']['tmp_name (這個是固定寫法=>暫存名)']
// $_FILES['img (form表單的name)']['name (這個是固定寫法=>原始檔名)']
// $_POST vs $_FILES 注意別搞混
include_once "base.php";

if(!empty($_FILES['img']['tmp_name'])){  
    // echo $_FILES['img']['name'];
    // echo "<br>";
    // echo "上傳成功";
    // echo "<hr>";
    // echo "暫存檔檔名".$_FILES['img']['tmp_name'];
    $originFilename=explode(".",$_FILES['img']['name']);
    $subname=array_pop($originFilename);
    $filename=date("Ymdhis").".".$subname;
    // echo "<hr>";
    // echo "原始附檔名".$subname;
    move_uploaded_file($_FILES['img']['tmp_name'],"./uploadFiles/".$filename);
    
    $row=[
        "name"=>$_FILES['img']['name'],
        "path"=>"./uploadFiles/".$filename,
        "type"=>$_POST['type'],
        "note"=>$_POST['note']
    ];

    print_r($row);
    save("upload",$row);
    to('manage.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>檔案上傳</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
 <h1 class="header">檔案上傳練習</h1>

 <!----建立你的表單及設定編碼----->
 <div class="container">
<div class="row">
 <form class="col-sm-8 col-lg-3" action="upload.php" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label class="mt-3">上傳的檔案：</label>
        <input type="file" class="form-control border-0" name="file">
        <label class="mt-3">檔案說明：</label>
        <input type="text" class="form-control" name="filedes">
        <label class="mt-3">檔案類型：</label>
        <select class="form-control" name="type">
            <option value="圖片">-圖片-</option>
            <option value="文件">-文件-</option>
            <option value="其他">-其他-</option>
        <input type="submit" class="mr-0" value="Upload">
    </div>
</form>
</div>
</div>


<!----建立一個連結來查看上傳後的圖檔---->  
<?php
// $rows=all('upload');
// echo "<table>";
// echo "<td>縮圖</td>";
// echo "<td>檔案名稱</td>";
// echo "<td>檔案類型</td>";
// echo "<td>檔案說明</td>";
// echo "<td>下載</td>";
// foreach($rows as $row){

//     echo "<tr>";

//     if($row['type']=='圖檔'){
//         echo "<td><img src='{$row['path']}' style='width:100px'></td>";
        
//     }else{
//         echo "<td><img src='./uploadFiles/file_icon.png' style='width:20px'></td>";

//     }
//     echo "<td>{$row['name']}</td>";
//     echo "<td>{$row['type']}</td>";
//     echo "<td>{$row['note']}</td>";
//     echo "<td><a href='{$row['path']}' download>下載</a></td>";

//     echo "</tr>";
// }
// echo "</table>";


?>


</body>
</html>