<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailsController extends Controller
{
    public function send() {
        $data = [
            'name' => 'csi',
            'body' => 'test body',
        ];
        Mail::send('mails.send', $data, function ($message) {
            $message->from('elscsi2019@gmail.com', 'ELS CSI');
            $message->sender('john@johndoe.com', 'John Doe');
            $message->to('grateja1001@gmail.com', 'Vee Jay Grateja');
            $message->subject('Subject');
        });
    }
}
