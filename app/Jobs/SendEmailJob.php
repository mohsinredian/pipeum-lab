<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Mail\Mailer;
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $toEmails;
    protected $subject;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param  string|array  $toEmails
     * @param  string  $subject
     * @param  array  $data
     * @return void
     */
    public function __construct($toEmails, $subject, $data)
    {
        $this->toEmails = $toEmails;
        $this->subject = $subject;
        $this->data = $data;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $mailer->send('emailtemp.doc_mail', $this->data, function ($message) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            $message->to($this->toEmails);
            $message->cc('raushan.k@rediansoftware.com');
            $message->subject($this->subject);
        });
    }
}
