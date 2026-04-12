<?php
/**
 * FastRide Simple SMTP Mailer Utility
 * Uses socket connection to send emails via SMTP without external libraries
 */

function sendEmail($to, $subject, $htmlContent) {
    // Load config from environment or defaults
    $host = getenv('EMAIL_HOST') ?: 'smtp.mailersend.net';
    $port = (int)(getenv('EMAIL_PORT') ?: 587);
    $user = getenv('EMAIL_USER') ?: 'MS_ARp6sE@test-51ndgwvmqpdlzqx8.mlsender.net';
    $pass = getenv('EMAIL_PASS') ?: 'mssp.NyRKK9S.o65qngkp5jolwr12.y097TJB';
    $from = $user;
    $fromName = 'FastRide Support';

    try {
        $socket = fsockopen($host, $port, $errno, $errstr, 10);
        if (!$socket) throw new Exception("Could not connect to SMTP server: $errstr ($errno)");

        $log = [];
        $log[] = fgets($socket, 515);

        // HELO/EHLO
        fwrite($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
        $log[] = fgets($socket, 515);
        while (substr($log[count($log)-1], 3, 1) === '-') {
            $log[] = fgets($socket, 515);
        }

        // STARTTLS
        fwrite($socket, "STARTTLS\r\n");
        $log[] = fgets($socket, 515);
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        // EHLO again after TLS
        fwrite($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
        $log[] = fgets($socket, 515);
        while (substr($log[count($log)-1], 3, 1) === '-') {
            $log[] = fgets($socket, 515);
        }

        // AUTH LOGIN
        fwrite($socket, "AUTH LOGIN\r\n");
        $log[] = fgets($socket, 515);

        fwrite($socket, base64_encode($user) . "\r\n");
        $log[] = fgets($socket, 515);

        fwrite($socket, base64_encode($pass) . "\r\n");
        $log[] = fgets($socket, 515);

        if (substr($log[count($log)-1], 0, 3) !== '235') {
            throw new Exception("SMTP Authentication failed: " . end($log));
        }

        // MAIL FROM
        fwrite($socket, "MAIL FROM:<$from>\r\n");
        $log[] = fgets($socket, 515);

        // RCPT TO
        fwrite($socket, "RCPT TO:<$to>\r\n");
        $log[] = fgets($socket, 515);

        // DATA
        fwrite($socket, "DATA\r\n");
        $log[] = fgets($socket, 515);

        // Build headers
        $headers = [
            "From: \"$fromName\" <$user>",
            "To: <$to>",
            "Subject: $subject",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Date: " . date("r"),
            "Message-ID: <" . time() . "-" . md5($user . $to) . "@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ">",
            "X-Mailer: FastRide Custom Mailer"
        ];

        $content = implode("\r\n", $headers) . "\r\n\r\n" . $htmlContent . "\r\n.\r\n";
        fwrite($socket, $content);
        $log[] = fgets($socket, 515);

        // QUIT
        fwrite($socket, "QUIT\r\n");
        fclose($socket);

        return true;
    } catch (Exception $e) {
        error_log("Email Sending Failed: " . $e->getMessage());
        return false;
    }
}
