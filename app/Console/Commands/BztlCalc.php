<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\BztlTransaction;
class BztlCalc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BztlCalc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '搬砖套利结算';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            echo '开始执行搬砖套利结算----->'.date('Y-m-d H:i:s').PHP_EOL.PHP_EOL;
            BztlTransaction::calcWealth();
            echo '结算执行搬砖套利结算----->'.date('Y-m-d H:i:s').PHP_EOL.PHP_EOL;
        }catch(\Throwable $e){
            echo '结算异常----->'.$e->getMessage().PHP_EOL;
        }
    }

}
