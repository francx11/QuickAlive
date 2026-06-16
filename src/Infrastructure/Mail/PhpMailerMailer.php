<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail;

use PHPMailer\PHPMailer\PHPMailer;

final readonly class PhpMailerMailer
{
    public function __construct(
        private string $host,
        private string $username,
        private string $password,
    ) {}

    public function enviar(string $destinatario, string $asunto, string $cuerpoHtml): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($this->username, 'QuickAlive');
            $mail->addAddress($destinatario);
            $mail->isHTML(true);
            $mail->Subject = mb_encode_mimeheader($asunto, 'UTF-8');
            $mail->Body = $cuerpoHtml;

            return $mail->send();
        } catch (\Throwable) {
            return false;
        }
    }
}
