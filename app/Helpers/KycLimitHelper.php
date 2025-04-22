<?php

namespace App\Helpers;

use App\Models\KycLimit;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use App\Models\User;

class KycLimitHelper
{
    public static function check(User $user, float $amount)
    {
        if ($user->pnd) {
            return ['status' => false, 'message' => 'This account is on PND.'];
        }

        $limit = KycLimit::where('kyc_level', $user->kyc_level)->first();

        if (!$limit) {
            return ['status' => false, 'message' => 'No limit config for this KYC level.'];
        }

        $today = Carbon::today();
        $startMonth = Carbon::now()->startOfMonth();

        $daily = Transaction::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->where('type', 'debit')
            ->sum('amount');

        $monthly = Transaction::where('user_id', $user->id)
            ->whereBetween('created_at', [$startMonth, now()])
            ->where('type', 'debit')
            ->sum('amount');

        $lifetime = Transaction::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        if ($daily + $amount > $limit->daily_limit) {
            $user->pnd = true;
            $user->save();
            return ['status' => false, 'message' => 'Daily transaction limit exceeded. You have been placed on PND.'];
        }

        if ($monthly + $amount > $limit->monthly_limit) {
            $user->pnd = true;
            $user->save();
            return ['status' => false, 'message' => 'Monthly transaction limit exceeded. You have been placed on PND.'];
        }

        if ($lifetime + $amount > $limit->lifetime_limit) {
            $user->pnd = true;
            $user->save();
            return ['status' => false, 'message' => 'Lifetime transaction limit exceeded. You have been placed on PND.'];
        }

        return ['status' => true];
    }
}
