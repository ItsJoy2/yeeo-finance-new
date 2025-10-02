<?php

namespace App\Http\Controllers\api;

use App\Models\Club;
use App\Models\User;
use App\Models\Founder;
use App\Models\Nominee;
use App\Models\Investor;
use App\Service\UserService;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use function Pest\Laravel\json;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService){$this->userService = $userService;}

    public function UserProfile(Request $request):JsonResponse
    {
        return $this->userService->UserProfile($request);
    }

    public function team(Request $request): JsonResponse
    {
        $user = $request->user();
        $team = $this->getTeamRecursive($user);
        return response()->json([
            'status' => true,
            'user' => $user->only(['email','name','is_founder','created_at']),
            'team' => $team
        ]);
    }



public function getDirectReferrals(Request $request): JsonResponse
{
    $user = $request->user();
    $directReferrals = $user->referrals()
        ->select('users.id', 'users.name', 'users.refer_by', 'users.email','users.is_founder','users.created_at')
        ->selectRaw('COALESCE(SUM(founders.investment), 0) as investment')
        ->leftJoin('founders', 'founders.user_id', '=', 'users.id')
        ->groupBy('users.id', 'users.name', 'users.refer_by', 'users.email','users.is_founder','users.created_at')
        ->paginate(10);

    return response()->json([
        'status' => true,
        'data' => $directReferrals->items(),
        'total' => $directReferrals->total(),
        'per_page' => $directReferrals->perPage(),
        'page' => $directReferrals->currentPage(),
        'current_page' => $directReferrals->currentPage(),
        'last_page' => $directReferrals->lastPage(),
        'from' => $directReferrals->firstItem(),
    ]);
}

    private function getTeamRecursive(User $user, int $level = 1, int $maxLevel = 3): array
    {
        if ($level > $maxLevel) {
            return [];
        }
        $user->load('referrals');
        $team = [];
        foreach ($user->referrals as $referral) {
            $team[] = [
                'level' => $level,
                'email' => $referral->email,
                'name' => $referral->name,
                'is_active' => $referral->is_active,
                'created_at' => $referral->created_at,
                'investment' => Founder::where('user_id', $referral->id)->sum('investment'),
                'team' => $this->getTeamRecursive($referral, $level + 1, $maxLevel)
            ];
        }
        return $team;
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

    public function generalSettings()
    {
        $settings = GeneralSetting::first();

        if (!$settings) {
            $settings = [
                'app_name' => null,
                'logo' => null,
                'favicon' => null,
                'total_founder' => 0,
                'available_founder_slot' => 0,
            ];
        } else {
            $settings = $settings->toArray();

            unset($settings['id'], $settings['created_at'], $settings['updated_at']);

            $settings['logo'] = $settings['logo'] ? asset('storage/' . $settings['logo']) : null;
            $settings['favicon'] = $settings['favicon'] ? asset('storage/' . $settings['favicon']) : null;
        }

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function nominee()
    {
        $user = Auth::user();
        $nominee = $user->nominee;

        if ($nominee) {
            $nominee->image = $nominee->image ? url('storage/' . $nominee->image): null;
        }

        return response()->json([
            'status' => true,
            'data' => $nominee
        ]);
    }
    public function nomineeUpdate(Request $request)
    {
        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        // ✅ Validation
        $request->validate([
            'name' => 'nullable|string|max:255',
            'date_of_birth' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($today, $tomorrow) {
                    if ($value === $today) {
                        $fail('Today date is not allowed as Nominee Date of Birth.');
                    }
                    if ($value === $tomorrow) {
                        $fail('Tomorrow date is not allowed as Nominee Date of Birth.');
                    }
                },
            ],
            'national_id' => 'nullable|string|max:50',
            'relationship' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'image' => 'nullable|image|max:2048',
        ]);

        // ✅ Ensure Authenticated
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ✅ Debugging (optional - remove later)
        // \Log::info('Nominee request data', $request->all());

        // ✅ Create or Update Nominee
        $nominee = Nominee::updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'name',
                'date_of_birth',
                'national_id',
                'relationship',
                'contact_number',
            ])
        );

        // ✅ Handle Image
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('nominees', 'public');
            $nominee->image = $path;
            $nominee->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Nominee updated successfully.',
            'nominee' => $nominee
        ]);
    }




}
