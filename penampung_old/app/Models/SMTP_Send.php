<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\Exception;
use DB;

class SMTP_Send extends Model
{

    public function send ($template, $email_to, $subjek, $variable, $value_id){
        
        $query_smtp = DB::table('tb_smtp')
        ->where('tb_smtp.delete_smtp','=','N')
        ->orderBy('tb_smtp.id_smtp','DESC')
        ->limit(1)
        ->get();
        
        if($query_smtp->count() < 1){
        
        }else{
            
            foreach($query_smtp as $data_query_smtp);
            
            $params = array(
                    $variable => $value_id,
                );

            //openEmail
            $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
            try {
              // Pengaturan Server
              // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            
              $mail->isSMTP();                                      // Set mailer to use SMTP
              $mail->Host = $data_query_smtp->host_smtp;                  // Specify main and backup SMTP servers
              $mail->SMTPAuth = true;                               // Enable SMTP authentication
              $mail->Username = $data_query_smtp->username_smtp;                 // SMTP username
              $mail->Password = $data_query_smtp->password_smtp;                           // SMTP password
              $mail->SMTPSecure = $data_query_smtp->enkripsi_smtp;                            // Enable TLS encryption, `ssl` also accepted
              $mail->Port = $data_query_smtp->port_smtp;                                    // TCP port to connect to
            
              // Siapa yang mengirim email
              $mail->setFrom($data_query_smtp->alamat_email_smtp, $data_query_smtp->nama_email_smtp);
            
              // Siapa yang akan menerima email
              $mail->addAddress($email_to);
            
              //Content
              $mail->isHTML(true);                                  // Set email format to HTML
              $mail->Subject = $subjek;
              $mail->Body    = view($template, compact('params'));
              $mail->send(); 
            
            } catch (Exception $e) {
                return 'Exception : '.$e;
            }
            //closeEmail
        
        }

    }

}