<?php

class Controller_Daibithapchu extends Controller
{

    /**
     * The basic Home index
     *
     * @access  public
     * @return  Response
     */
    public function action_index()
    {
        /* check for reporting part */
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $report = isset($_REQUEST['report']) ? preg_replace('/[^0-9a-zA-Z_\-\.]/', '', $_REQUEST['report']) : null;
        $md5_value = strtotime(date('Y-m-d'));
        $check_token = md5('A_Di_Da_Phat' . $md5_value);
        //var_dump($report,('~' . date('Y-m-d')), $report === ('~' . date('Y-m-d')));

        if (isset($report) && $report == ('-' . date('Y-m-d') . '-')){
            var_dump($check_token);

            //echo $_SERVER['REMOTE_ADDR'];
            die();
        }
        else if (isset($report) && $report == $check_token) {
            $result = Model_Daibithapchu::find(array(
                'select' => array('REMOTE_ADDR','lastest','count'),
                'where' => DB::expr("lastest > (now() - interval 180 day)"),
                'order_by' => array('lastest'=> 'desc'),
            ));
            $saveDate="";
            $count = $total = 1;

            $result = Format::forge($result)->to_array();
            print("<pre style='    text-align: right;    justify-content: center;    display: flex;'>");
            foreach($result as $r => $d) {
                //print_r($d);
                $col =  Format::forge($d)->to_array();
                //var_dump(left($col['lastest'],10));
                /**/
                if(substr($col['lastest'],0,10)!== $saveDate){
                	if($saveDate!=="")
                		print($count. "/" .$total."<br>");// summary

                	$count = $total =0;

                	$saveDate=substr($col['lastest'],0,10);
                	print ($saveDate); // group by date

                }
                if(substr($col['lastest'],0,10)=== $saveDate || $saveDate){
                	$count += (int)$col['count'];
                	$total += 1;
                }
                /**/

                //var_dump(preg_replace("/([0-9]+\\.[0-9]+\\.[0-9]+)\\.[0-9]+/", '\\1.1.xxx', $col['REMOTE_ADDR']));
                //var_dump(preg_replace("/\d+$/", 'xxx', $col['REMOTE_ADDR']));
                //var_dump(preg_replace("/\d{2,}$/", 'xxx', $col['REMOTE_ADDR']));

                $col1 = preg_replace("/^\d+/", "xxx" ,$col['REMOTE_ADDR']);
                if (strlen($col['REMOTE_ADDR']) < 18)
                {
                    $col1 = str_repeat(" ", 18 - strlen($col1)) . $col1;
                }

                $col2 = str_repeat(" ", 5) . $col['lastest'];

                $col3 = $col['count'];
                if (strlen($col['count']) < 5)
                {
                    $col3 = str_repeat(" ", 5 - strlen($col3)) . $col3;
                }

                print("{$col1} - {$col2} - {$col3}<br>");
            }
            print("</pre>");
            die();
        }
        else if (isset($report) && $report != $check_token &&
        	$report != ('-' . date('Y-m-d') . '-')){
    		die();// not log
 		}
        /* saving log & update data */
        $result = Model_Daibithapchu::find(array(
            'select' => array('*'),
            'where' => array(
                array('remote_addr', 'like', $_SERVER['REMOTE_ADDR'])
            ),
        ));

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        // getting current date
        $cDate = date('Y-m-d H:i:s');

        /* Neu co roi thi tang bien dem count */
        if ($result != null)
        {
            $timestamp = strtotime($result[0]->lastest); //1373673600
            // Getting the value of old date + 24 hours
            $oldDate = $timestamp + 720; // 86400 seconds in 24 hrs

            // var_dump($timestamp, $oldDate , $cDate);
            // Cach lan log truoc 24h
            if($oldDate < strtotime($cDate)){
                $result[0]->count = $result[0]->count + 1;
                $result[0]->lastest = $cDate;
                $result[0]->save();
            }
        }
        /* Neu chua co thi them dia chi */
        else{
            $result = Model_Daibithapchu::forge()->set(array(
                'remote_addr' => $_SERVER['REMOTE_ADDR'],
                'lastest' => $cDate
            ));
            $result->save();
        }

        // redirect page
        if (!isset($report))
        {
            return Response::forge(View::forge('home/index'));
        }

        //return Response::forge(View::forge('home/index'));
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