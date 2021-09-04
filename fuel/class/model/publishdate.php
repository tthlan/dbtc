<?php
class Model_Publishdate extends \Model_Crud {
    
    protected static $_table_name = 'andriod_daibithapchu_publishdate'; 
    /*protected static $_primary_key = array('id'); 
    protected static $_properties = array('id', 'remote_addr',	'lastest',	'count');*/
	
	const STATUS_NEW = 0;
	const STATUS_AUTHENTICATED = 1;
	const STATUS_CREATED = 2;
	const STATUS_PUPBLISHED = 3;

    protected static $_observers =
        array(
          'Orm\Observer_CreatedAt' => array(
          'events' => array('before_insert'),
          'mysql_timestamp' => false,
        ),
          'Orm\Observer_UpdatedAt' => array(
          'events' => array('before_update'),
          'mysql_timestamp' => false,
        ),
    );
    public static function get_results()
    {
        // Database interactions
    }

}