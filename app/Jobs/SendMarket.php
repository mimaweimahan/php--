<?php

namespace App\Jobs;

use App\Needle;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\UserChat;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use function GuzzleHttp\json_encode;

class SendMarket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $marketData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($market_data)
    {
        $this->marketData = $market_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        UserChat::sendText($this->marketData);
    }
}
