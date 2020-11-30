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

if($_GET['do']=='download'){
    $rows=all('students');
    $file=fopen('donwload.csv',"w+");
    foreach($rows as $row){
        $line=implode(",",array_values($row));
        fwrite($file,$line);
        echo $line." 已寫入檔案<br>";
    }
    fclose($file);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>文字檔案匯出</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1 class="header">文字檔案匯出練習</h1>
<!----讀出匯入完成的資料----->


<table class="table text-center">
    <tr>
        <th>姓名</th>
        <th>年齡</th>
        <th>生日</th>
        <th>居住地</th>
    </tr>
    <?php

foreach($rows as $row){

?>

    <tr>
        <td><?=$row['name'];?></td>
        <td><?=$row['age'];?></td>
        <td><?=$row['birth'];?></td>
        <td><?=$row['addr'];?></td>
    </tr>
<?php
}
?>
</table>

</body>
</html>