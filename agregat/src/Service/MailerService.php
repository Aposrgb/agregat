<?php

namespace App\Service;

use App\Helper\EnumType\SettingsType;
use App\Repository\SettingsRepository;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;

class MailerService
{
    public function __construct(
        protected SettingsRepository $settingsRepository,
        protected                    $mailFrom,
        protected                    $host,
        protected                    $username,
        protected                    $password,
        protected                    $port,
        protected Environment        $twig
    )
    {
    }

    public function sendMailTemplate($template, string $subject = '', array $context = [], array $toAddresses = [])
    {
        $mail = new PHPMailer(true);
        $mail->Host = $this->host;
        $mail->Username = $this->username;
        $mail->Password = $this->password;
        $mail->Port = $this->port;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($this->mailFrom);
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        if (count($toAddresses) > 0) {
            foreach ($toAddresses as $toAddress) {
                $mail->addAddress($toAddress);
            }
        } else {
            $mail->addAddress($this->settingsRepository->findOneBy(['type' => SettingsType::SENDER_NAME->value])?->getSenderTo());
        }
        $mail->Subject = $subject;
        $mail->msgHTML($this->twig->render($template, $context));
        $mail->send();
    }
}
