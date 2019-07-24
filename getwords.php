<?php 
include_once __DIR__.'../includes/Helper.php';
$arr = select('SELECT * FROM `tbl_words`');
$data = [];
$data['correct'] = [];
$data['wrong'] = [];
foreach($arr as $key => $value){
   if($value['status'] == 1){
       array_push($data['correct'],$value['word']);
    }else{
       array_push($data['wrong'],$value['word']);
   }
}

header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
die;
?>