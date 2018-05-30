<?php
/* Đổi chuổi có dấu thành không dấu */
function stripUnicode($str){ 
  if(!$str) return false; 
  $unicode = array( 
   'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ', 
   'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ', 
   'd'=>'đ', 
   'D'=>'Đ', 
   'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ', 
   'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ', 
   'i'=>'í|ì|ỉ|ĩ|ị',       
   'I'=>'Í|Ì|Ỉ|Ĩ|Ị', 
   'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ', 
   'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ', 
   'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự', 
   'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự', 
   'y'=>'ý|ỳ|ỷ|ỹ|ỵ', 
   'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ' 
 ); 
  foreach($unicode as $khongdau=>$codau) { 
    $arr=explode("|",$codau); 
    $str = str_replace($arr,$khongdau,$str); 
  } 
  return $str; 
} 
/*Đổi chuổi sang keywords*/
function changeTitle($str){ 
  $str=trim($str); 
  if ($str=="") return ""; 
  $str =str_replace('"','',$str); 
  $str =str_replace("'",'',$str); 
  $str = stripUnicode($str); 
  $str = mb_convert_case($str,MB_CASE_TITLE,'utf-8'); 

    // MB_CASE_UPPER / MB_CASE_TITLE / MB_CASE_LOWER 
  $str = str_replace(' ','-',$str); 
  return $str; 
} 
/*Hàm đổi màu từ khoá tìm kiếm*/
function keywordsColor($str,$keywords){
  return str_replace($keywords,"<span style='background-color:yellow;'>$keywords</span>",$str);
}
/*Tìm thứ */
function dayOfWeeksVN($lang, $day) {
  $day_name = '';
  if($lang == 'vi') {
    switch($day) {
      case 0:
      $day_name = 'Chủ nhật';
      break;
      case 1:
      $day_name = 'Thứ hai';
      break;
      case 2:
      $day_name = "Thứ ba";
      break;
      case 3:
      $day_name = "Thứ tư";
      break;
      case 4:
      $day_name = "Thứ năm";
      break;
      case 5:
      $day_name = "Thứ sáu";
      break;
      case 6:
      $day_name = "Thứ bảy";
      break;
    }
  }
  else if($lang == 'en') {
    switch($day) {
      case 0 :
      $day_name = 'Sunday';
      break;
      case 1:
      $day_name = 'Monday';
      break;
      case 2:
      $day_name = 'Tuesday';
      break;
      case 3:
      $day_name = 'Wednesday';
      break;
      case 4:
      $day_name = 'Thursday';
      break;
      case 5:
      $day_name = 'Friday';
      break;
      case 6:
      $day_name = 'Saturday';
      break;
    }
  }
  return $day_name;
}