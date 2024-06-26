<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class CompleteOrders extends Command
{
    protected $signature = 'orders:complete';
    protected $description = 'Complete orders that were accepted an hour after their delivery date';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $oneHourAgo = Carbon::now()->subHour();

        Order::where('status', 'accepted')
            ->where('date_of_delivery', '<=', $oneHourAgo)
            ->update(['status' => 'completed']);

        $this->info('Orders completed successfully.');
    }

}
