<?php

namespace App\Listeners;

use App\Events\ScanUmbrellaCode;
use App\Models\Umbrella;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class QRCodeScanIncrement
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ScanUmbrellaCode  $event
     * @return void
     */
    public function handle(ScanUmbrellaCode $event)
    {
        call_user_func([new self(), $event->type], $event->umbrella);
    }

    public function scan(Umbrella $umbrella)
    {
        $umbrella->scanCountIncrement();
    }

    public function realScan(Umbrella $umbrella)
    {
        $umbrella->realScanCountIncrement();
    }
}
