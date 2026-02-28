<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public string $fromEmail  = '';
    public string $fromName   = '';
    public string $recipients = '';

    /**
     * The "user agent"
     */
    public string $userAgent = 'CodeIgniter';

    /**
     * The mail sending protocol: mail, sendmail, smtp
     */
    public string $protocol = 'smtp';

    /**
     * The server path to Sendmail.
     */
    public string $mailPath = '/usr/sbin/sendmail';

    /**
     * SMTP Server Hostname
     */
    public string $SMTPHost = '';

    /**
     * SMTP Username
     */
    public string $SMTPUser = '';

    /**
     * SMTP Password
     */
    public string $SMTPPass = '';

    /**
     * SMTP Port
     */
    public int $SMTPPort = 465;

    /**
     * SMTP Crypto (ssl, tls)
     */
    public string $SMTPCrypto = 'ssl';
    
    public function __construct()
    {
        parent::__construct();

        // Cargar configuración desde .env (local) o variables de entorno (GKE)
        $this->fromEmail = getenv('EMAIL_FROM_EMAIL') ?: ($_ENV['EMAIL_FROM_EMAIL'] ?? env('email.fromEmail', ''));
        $this->fromName  = getenv('EMAIL_FROM_NAME') ?: ($_ENV['EMAIL_FROM_NAME'] ?? env('email.fromName', ''));
        $this->SMTPHost  = getenv('EMAIL_SMTP_HOST') ?: ($_ENV['EMAIL_SMTP_HOST'] ?? env('email.SMTPHost', ''));
        $this->SMTPUser  = getenv('EMAIL_SMTP_USER') ?: ($_ENV['EMAIL_SMTP_USER'] ?? env('email.SMTPUser', ''));
        // SMTPPass debe estar definida para que el servidor acepte (SMTP AUTH); suele venir del Secret vitasync-email en GKE
        $this->SMTPPass  = getenv('EMAIL_SMTP_PASS') ?: ($_SERVER['EMAIL_SMTP_PASS'] ?? $_ENV['EMAIL_SMTP_PASS'] ?? env('email.SMTPPass', ''));
        $this->SMTPPort  = (int) (getenv('EMAIL_SMTP_PORT') ?: ($_ENV['EMAIL_SMTP_PORT'] ?? env('email.SMTPPort', 465)));
        $this->SMTPCrypto = getenv('EMAIL_SMTP_CRYPTO') ?: ($_ENV['EMAIL_SMTP_CRYPTO'] ?? env('email.SMTPCrypto', 'ssl'));
    }

    /**
     * SMTP Timeout (in seconds)
     */
    public int $SMTPTimeout = 5;

    /**
     * Enable persistent SMTP connections
     */
    public bool $SMTPKeepAlive = false;

    /**
     * Enable word-wrap
     */
    public bool $wordWrap = true;

    /**
     * Character count to wrap at
     */
    public int $wrapChars = 76;

    /**
     * Type of mail, either 'text' or 'html'
     */
    public string $mailType = 'html';

    /**
     * Character set (utf-8, iso-8859-1, etc.)
     */
    public string $charset = 'UTF-8';

    /**
     * Whether to validate the email address
     */
    public bool $validate = false;

    /**
     * Email Priority. 1 = highest. 5 = lowest. 3 = normal
     */
    public int $priority = 3;

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $CRLF = "\n";

    /**
     * Newline character. (Use "\r\n" to comply with RFC 822)
     */
    public string $newline = "\n";

    /**
     * Enable BCC Batch Mode.
     */
    public bool $BCCBatchMode = false;

    /**
     * Number of emails in each BCC batch
     */
    public int $BCCBatchSize = 200;

    /**
     * Enable notify message from server
     */
    public bool $DSN = false;
}
