<?php

namespace Developcreativo\Inventarios\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DistribucionEquipamentExpiredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $filepath;


    public function __construct($filepath)
    {
        $this->filepath = $filepath;
    }

    public function build(): self
    {
        return $this->view('inventarios::equipamientos')
            ->subject('Vencimiento de equipamiento')
            ->attachFromStorageDisk('public', $this->filepath);
    }

}