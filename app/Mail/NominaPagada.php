<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NominaPagada extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $nomina;
    public $empresa;
    public $pdf;
    public $persona;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $nomina, $empresa, $pdf, $persona)
    {
        $this->subject = $subject;
        $this->nomina = $nomina;
        $this->empresa = $empresa;
        $this->pdf = $pdf;
        $this->persona = $persona;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
            ->from($this->empresa->email, $this->empresa->nombre)
            ->view('emails.nomina-pagada')
            ->attachFromStorageDisk('public',$this->pdf);
    }
}
