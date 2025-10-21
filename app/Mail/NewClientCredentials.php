<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewClientCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $client;

    public function __construct($client, $password)
    {
        $this->client = $client;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Vos identifiants - Compte')
            ->view('emails.new_client_credentials')
            ->with([
                'client' => $this->client,
                'password' => $this->password,
            ]);
    }
}
