<?php

namespace App\Libraries;

use CodeIgniter\Email\Email as BaseEmail;

/**
 * Extiende el Email de CodeIgniter para aceptar respuesta 250 a QUIT.
 * Algunos servidores SMTP (ej. Exim en zglobalhost) responden 250 OK en lugar de 221 al QUIT;
 * el framework espera 221 y marca error aunque el mensaje ya fue aceptado (250 OK id=...).
 */
class Email extends BaseEmail
{
    /**
     * Para QUIT acepta 221 (estándar) o 250. Resto igual que el padre.
     *
     * @inheritdoc
     */
    protected function sendCommand($cmd, $data = '')
    {
        if ($cmd === 'quit') {
            $this->sendData('QUIT');
            $reply = $this->getSMTPData();

            $this->debugMessage[] = '<pre>' . $cmd . ': ' . $reply . '</pre>';

            $code = (int) static::substr($reply, 0, 3);
            if ($code !== 221 && $code !== 250) {
                $this->setErrorMessage(lang('Email.SMTPError', [$reply]));
                return false;
            }

            if (is_resource($this->SMTPConnect)) {
                fclose($this->SMTPConnect);
            }
            return true;
        }

        return parent::sendCommand($cmd, $data);
    }
}
