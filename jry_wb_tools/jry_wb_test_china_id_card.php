<?php
function jry_wb_test_china_id_card($idcard) 
{
	$idcard=jry_wb_get_18_by_china_id_card_15($idcard);
	return jry_wb_test_china_id_card_18($idcard); 
} 
function jry_wb_get_18_by_china_id_card_15($idcard) 
{
	if(strlen($idcard)==15)
	{		
		if(array_search(substr($idcard,12,3),array('996','997','998','999'))!==false)
			$idcard=substr($idcard,0,6).'18'.substr($idcard, 6, 9); 
		else
			$idcard=substr($idcard,0,6).'19'.substr($idcard, 6, 9); 
		$idcard=$idcard.jry_wb_get_china_id_card_code($idcard); 
	}
	return $idcard;
}
function jry_wb_get_birthday_by_china_id_card($idcard) 
{ 
	$idcard=jry_wb_get_18_by_china_id_card_15($idcard);
	if(jry_wb_test_china_id_card($idcard)===false)
		return false;	
	return substr($idcard,6,4).'-'.substr($idcard,10,2).'-'.substr($idcard,12,2);
} 
function jry_wb_get_sex_by_china_id_card($idcard)
{
	$idcard=jry_wb_get_18_by_china_id_card_15($idcard);
	if(jry_wb_test_china_id_card($idcard)===false)
		return false;
	if(substr($idcard,14,3)%2==0)
		return 0;
	return 1;
}
function jry_wb_get_china_id_card_code($idcard_base) 
{ 
	if(strlen($idcard_base)!=17) 
		return false; 
	$factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
	$verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
	$checksum = 0; 
	for ($i = 0; $i < strlen($idcard_base); $i++) 
		$checksum+=substr($idcard_base, $i, 1) * $factor[$i]; 
	$mod=$checksum%11; 
	$verify_number=$verify_number_list[$mod]; 
	return $verify_number; 
}
function jry_wb_test_china_id_card_15($idcard)
{
	if (strlen($idcard) != 15) 
		return false;  
	if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false)
		$idcard = substr($idcard, 0, 6) . '18'. substr($idcard, 6, 9); 
	else
		$idcard = substr($idcard, 0, 6) . '19'. substr($idcard, 6, 9); 
	$idcard=$idcard.jry_wb_get_china_id_card_code($idcard); 
	return jry_wb_test_china_id_card_18($idcard); 
} 
function jry_wb_test_china_id_card_18($idcard)
{
	if(strlen($idcard)!=18)
		return false;
	$idcard_base=substr($idcard, 0, 17);
	$factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
	$verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
	$checksum = 0; 
	for ($i = 0; $i < strlen($idcard_base); $i++) 
		$checksum+=substr($idcard_base, $i, 1) * $factor[$i]; 
	$mod=$checksum%11; 
	$verify_number=$verify_number_list[$mod]; 
	if($verify_number!=strtoupper(substr($idcard, 17, 1)))
		return false; 
	else
		return true; 
} 