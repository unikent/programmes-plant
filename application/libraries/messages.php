<?php
class Messages {

     public static $msgss = array();

     /**
      * Add a message to the message array (adds to the user's session)
      * @param string  $type    You can have several types of messages, these are class names for Bootstrap's messaging classes, usually, info, error, success, warning
      * @param string $message  The message you want to add to the list
      */
     public static function add($type = 'info',$message = false){
     	if(!$message) return false;
     	if(is_array($message)){
     		foreach($message as $msg){
     			static::$msgss[$type][] = $msg;
     		}
     	}else{
     		static::$msgss[$type][] = $message;
     	}
     	Session::flash('messages', static::$msgss);
     }

     /**
      * Pull back those messages from the session
      * @return array
      */
     public static function get(){
     	return Session::get('messages');
     }
    
     /**
      * Gets all the messages from the session and formats them accordingly for Twitter bootstrap.
      * @return string
      */
     public static function get_html(){
     	$messages = Session::get('messages');
     	$output = false;
     	if($messages){
     		foreach($messages as $type=>$msgs){
     			$output .= '<div class="alert alert-'.$type.'"><a class="close" data-dismiss="alert">×</a>';
     			foreach($msgs as $msg){
     				$output .= '<p>'.$msg.'</p>';
     			}
     			$output .= '</div>';
     		}
     	}
     	return $output;
     }
}