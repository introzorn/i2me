<?php


namespace App;
use Exception;

class Mailer {

    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;
    public $smtp_reply;
    public $smtp_fromstring;


    
    
    public function __construct($smtp_username='', $smtp_password='', $smtp_host='', $smtp_from='', $smtp_port = 25, $smtp_charset = "utf-8") {
        if(!$smtp_username){$smtp_username=SMTP_USER;}
        if(!$smtp_password){$smtp_password=SMTP_PASS;}
        if(!$smtp_host){$smtp_host=SMTP_HOST;}
        if(!$smtp_port){$smtp_port=SMTP_PORT;}
        if(!$smtp_from){$smtp_from=SMTP_FROM;}
        
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_from = $smtp_from;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;
        
    }
    
 
    public function send($mailTo, $subject, $message, $headers) {
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= "To: " . $mailTo ."\r\n";
        $contentMail .= "From: " . $this->smtp_fromstring ."<".$this->smtp_from.">\r\n";
         if ($this->smtp_reply) {
            $contentMail .= "Reply-To: " . $this->smtp_reply . "\r\n";
        }
        $contentMail .= "X-Sender: ".$this->smtp_from."\r\n";
        $contentMail .= "X-Mailer: CodeIgniter\r\n";
        $contentMail .= "X-Priority: 2\r\n";
        $contentMail .= "Mime-Version: 1.0\r\n";
        $contentMail .= "Content-Type: text/html; charset=".$this->smtp_charset."\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?'  . base64_encode($subject) . "=?=\r\n";
        
       
        $contentMail .= $headers . "\r\n";
        $contentMail .= $message . "\r\n";
       
        try {
            $this->doLOG('connect '.$this->smtp_host.':'.$this->smtp_port);
            if(!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)){
                throw new Exception($errorNumber.".".$errorDescription);
               
            }
            if (!$this->_parseServer($socket, "220")){
                throw new Exception('Connection error');
                
            }
			
           
			$server_name = $_SERVER["SERVER_NAME"];
             $this->doLOG("HELO $server_name");
            fputs($socket, "HELO $server_name\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: HELO');
                
            }

            if(defined('SMTP_NOAUTH')==true && constant('SMTP_NOAUTH')==false){
                $this->doLOG("AUTH LOGIN");
                fputs($socket, "AUTH LOGIN\r\n");
            
                if (!$this->_parseServer($socket, "334")) {
                    fclose($socket);
                    throw new Exception('Autorization error');
                
                }
                
                $this->doLOG("login $this->smtp_username");
                fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            
                if (!$this->_parseServer($socket, "334")) {
                    fclose($socket);
                    throw new Exception('Autorization error');
                    
                }
                $this->doLOG("poass $this->smtp_password");
                fputs($socket, base64_encode($this->smtp_password) . "\r\n");
                if (!$this->_parseServer($socket, "235")) {

                    fclose($socket);
                    throw new Exception('Autorization error');
                
                }
             }

            $this->doLOG( "MAIL FROM: <".$this->smtp_from.">");
            fputs($socket, "MAIL FROM: <".$this->smtp_from.">\r\n");
            if (!$this->_parseServer($socket, "250")) {
               
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }
            
			$mailTo = ltrim($mailTo, '<');
			$mailTo = rtrim($mailTo, '>');
            $this->doLOG("RCPT TO: <" . $mailTo . ">");

            fputs($socket, "RCPT TO: <" . $mailTo . ">\r\n");     
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: RCPT TO');
               
            }
            
            $this->doLOG( "R Data"); 
            fputs($socket, "DATA\r\n");     
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
               
            }
            $this->doLOG( "R Data content"); 
            fputs($socket, $contentMail."\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
                
            }
            
            $this->doLOG("QUIT"); 
            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            $this->doLOG('error-'.$e->getMessage()); 
            return  $e->getMessage();
            
        }
        return true;
    }
    
    private function _parseServer($socket, $response) {
        $responseServer='';
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                $this->doLOG($responseServer);
                return false;
            }
            $this->doLOG($responseServer);  
        }
      
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
         
        return true;
      
    }

    function doLOG($data){

        //отелючаем лог
        return;

        $file = 'maillog.txt';
        if (!file_exists($file)) { file_put_contents($file,"\r\n");}
        $current = file_get_contents($file);

        $current .= date("[d.M.Y H:i:s]::").$data."\r\n";

        file_put_contents($file, $current);
      
    }
}