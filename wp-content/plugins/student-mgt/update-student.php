<?php 
if(current_user_can('administrator'))
{
	$_user_id = $_POST["i"];
	$_user_key = $_POST["k"];
	$_user_val = $_POST["v"];

	echo update_user($_user_id,$_user_key,$_user_val);
}
function update_user($id,$key,$val){
		return wp_update_user( array( 'ID' => $user_id, $key => $val ) );
}