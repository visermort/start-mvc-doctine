<?php

/**
 * mail config
 */
return [
    'server' => [
        'isSMTP' => true,                                 // Set mailer to use SMTP
        'Host' => 'smtp.example.com',                     // Specify main and backup SMTP servers
        'SMTPAuth' => true,                               // Enable SMTP authentication
        'Username' => 'user@example.com',                 // SMTP username
        'Password' => 'secret',                           // SMTP password
        'SMTPSecure' => 'tls',                            // Enable TLS encryption, `ssl` also accepted
        'Port' => 587,
    ],
];