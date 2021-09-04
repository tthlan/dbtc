<?php
class Model_PublishDate extends \Model_Crud {
    
    protected static $_table_name = 'android_daibithapchu_address'; 
    /*protected static $_primary_key = array('id'); 
    protected static $_properties = array('id', 'remote_addr',	'lastest',	'count');*/

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