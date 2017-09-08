<?php
/**
 * Created by PhpStorm.
 * User: wuzc
 * Date: 17-7-5
 * Time: 下午9:04
 */

$file = date('Ym/d') . '.log';
$url = sprintf('', $file);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$rs = curl_exec($ch);
curl_close($ch);

if ('fopen() failed' == $rs) {
    echo 'nothing';
} else {
    send_email($rs);
    exit;
}

function send_email($content = 'haha')
{
    include 'class.phpmailer.php';
    include 'class.smtp.php';

    $mail = new PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Charset = 'UTF-8';
    $mail->Host = 'smtp.163.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = '';                 // SMTP username
    $mail->Password = '';                       // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 994;                                    // TCP port to connect to

    $mail->setFrom('', "=?utf-8?B?" . base64_encode("短信监控wuzhc") . "?=");
    $mail->addAddress('', 'Joe User');    // Add a recipient
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = "=?utf-8?B?" . base64_encode("阿里云短信监控") . "?=";
    $mail->Body = $content;

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}