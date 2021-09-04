<?php

class Controller_Sitecron extends Controller
{

    /**
     * The basic Home index
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
		\Package::load('email');
		$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;

        // getting current date
		date_default_timezone_set('Asia/Ho_Chi_Minh');
        $cDate = date('Y-m-d');

		if ($mode === 'now'){
			$dataSet = Model_Publishdate::find(
			array(
					'select' => array('*'),
					'where' => DB::expr("DATEDIFF(publish_date, now()) = 0 AND status = " . Model_Publishdate::STATUS_CREATED)
				)
			);

			if ($mode === 'now' && isset($dataSet))
			{
				foreach($dataSet as $publishData)
				{
					$luna = ($publishData->luna_date ? $publishData->luna_date : '');

					$dataMailing = array('email' => $publishData->email, 'luna' => $luna , 'message' => $publishData->message, 'token' => $publishData->token);
				
					$result = $this->cron_mail($dataMailing);
					if ($result == true){
						$publishData->set(array(
							'status'  => Model_Publishdate::STATUS_PUPBLISHED,
							'run_date' => $cDate
						));

						$publishData->save();
					}
				}
			}
		}
    }

	protected function cron_mail($data){
		// Create an instance
		$email = \Email::forge();

		// Set the from address
		$email->from('no-reply-daibithapchu@tthlan.info', 'DaibiThapChu System');

		// Set the to address
		$email->to($data['email'], $data['email']);

		// Set a subject
		$email->subject('DaibiThapChu - Mail nhắc lịch ngày Hôm nay' . ($data['luna'] != '' ? ' - Âm Lịch ' . $data['luna'] : ''));


		// Set multiple to addresses
		/*$email->to(array(
			'example@mail.com',
			'another@mail.com' => 'With a Name',
		));*/

		// And set the body.
		//$email->body('Bạn có một tin nhắn đã được tạo trước với nội dung:' . $data['message']);		
		$email->html_body(\View::forge(
				'email/publishmessage', $data
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

    /**
     * The 404 action for the application.
     *
     * @access  public
     * @return  Response
     */
    public function action_404()
    {
        return Response::forge(Presenter::forge('welcome/404'), 404);
    }

}
