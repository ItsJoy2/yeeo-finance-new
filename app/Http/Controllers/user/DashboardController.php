<?php

namespace App\Http\Controllers\user;


use App\Models\User;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class DashboardController extends Controller
{
    public function index() : view
    {
        $user = auth()->user();
        $totalWithdraw = Transactions::where('user_id', $user->id)->where('type', 'withdraw')->sum('amount');
        $totalTransfer = Transactions::where('user_id', $user->id)->where('type', 'transfer')->sum('amount');
        $earningBalance = Transactions::where('user_id', $user->id) ->where('type', '+')->whereIn('remark', ['pnl_bonus', 'activation_bonus', 'trade_bonus', 'daily_pnl'])->sum('amount');
        $dashboard = [
            'totalWithdraw'   => $totalWithdraw,
            'totalTransfer'   => $totalTransfer,
            'earningBalance'  => $earningBalance,
        ];

        return view ('user.pages.dashboard', compact('user', 'dashboard' ));
    }
}
