<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Member;
use Carbon\Carbon;
use App\Models\User;

class UpdateMemberStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'members:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update expired member statuses from active to expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Update members whose membership has expired
        User::where('end_date', '<', Carbon::today())
        ->where('member_status', 'Active')
        ->update(['member_status' => 'Expired']);
    

        $this->info('Expired members have been updated successfully.');
    }
}
