<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->filter;
        $search = $request->search;

        $query = User::query()->where('role', 'user')->withSum('founder', 'investment');

        switch ($filter) {
            case 'founder':
                $query->where('is_founder', 1);
                break;
            case 'member':
                $query->where('is_founder', 0);
                break;
            case 'blocked':
                $query->where('is_block', 1);
                break;
            case 'unblocked':
                $query->where('is_block', 0);
                break;
        }

        if (!empty($search)) {
            $query->where('email', 'like', '%' . $search . '%');
        }

        $users = $query->orderByDesc('id')->paginate(10);

        return view('admin.pages.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['founder', 'clubs', 'nominee', 'referredBy', 'referrals'])->findOrFail($id);

        return view('admin.pages.users.show', compact('user'));
    }
public function update(Request $request)
{
    $user = User::findOrFail($request->user_id);

    $validated = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
        'mobile'   => 'required|string|max:20',
        'is_block' => 'required|boolean',
    ]);

    $user->name     = $request->name;
    $user->email    = $request->email;
    $user->mobile   = $request->mobile;
    $user->is_block = $request->is_block;

    $user->save();

    return redirect()->back()->with('success', 'User updated successfully!');
}

}
