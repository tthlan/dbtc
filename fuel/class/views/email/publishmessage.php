<?php 
if (isset($luna)){
	echo "Hôm nay ngày " . $luna;
}

if (isset($message)){
	echo "<br><br>Lời nhắn đã được thiết lập từ trước:<br><br>" . $message;
}
else{
	echo "<br><br>Đây là email thiết lập từ trước được gởi từ hệ thống.";
}

echo "<br><br>Cám ơn bạn đã sử dụng";
?>