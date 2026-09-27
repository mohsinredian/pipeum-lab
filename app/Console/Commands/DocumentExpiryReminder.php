<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Document;
use App\Mail\DocumentExpiryMail;
use Illuminate\Support\Facades\Mail;
use DB;
class DocumentExpiryReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'notify:document-expiry-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify admin and user about document expiration';

    protected $log_file_name = 'document_expiration_reminder_log.txt';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->log_file = storage_path('logs'.DIRECTORY_SEPARATOR.$this->log_file_name);
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Start');
            $log_text = "\n\n".'******** Cron Start ********'."\n";
            $log_text .= 'Start time '.date('Y-m-d H:i:s')."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);

            $this->beforeMonth();

            $this->beforeHalfMonth();
            $this->info('End');
            
        } catch (\Exception $e) {
            $log_text = "\n".'Exception: '.$e->getMessage()."\n";
            $log_text .= 'File: '.$e->getfile()."\n";
            $log_text .= 'Line No: '.$e->getLine()."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        } finally {
            $log_text = "\n".'End time '.date('Y-m-d H:i:s')."\n";
            $log_text .= '******** Cron Ends ********'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function beforeMonth(){
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+30 day', strtotime($start)));
        $days= 30;
        $this->checkBrpExp($start,$end,$days);
        $this->checkEcsExp($start,$end,$days);
        $this->checkDrivingLicenceExp($start,$end,$days);
        $this->checkCarInsuranceExp($start,$end,$days);
        $this->checkCarTaxExp($start,$end,$days);
        $this->checkMotExp($start,$end,$days);

    }public function beforeHalfMonth(){
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime('+15 day', strtotime($start)));
        $days= 15;
        $this->checkBrpExp($start,$end,$days);
        $this->checkEcsExp($start,$end,$days);
        $this->checkDrivingLicenceExp($start,$end,$days);
        $this->checkCarInsuranceExp($start,$end,$days);
        $this->checkCarTaxExp($start,$end,$days);
        $this->checkMotExp($start,$end,$days);
    }
    public function checkBrpExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('brp_exp', '!=', null)
                ->whereDate('brp_exp', '>=', $start)
                ->whereDate('brp_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"brp" as category'), DB::raw('DATE_FORMAT(brp_exp, "%b %d, %Y") as expiry_date'),'brp_exp')
                ->get();

        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->brp_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days
                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No brp records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function checkEcsExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('ecs_expired', '!=', null)
                ->whereDate('ecs_expired_exp', '>=', $start)
                ->whereDate('ecs_expired_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"ecs" as category'), DB::raw('DATE_FORMAT(ecs_expired, "%b %d, %Y") as expiry_date'),'ecs_expired_exp')
                ->get();

        if (count($documents) > 0) {
            ;
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->ecs_expired_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days

                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No ecs records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function checkDrivingLicenceExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('driving_licence_exp', '!=', null)
                ->whereDate('driving_licence_exp', '>=', $start)
                ->whereDate('driving_licence_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"driving licence" as category'), DB::raw('DATE_FORMAT(driving_licence_exp, "%b %d, %Y") as expiry_date'),'driving_licence_exp')
                ->get();
        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->driving_licence_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days
                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No driving licence records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function checkCarInsuranceExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('car_exp', '!=', null)
                ->whereDate('car_exp', '>=', $start)
                ->whereDate('car_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"car insurance" as category'), DB::raw('DATE_FORMAT(car_exp, "%b %d, %Y") as expiry_date'),'car_exp')
                ->get();
        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->car_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days
                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No car insurance records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function checkCarTaxExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('tax_exp', '!=', null)
                ->whereDate('tax_exp', '>=', $start)
                ->whereDate('tax_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"car tax" as category'), DB::raw('DATE_FORMAT(tax_exp, "%b %d, %Y") as expiry_date'),'tax_exp')
                ->get();
        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->tax_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days
                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No car tax records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function checkMotExp($start,$end,$days){
        $documents = Document::leftjoin('employee', 'employee.id', '=', 'documents.employee_id')
                ->where('employee.status', '=', 1)
                ->where('mot_exp', '!=', null)
                ->whereDate('mot_exp', '>=', $start)
                ->whereDate('mot_exp', '<=', $end)
                ->select('documents.id', 'employee.id as employee_id', 'employee.name','employee.email', DB::raw('"mot" as category'), DB::raw('DATE_FORMAT(mot_exp, "%b %d, %Y") as expiry_date'),'mot_exp')
                ->get();
        if (count($documents) > 0) {
            foreach ($documents as $document) {
                $datetime1 = new \DateTime($start);
                $datetime2 = new \DateTime($document->mot_exp);
                $interval = $datetime1->diff($datetime2);
                $diffDays = $interval->format('%a');//now do whatever you like with $days
                if($days == $diffDays){
                    $this->send_mail($document);
                }
            }
        }else{
            $log_text = "\n".'No mot records found.'."\n";
            file_put_contents($this->log_file, $log_text, FILE_APPEND);
        }
    }
    public function send_mail($data){

        $details['subject'] = 'Document Expires Soon';
        $details['content'] = $data['name']."'s document type ".$data['category'].' is expiring on '.$data['expiry_date'].'.';
        $details['name'] = $data['name'];
        $admin_mail = config('constants.ADMIN_MAIL');

        $mail = Mail::to($details['email'])->cc($admin_mail)->send(new DocumentExpiryMail($details));
    }
}
