<?php

namespace App\Console\Commands;

use App\Repositories\Wechat\QrcodeRepository;
use App\Services\QrcodeService;
use Illuminate\Console\Command;

class QRcodeDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:daemon {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle some qrcode daemon';

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
        $method = $this->argument('method');

        return $this->{$method}();
    }

    /**
     * 创建一个二维码
     *
     * @throws \Exception
     */
    public function create()
    {
        QrcodeRepository::self()->forever('school_badge_subscribe')->create();

        $this->info('successful!');
    }
}
