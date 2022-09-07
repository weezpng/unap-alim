<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QRCodeSecPessRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $attachment_file;

    public function __construct($data, $attachment_file)
    {
        $this->data = $data;
        $this->attachment_file = $attachment_file;
    }

    public function build()
    {
        $as_file = 'QRCode_' . $this->data['nim'] . '.png';
        return $this->markdown('mails.qr_code_request')
        ->with(['data' => $this->data])
        ->subject("GESTÃO DE REFEIÇÃO: Pedido de impressão Código QR");
    }
}
