<?php
class Help_Controller extends Admin_Controller
{

    public $restful = true;

    public function get_index()
    {
        return View::make('admin.help.index',$this->data);
    }

    public function post_index(){
        $rules = array(
            'issue' => 'required'
        );
        $validation = Validator::make(Input::all(),$rules);
        if($validation->fails()){
            Messages::add('error','Please make sure you fill out the form to let us know what problem you are having.');
            return Redirect::to($this->views.'')->with_input();
        }else{
            // Get the Swift Mailer instance
            $mailer = IoC::resolve('mailer');
            // Construct the message
            $message = Swift_Message::newInstance('Message From Website')
            ->setFrom(array($this->data['user']->email => $this->data['user']->fullname))
            ->setTo(array(TECHNICAL_EMAIL=>'Framework Support For '.COMPANY_NAME))
            ->setBody('<p><strong>'.$this->data['user']->fullname.' has emailed you:</strong></p>
            <q>'.Input::get('issue').'</q>
            <p><strong>Email Address: </strong> '.$this->data['user']->email.'</p>
            <p><strong>IP Address: </strong> '.Request::ip().'</p>
            <p><strong>User Agent: </strong> '.Request::server('HTTP_USER_AGENT').'</p>'
            ,'text/html');
            // Send the email
            $mailer->send($message);
            Messages::add('success','<strong>Support Issue Sent</strong> We\'ll be in touch shortly.');
            return Redirect::to('help');
        }
    }


}