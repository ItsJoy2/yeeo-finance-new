<?php

namespace App\Http\Controllers\user;

use App\Models\Club;
use App\Models\User;
use App\Models\Founder;
use App\Models\Nominee;
use App\Models\Investor;
use App\Models\Transactions;
use App\Service\UserService;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use function Pest\Laravel\json;
use App\Models\ActivationSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Service\TransactionService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService){$this->userService = $userService;}

    public function UserProfile(Request $request):JsonResponse
    {
        return $this->userService->UserProfile($request);
    }
    public function kyc(Request $request): JsonResponse
    {
        return $this->userService->UserKyc($request);
    }

     public function clubList(): JsonResponse
    {
        $clubs = Club::where('status', 1)
            ->orderBy('required_refers', 'asc')
            ->get()
            ->map(function ($club) {
                return [
                    'id' => $club->id,
                    'name' => $club->name,
                    'Badge' => $club->image ? asset('storage/' . $club->image) : null,
                    'required_refers' => $club->required_refers,
                    'bonus_percent' => $club->bonus_percent,
                    'incentive' => $club->incentive,
                    'incentive_image' => $club->incentive_image ? asset('storage/' . $club->incentive_image) : null,
                    'status' => $club->status ? 'Active' : 'Inactive',
                    'created_at' => $club->created_at,
                    'updated_at' => $club->updated_at,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Club list retrieved successfully',
            'data' => $clubs,
        ]);
    }

    public function showActivation()
    {
        $activationSetting = ActivationSetting::first();
        return view('user.pages.activation.index', compact('activationSetting'));
    }

    public function activeAccount(Request $request)
    {
        $user = $request->user();

        if ($user->is_active) {
            return redirect()->back()->with('error', 'Your account is already active.');
        }

        $settings = ActivationSetting::first();

        if (!$settings || !$settings->activation_amount) {
            return redirect()->back()->with('error', 'Activation settings are not configured.');
        }

        $activationAmount      = $settings->activation_amount;
        $activationBonus       = $settings->activation_bonus ?? 0;
        $referralPercentage    = $settings->referral_bonus ?? 0;
        $referralBonus         = ($activationAmount * $referralPercentage) / 100;


        if ($user->funding_wallet < $activationAmount) {
            return redirect()->back()->with('error', 'Insufficient balance in your funding wallet.');
        }

        DB::transaction(function () use ($user, $activationAmount, $activationBonus, $referralBonus) {
            $user->decrement('funding_wallet', $activationAmount);

            Transactions::create([
                'transaction_id' => Transactions::generateTransactionId(),
                'user_id'        => $user->id,
                'amount'         => $activationAmount,
                'remark'         => 'account_activation',
                'type'           => '-',
                'status'         => 'Paid',
                'details'        => 'Activation amount from funding wallet',
                'charge'         => 0,
            ]);

            $user->update(['is_active' => true]);

            if ($activationBonus > 0) {
                $user->increment('token_wallet', $activationBonus);

                Transactions::create([
                    'transaction_id' => Transactions::generateTransactionId(),
                    'user_id'        => $user->id,
                    'amount'         => $activationBonus,
                    'remark'         => 'account_activation',
                    'type'           => '+',
                    'status'         => 'Paid',
                    'details'        => 'Activation bonus to token wallet',
                    'charge'         => 0,
                ]);
            }

            if ($user->refer_by && $referralBonus > 0) {
                $referrer = User::find($user->refer_by);
                if ($referrer) {
                    $referrer->increment('spot_wallet', $referralBonus);

                    Transactions::create([
                        'transaction_id' => Transactions::generateTransactionId(),
                        'user_id'        => $referrer->id,
                        'amount'         => $referralBonus,
                        'remark'         => 'activation_bonus',
                        'type'           => '+',
                        'status'         => 'Paid',
                        'details'        => "Referral bonus from user ID: {$user->id} activation",
                        'charge'         => 0,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Your account has been successfully activated.');
    }


}
