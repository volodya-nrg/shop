<?php

final class ServiceEmail
{
    private $conn;
    private string $br = "\r\n";
    private int $timeout = 10;
    private string $smtpServer;
    private int $port;
    private string $login;
    private string $pass;
    private string $from;
    private bool $isTestMode;

    public function __construct(string $smtpServer, int $port, string $login, string $pass, string $from, bool $isTestMode)
    {
        $this->smtpServer = $smtpServer;
        $this->port = $port;
        $this->login = $login;
        $this->pass = $pass;
        $this->from = $from;
        $this->isTestMode = $isTestMode;
    }

    public function send(string $to, string $subject, string $msg, array $aFilepath = []): null|Error
    {
        $files = [];

        foreach ($aFilepath as $filepath) {
            if (!is_file($filepath)) {
                continue;
            }

            $fa = fopen($filepath, "rb");
            $files[] = [
                "name" => basename($filepath),
                "data" => fread($fa, filesize($filepath))
            ];
            fclose($fa);
        }

        $serverName = explode("@", $this->from)[1];
        $header = "MIME-Version: 1.0" . $this->br;
        $header .= "Date: " . date('D, d M Y H:i:s O') . $this->br;
        $header .= "From: =?utf-8?b?" . base64_encode($serverName) . "?= <{$this->from}>" . $this->br;
        $header .= "Reply-To: =?utf-8?b?" . base64_encode($serverName) . "?= <{$this->from}>" . $this->br;
        $header .= "Return-Path: {$this->from}" . $this->br;
        $header .= "X-Mailer: PHP/" . phpversion() . $this->br;
        $header .= "X-Priority: 3 (Normal)" . $this->br;
        $header .= "Message-ID: <172562218." . date("YmjHis") . "@{$serverName}>" . $this->br;
        $header .= "To: <{$to}>" . $this->br;
        $header .= "Subject: =?utf-8?b?" . base64_encode($subject) . "?=" . $this->br;

        if (count($files)) {
            $bound = "--" . md5(uniqid(rand(), true));
            $header .= "Content-Type: multipart/mixed; boundary=\"{$bound}\"" . $this->br;

            $temp = $this->br . "--{$bound}" . $this->br;
            $temp .= "Content-Type: text/html; charset=utf-8" . $this->br;
            $temp .= "Content-Transfer-Encoding: base64" . $this->br;
            $temp .= $this->br;
            $temp .= chunk_split(base64_encode($msg));

            foreach ($files as $val) {
                $temp .= $this->br . $this->br . "--{$bound}" . $this->br;
                $temp .= "Content-Type: application/octet-stream; name=\"" . $val["name"] . "\"" . $this->br;
                $temp .= "Content-Transfer-Encoding: base64" . $this->br;
                $temp .= "Content-Disposition: attachment; filename=\"" . $val["name"] . "\"" . $this->br;
                $temp .= $this->br;
                $temp .= chunk_split(base64_encode($val["data"]));
            }

            $msg = $temp;

        } else {
            $header .= "Content-Type: text/html; charset=utf-8" . $this->br;
            $header .= "Content-Transfer-Encoding: base64" . $this->br;

            $msg = base64_encode($msg);
        }

        // если тестовый режим, то далее нет смысла идти
        if ($this->isTestMode) {
            return null;
        }

        $this->conn = fsockopen(
            $this->smtpServer,
            $this->port,
            $errCode,
            $errMsg,
            $this->timeout,
        );
        if ($this->conn === false) {
            return new Error("not connect to smtp server: {$errCode}, {$errMsg}");
        }

        fputs($this->conn, "EHLO {$serverName}{$this->br}");
        if (!$this->isResponseCode("220")) {
            fclose($this->conn);
            return new Error("error in EHLO");
        }

        fputs($this->conn, "AUTH LOGIN{$this->br}");
        if (!$this->isResponseCode("250")) {
            fclose($this->conn);
            return new Error("error in AUTH LOGIN");
        }

        fputs($this->conn, base64_encode($this->login) . $this->br);
        if (!$this->isResponseCode("334")) {
            fclose($this->conn);
            return new Error("error in login");
        }

        fputs($this->conn, base64_encode($this->pass) . $this->br);
        if (!$this->isResponseCode("334")) {
            fclose($this->conn);
            return new Error("error in password");
        }

        fputs($this->conn, "MAIL FROM:{$this->from}{$this->br}");
        if (!$this->isResponseCode("235")) {
            fclose($this->conn);
            return new Error("error in MAIL FROM");
        }

        fputs($this->conn, "RCPT TO:{$to}{$this->br}");
        if (!$this->isResponseCode("250")) {
            fclose($this->conn);
            return new Error("error in RCPT TO");
        }

        fputs($this->conn, "DATA{$this->br}");
        if (!$this->isResponseCode("250")) {
            fclose($this->conn);
            return new Error("error in DATA");
        }

        fputs($this->conn, "{$header}{$this->br}{$msg}{$this->br}.{$this->br}");
        if (!$this->isResponseCode("354")) {
            fclose($this->conn);
            return new Error("error in send");
        }

        fputs($this->conn, "QUIT{$this->br}");
        fclose($this->conn);

        return null;
    }

    private function isResponseCode(string $expected): bool
    {
        $data = "";

        while ($str = fgets($this->conn, 256)) {
            $data .= $str;

            if (substr($str, 3, 1) === " ") {
                break;
            }
        }

        $actual = substr($data, 0, 3);
        return $expected === $actual;
    }
}