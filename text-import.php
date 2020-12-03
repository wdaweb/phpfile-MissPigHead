<?php
/****
 * 1.建立資料庫及資料表
 * 2.建立上傳檔案機制
 * 3.取得檔案資源
 * 4.取得檔案內容
 * 5.建立SQL語法
 * 6.寫入資料庫
 * 7.結束檔案
 */

include_once ("base.php");

if(!empty($_FILES['txt']['tmp_name'])){
    echo $_FILES['txt']['name'];
    move_uploaded_file($_FILES['txt']['tmp_name'],"./uploadFiles/".$_FILES['txt']['name']);

    $file=fopen("./uploadFiles/".$_FILES['txt']['name'],'r');
    $num=0;
    while(!feof($file)){
        $line=fgets($file); // 此時$line 是字串
        if($num!=0){
            $line=explode(",",$line); // 此時$line 已轉成陣列
            $data=[
                'name'=>$line[1],
                'age'=>$line[2],
                'birth'=>$line[3],
                'addr'=>$line[4]
            ];
            save('students',$data);
        }
        $num++;
    }


    /* fgets()此函數：一次讀出一行資料
    對照 fread()函數：一次可讀出整個檔案，可指定要讀入多少“bytes” */


}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>文字檔案匯入</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1 class="header">文字檔案匯入練習</h1>
<!---建立檔案上傳機制--->
<div class="container">
    <div class="row">
        <form class="col-sm-8 col-lg-3" action="text-import.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label class="mt-3">上傳的檔案：</label>
                <input type="file" class="form-control border-0" name="txt">
                <input type="submit" class="mr-0" value="Upload">
            </div>
        </form>
    </div>
</div>

<!----讀出匯入完成的資料----->



</body>
</html>