<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

abstract class smart_forms_email_troubleshoot_base {

    public $LatestError="";
    public function __construct()
    {

    }

    public abstract function Start();

}


/*-------------------------------------------------------------------basic-------------------------------------------------------------------------*/
class smart_forms_email_troubleshoot_basic extends smart_forms_email_troubleshoot_base {
    public function __construct()
    {

    }

    public function Start()
    {
        $to=JFactory::getApplication()->input->get("To",'','raw');
        global $current_user;
        $headers = "MIME-Version: 1.0\r\n" .
            "From: " . $current_user->user_email . "\r\n" .
            "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\r\n";
        if(wp_mail( $to,"Smart Forms Test Email", "Test successful!, so your site can accept emails, lets continuing checking what is going wrong.", $headers ))
            return true;
        return false;
    }
}

/*-------------------------------------------------------------------basic-------------------------------------------------------------------------*/
class smart_forms_email_troubleshoot_custom_smtp extends smart_forms_email_troubleshoot_base {
    public function __construct()
    {

    }

    public function Start()
    {

        $errors = '';
        require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
        $mail = new PHPMailer();

        $from_name  = JFactory::getApplication()->input->get("FromEmailAddress",'','raw');
        $from_email = JFactory::getApplication()->input->get("FromEmailAddress",'','raw');
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->Username = JFactory::getApplication()->input->get("FromEmailAddress",'','raw');
        $mail->Password = JFactory::getApplication()->input->get("FromEmailPassword",'','raw');
        $mail->SMTPSecure="ssl";



        $mail->Host = 'smtp.mail.yahoo.com';
        $mail->Port = '465';
        $mail->SetFrom( $from_email, $from_name );
        $mail->isHTML( true );
        $mail->Subject = utf8_decode(JFactory::getApplication()->input->get("EmailSubject",'','raw'));
        $mail->MsgHTML(JFactory::getApplication()->input->get("EmailText",'','raw'));
        $mail->AddAddress( JFactory::getApplication()->input->get("ToEmail",'','raw'));
        $mail->SMTPDebug = true;
        ob_start();

        if ( ! $mail->Send() )
            $errors = $mail->ErrorInfo;
        $smtp_debug = ob_get_clean();
        $mail->ClearAddresses();
        $mail->ClearAllRecipients();

        if ( ! empty( $errors ) ) {
            $this->LatestError='Error:'.$errors.'\r\n Detail:'.$smtp_debug;

            return false;
        }
        else{
            return true;
        }
    }


}

