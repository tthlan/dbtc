<?php 
if (isset($luna)){
echo "Hôm nay ngày " . $luna;
}

if (isset($message)){
echo "Lời nhắn đã được thiết lập từ trước: " . $message;
}
else{
	echo "Đây là email thiết lập từ trước được gởi từ hệ thống.";
}
echo "Cám ơn bạn đã sử dụng";
?>