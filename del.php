<?php
include_once "base.php";

$id=$_GET['id'];

$row=find('upload',$id);
$path=$row['path'];
unlink($path);
// 去查unlink 的定義


del('upload',$id);

to('manage.php');

?>