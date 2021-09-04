<?php

class Controller_PublishDate extends Controller_Rest
{
	protected $format = 'json';

	public function get_list()
	{
		return $this->response(array(
            'foo' => Input::get('foo'),
            'baz' => array(
                1, 50, 219
            ),
            'empty' => null
        ));
	}

	public function post_register()
	{
		date_default_timezone_set('Asia/Ho_Chi_Minh');
        $email = isset($_POST["email"]) ? $_POST["email"] : null; //"hlanart @gmail.com";

		if (!isset($email)){
			$data['status'] = 'fase';
			$data['response']['message'] = 'Vui lòng nhập email';

			return $this->response($data);
		}

		if (isset($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$data['status'] = 'fase';
			$data['response']['message'] = 'Không đúng định dạng Email';

			return $this->response($data);
		}


		if ($this->checkInMininutes($email) === false){

			$data['status'] = 'false';
			$data['response']['message'] = "Chỉ tạo mã sau 1 phút"; // Only generate token after 1 minute
			return $this->response($data);
		}

		$cDate = date('Y-m-d H:i:s');
		$token = substr(md5($email . $cDate),0, 8);

		$result = Model_Publishdate::forge()->set(
			array(
                'email' => $email,
				'status' => Model_Publishdate::STATUS_NEW,
                'registered_date' => $cDate,
				'token' => $token
            )
		);

		if ($result->save()){
			$dataMailing = array('email' => $email, 'token' => $token);

			if ($this->token_mailing($dataMailing)){
				$result->status = Model_Publishdate::STATUS_AUTHENTICATED;
				$result->save();
			};

			$data['status'] = 'true';
			$data['response']['data'] = $result;

			return $this->response($data);
		}
		else{
			$data['status'] = 'fase';
			$data['response']['message'] = 'Xử lý bị lỗi'; // Fail Progress

			return $this->response($data);
		}
	}

	protected function checkInMininutes($email)
	{
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$cDate = date('Y-m-d H:i:s');

		$dataSet = Model_Publishdate::find(
			array(
				'select' => array('registered_date'),
				'where' => DB::expr(
					"(status = " . Model_Publishdate::STATUS_CREATED . " OR status = " . Model_Publishdate::STATUS_AUTHENTICATED . ")"),
				'order_by' =>  array('id' => 'desc'),
				'limit' => array('1')
			)
		);

		if (isset($dataSet)){
			$lastTimeStamp = strtotime($dataSet[0]['registered_date']);
			$curretDateTime = strtotime(date('Y-m-d H:i:s'));
			$mins = round(($curretDateTime - $lastTimeStamp) / 60);

			if ($mins > 5){ // 1 min
				return true;
			}
		}

		return false;
	}

	protected function token_mailing($data)
	{
		\Package::load('email');

		// Create an instance
		$email = \Email::forge();

		// Set the from address
		$email->from('no-reply-daibithapchu@tthlan.info', 'DaibiThapChu System');

		// Set the to address
		$email->to($data['email'], $data['email']);

		// Set a subject
		$email->subject('DaibiThapChu - Your token ' . $data['token']);

		// And set the body.
		$email->html_body(\View::forge(
				'email/sendtoken', $data
			),
		);
		try
		{
			$email->send();
		}
		catch(\EmailValidationFailedException $e)
		{
			// The validation failed
			return false;
		}
		catch(\EmailSendingFailedException $e)
		{
			// The driver could not send the email
			return false;
		}
		return true;
	}

	protected function input_validation($input)
	{
		$email = isset($input["email"]) ? $input["email"] : null; //"hlanart @gmail.com";
		$token = isset($input["token"]) ? $input["token"] : null; //"ABCD1234";
		$publishDate = isset($input["publishDate"]) ? $input["publishDate"] : null; //"ABCD1234";
		$lunaDate = isset($input["lunaDate"]) ? $input["lunaDate"] : null; //"ABCD1234";
		$message = isset($_POST["message"]) ? $_POST["message"] : null; //"ABCD1234";

		if (!isset($email)){
			$data['status'] = 'fase';
			$data['response']['message'] = 'Vui lòng nhập Email';

			return $this->response($data);
		}

		if (!isset($token)){
			$data['status'] = 'fase';
			$data['response']['message'] = 'Vui lòng nhập Mã';

			return $this->response($data);
		}

		if (!isset($publishDate)){
			$data['status'] = 'fase';
			$data['response']['message'] = 'Vui lòng nhập Ngày tương lai nhận mail';

			return $this->response($data);
		}

		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$cDate = date('Y-m-d H:i:s');

		if (!isset($publishDate)){
			$publishDateTime = strtotime($dataSet[0]['registered_date']);
			$currentDateTime = strtotime(date('Y-m-d H:i:s'));

			if ($currentDateTime >= $publishDateTime)
			{
				$data['status'] = 'fase';
				$data['response']['message'] = 'Ngày tương lai không hợp lệ';

				return $this->response($data);
			}
		}

		if (isset($message) && strlen(ltrim($message)) > 1000){

			$data['status'] = 'fase';
			$data['response']['message'] = 'Tin nhắn không được vượt quá 1000 ký tự';

			return $this->response($data);
		}

		return true;
	}

	public function post_create()
	{
		$email = isset($_POST["email"]) ? $_POST["email"] : null; //"hlanart @gmail.com";
		$token = isset($_POST["token"]) ? $_POST["token"] : null; //"ABCD1234";
		$publishDate = isset($_POST["publishDate"]) ? $_POST["publishDate"] : null; //"ABCD1234";
		$lunaDate = isset($_POST["lunaDate"]) ? $_POST["lunaDate"] : null; //"ABCD1234";
		$message = isset($_POST["message"]) ? $_POST["message"] : null; //"ABCD1234";

		$this->input_validation($_POST);

		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$cDate = date('Y-m-d H:i:s');

		$entry = Model_Publishdate::find(
			array(
				'select' => array('*'),
				'where' => DB::expr(
					"status = " . Model_Publishdate::STATUS_AUTHENTICATED . " AND " .
					"email like '" . $email . "' AND token like '" . $token . "'"
				),
			)
		);

		if (isset($entry))
		{
			$record = $entry[0];
			$record->message = $message;
			$record->created_date = $cDate;
			$record->publish_date = $publishDate;
			$record->luna_date = $lunaDate;
			$record->status = Model_Publishdate::STATUS_CREATED;

			$record->save();

			$data['status'] = 'true';
			$data['response']['data'] = $record;

			return $this->response($data);
		}
		else{
			$data['status'] = 'false';
			$data['response']['message'] = 'Không tồn tại email với mã. Vui lòng tạo token mới'; // Fail Progress

			return $this->response($data);
		}
	}

	public function get_expand()
	{
		$email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : null; //"hlanart @gmail.com";
		$token = isset($_REQUEST["token"]) ? $_REQUEST["token"] : null; //"ABCD1234";
		$expandMode = isset($_REQUEST["expandMode"]) ? $_REQUEST["expandMode"] : null; // minute, day, week, month, quater, year
		$expandValue = isset($_REQUEST["expandValue"]) ? intval($_REQUEST["expandValue"]) : null;

		$entry = Model_Publishdate::find(
			array(
				'select' => array('*'),
				'where' => DB::expr(
					"status = " . Model_Publishdate::STATUS_PUPBLISHED . " AND " .
					"email like '" . $email . "' AND token like '" . $token . "'"
				),
			)
		);
		
		$this->format = 'html';

		if (isset($entry)){
			$record = $entry[0];

			$publishDate = date_create($record->publish_date)
				->add(date_interval_create_from_date_string($expandValue . " " . $expandMode ."s"))
				->format('Y-m-d H:i:s');

			$record->publish_date = $publishDate;
			$record->luna_date = null;
			$record->run_date = null;
			$record->status = Model_Publishdate::STATUS_CREATED;
			$record->save();

			echo "<h3 style='font-size:5.0vh;max-width:100%;line-height: 200px;text-align: center;'>Cài đặt lập tin nhắn thành công! Cám ơn bạn đã sử dụng</h3>";
		}
		else{
			echo "<h3 style='font-size:5.0vh;max-width:100%;line-height: 200px;text-align: center;;'>Không thể cài đặt lập tin nhắn! Hãy quay lại sau!</h3>";
		}
	}
}