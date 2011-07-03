<?php
$listFile = "sample.lst";
$scriptFile = "sample.sql";

$fp = fopen($listFile,"r");
$fp = fopen($scriptFile,"w");

/*
 * 0 TARGET_TABLE
 * 1 JOIN1_COND_TABLE
 * 2 JOIN1_COND_COLUMN
 * 3 JOIN1_TARGET_COLUMN
 * 4 JOIN2_COND_TABLE
 * 5 JOIN2_COND_COLUMN
 * 6 JOIN2_TARGET_COLUMN
 * 7 OTHER_COND
 */
$data = array();
$cols = array();
$fp = fopen($listFile,"r");
while($line = fgets($fp,4000)){
  $cols = explode(",",$line);
  $data[] = array(
    'TARGET_TABLE' => $cols[0],
    'JOIN1_COND_TABLE' => $cols[1],
    'JOIN1_COND_COLUMN' => $cols[2],
    'JOIN1_TARGET_COLUMN' => $cols[3],
    'JOIN2_COND_TABLE' => $cols[4],
    'JOIN2_COND_COLUMN' => $cols[5],
    'JOIN2_TARGET_COLUMN' => $cols[6],
    'OTHER_COND' => $cols[7]
    );
}
fclose($fp);

/*
 * SQLスクリプトファイル書き込み
 */
$sql_no_join = "";
$sql_join1 = "";
$sql_join1and2 = "";
$fp = fopen($scriptFile,"w");
foreach($data as $items){
/*
  echo $items['TARGET_TABLE'];
  echo "\t".$items['JOIN1_COND_TABLE'];
  echo "\t".$items['JOIN1_COND_COLUMN'];
  echo "\t".$items['JOIN1_TARGET_COLUMN'];
  echo "\t".$items['JOIN2_COND_TABLE'];
  echo "\t".$items['JOIN2_COND_COLUMN'];
  echo "\t".$items['JOIN2_TARGET_COLUMN'];
  echo "\t".$items['OTHER_COND']."\n";
 */

  // JOINが無い場合
  if($items['JOIN1_COND_TABLE'] == ""){
    $sql_no_join .= "DELETE FROM ".$items['TARGET_TABLE']." T ";
    $sql_no_join .= "WHERE T.DELETE_ID = delete_id";
    
    // その他条件の追加
    if($items['OTHER_COND'] != ""){
      $sql_no_join .= " AND ".$items['OTHER_COND'].";\n"; 
    }else{
      $sql_no_join .= ";\n";
    } 
  }

  // JOIN1のみの場合
  if($items['JOIN1_COND_TABLE'] != "" && $items['JOIN2_COND_TABLE'] == ""){
    $sql_join1 .= "DELETE FROM ".$items['TARGET_TABLE']." T";
    $sql_join1 .= " WHERE EXISTS ( SELECT * FROM ".$items['JOIN1_COND_TABLE']." J1";
    $sql_join1 .= " WHERE J1".$items['JOIN1_COND_COLUMN']." = T.".$items['JOIN1_TARGET_COLUMN'];
    $sql_join1 .= " AND J1.DELETE_ID = delete_id )";

    // その他条件の追加
    if($items['OTHER_COND'] != ""){
      $sql_join1 .= " WHERE ".$items['OTHER_COND'].";\n"; 
    }else{ 
      $sql_join1 .= ";\n";
    } 
  }
 
  // JOIN1、JOIN2がある場合
  if($items['JOIN1_COND_TABLE'] == "" && $items['JOIN2_COND_TABLE'] == ""){
    $sql_join1and2 .= "DELETE FROM ".$items['TARGET_TABLE']." T";
    $sql_join1and2 .= " WHERE EXISTS ( SELECT * FROM ".$items['JOIN1_COND_TABLE']." J1";
    $sql_join1and2 .= " WHERE EXISTS ( SELECT * FROM ".$items['JOIN2_COND_TABLE']." J2";
    $sql_join1and2 .= " WHERE J2.".$items['JOIN2_COND_COLUMN']." = J1.".$items['JOIN2_TARGET_COLUMN'];
    $sql_join1and2 .= " AND J1.DELETE_ID = delete_id  )";
    $sql_join1and2 .= " AND J1".$items['JOIN1_COND_COLUMN']." = T.".$items['JOIN1_TARGET_COLUMN'].")";

    // その他条件の追加
    if($items['OTHER_COND'] != ""){
      $sql_join1and2 .= " WHERE ".$items['OTHER_COND'].";\n"; 
    }else{ 
      $sql_join1and2 .= ";\n";
    } 
  }
}

fwrite($fp,$sql_join1and2);
fwrite($fp,$sql_join1);
fwrite($fp,$sql_no_join);
fclose($fp);

?>
