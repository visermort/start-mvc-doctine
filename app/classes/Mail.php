<?php

namespace app\classes;

use app\App;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class Mail
 * @package app\classes
 */
class Mail extends PHPMailer
{
    /**
     * Mail constructor.
     * @param null $exceptions
     */
    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);

        $config = App::getComponent('config')->get('mail.server');
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
            if (method_exists($this, $key) && $value) {
                $this->$key();
            }
        }
    }
}