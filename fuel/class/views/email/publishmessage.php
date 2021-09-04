<?php 
if ($luna != ""){
	echo "Hôm nay ngày " . $luna;
}

if ($message != ""){
	echo "<br><br>Lời nhắn đã được thiết lập từ trước:<br><br>" . $message;
}
else{
	echo "<br><br>Đây là email thiết lập từ trước được gởi từ hệ thống.";
}

echo '<br><br>Bạn có muốn lập lại lời nhắc này trong thời gian nào hãy chọn đường link bên dưới: ' .
'<a href="https://tthlan.info/publishDate/expand?email=' . $email . '&token=' . $token . '&expandMode=minute&expandValue=1">05 phút nữa</a> | ' .
'<a href="https://tthlan.info/publishDate/expand?email=' . $email . '&token=' . $token . '&expandMode=hour&expandValue=12">12 giờ nữa</a> | ' .
'<a href="https://tthlan.info/publishDate/expand?email=' . $email . '&token=' . $token . '&expandMode=day&expandValue=7">07 ngày giờ nữa</a> | ' .
'<a href="https://tthlan.info/publishDate/expand?email=' . $email . '&token=' . $token . '&expandMode=month&expandValue=1">01 tháng giờ nữa</a>';

echo "<br><br>Cám ơn bạn đã sử dụng";
?>