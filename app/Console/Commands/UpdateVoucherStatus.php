<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateVoucherStatus extends Command
{
    protected $signature = 'vouchers:update-status';
    protected $description = 'Update voucher statuses to expired if the current date is greater than the expired_at date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::today()->toDateString();
    
        DB::table('vouchers')
            ->where('status', 'available')
            ->whereDate('expired_at', '<', $today)
            ->update(['status' => 'expired']);
    
        $this->info('Voucher statuses updated successfully.');
    }
}
