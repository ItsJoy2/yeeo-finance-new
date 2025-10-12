<?php

namespace App\Http\Controllers\user;

use App\Models\User;
use App\Models\Founder;
use App\Models\Package;
use App\Models\Category;
use App\Models\Investor;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use Illuminate\Support\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Service\TransactionService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PackagesController extends Controller
{
    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index()
    {
        $categories = Category::with(['packages' => function ($q) {
            $q->where('status', 'active');
        }])->where('status', 'active')->get();

        return view('user.pages.package.index', compact('categories'));
    }

    // Buy package and invest
    public function buyPackage(Request $request)
    {
        $request->validate([
            'package_id' => 'required|integer|exists:packages,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();

        if (!$user->is_active) {
            return back()->with('error', 'Your account is not active. You cannot invest.');
        }

        if ($user->is_block) {
            return back()->with('error', 'Your account is blocked. You cannot invest.');
        }

        $package = Package::findOrFail($request->package_id);

        if ($request->amount < $package->min_investment) {
            return back()->with('error', "Minimum investment amount is $ {$package->min_investment}");
        }

        if ($request->amount > $package->max_investment) {
            return back()->with('error', "Maximum investment amount is $ {$package->max_investment}");
        }

        if ($user->funding_wallet < $request->amount) {
            return back()->with('error', 'Insufficient funds in funding wallet');
        }

        DB::beginTransaction();

        try {

            $amount = $request->amount;
            $expectedReturn = round($amount * ($package->pnl_return / 100), 8);
            $startDate = Carbon::now();

            $nextReturnDate = $package->return_type === 'daily'
                ? $startDate->copy()->addDay()
                : $startDate->copy()->addMonth();

            $endDate = null;
            if ((int)$package->duration > 0) {
                $duration = (int) $package->duration;

                $endDate = $package->return_type === 'daily'
                    ? $startDate->copy()->addDays($duration)
                    : $startDate->copy()->addMonths($duration);
            }

            Investor::create([
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $amount,
                'expected_return' => $expectedReturn,
                'return_type' => $package->return_type,
                'duration' => $package->duration,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate ? $endDate->toDateString() : null,
                'next_return_date' => $nextReturnDate->toDateString(),
                'received_count' => 0,
                'status' => 'running',
            ]);

            $user->decrement('funding_wallet', $amount);

            Transactions::create([
                'transaction_id' => Transactions::generateTransactionId(),
                'user_id' => $user->id,
                'amount' => $amount,
                'remark' => "package_purchased",
                'type' => '-',
                'status' => 'Paid',
                'details' => "Invested in plan: {$package->plan_name}",
                'charge' => 0,
            ]);

            $generalSetting = GeneralSetting::first();
            $referralBonusPercent = $generalSetting->referral_bonus;
            if ($referralBonusPercent > 0 && $user->refer_by) {
                $referrer = User::find($user->refer_by);
                if ($referrer) {
                    $bonusAmount = round($amount * ($referralBonusPercent / 100), 5);
                    $referrer->increment('spot_wallet', $bonusAmount);

                    Transactions::create([
                        'transaction_id' => Transactions::generateTransactionId(),
                        'user_id' => $referrer->id,
                        'amount' => $bonusAmount,
                        'remark' => "trade_bonus",
                        'type' => '+',
                        'status' => 'Paid',
                        'details' => "Referral bonus from {$user->name}'s investment",
                        'charge' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('user.packages')
                ->with('success', 'Investment successful');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors('Investment failed: ' . $e->getMessage());
        }
    }

    public function InvestHistory()
     {
        $investors = auth()->user()
        ->investors()
        ->with('package.category')
        ->latest()
        ->paginate(5);
        return view('user.pages.package.my-investment', compact('investors'));
    }

}
