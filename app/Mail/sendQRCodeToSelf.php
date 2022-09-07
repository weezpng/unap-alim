<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class sendQRCodeToSelf extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $attachment_file;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $attachment_file)
    {
        $this->data = $data;
        $this->attachment_file = $attachment_file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails.qr_code_self')
        ->with(['data' => $this->data])
        ->subject("GESTÃO DE REFEIÇÃO: Código QR")
        ->attach(public_path($this->attachment_file,), [
            'as' => 'QRCode.png',
            'mime' => 'image/png',
       ]);;
    }
}
