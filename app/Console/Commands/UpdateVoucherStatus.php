<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateVoucherStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vouchers:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update voucher statuses to expired if the current date is greater than the expired_at date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        DB::table('vouchers')
            ->where('status', 'inactive')
            ->whereDate('expired_at', '<', $now)
            ->update(['status' => 'expired']);

        $this->info('Voucher statuses updated successfully.');

        return 0;
    }
}
