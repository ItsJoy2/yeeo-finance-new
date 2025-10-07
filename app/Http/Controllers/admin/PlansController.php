<?php

namespace App\Http\Controllers\admin;

use App\Models\Package;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class PlansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = $request->filter;
        $page = $request->get('page', 1);
        $cacheKey = "packages_{$filter}_page_{$page}";

        $plans = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($filter) {
            $query = Package::query();

            if ($filter === 'active') {
                $query->where('status', 'active');
            } elseif ($filter === 'inactive') {
                $query->where('status', 'inactive');
            }

            return $query->paginate(10);
        });

        return view('admin.pages.plan.index', compact('plans'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();

        return view('admin.pages.plan.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'plan_name'        => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'min_investment'   => 'required|numeric|min:0',
            'max_investment'   => 'required|numeric|gte:min_investment',
            'return_type'      => 'required|in:daily,weekly,monthly',
            'duration'         => 'nullable|integer|min:1',
            'pnl_return'       => 'required|numeric|min:0',
            'pnl_bonus'   => 'required|numeric|min:0',
            'status'           => 'required|in:active,inactive',
        ]);

        $data = $request->only([
            'category_id',
            'plan_name',
            'min_investment',
            'max_investment',
            'return_type',
            'duration',
            'pnl_return',
            'pnl_bonus',
            'status'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('plan_images', 'public');
        }

        Package::create($data);

        $this->clearPackageCache();

        return redirect()->route('admin.plans.index')->with('success', 'Plan created successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $plan = Package::findOrFail($id);
        $categories = Category::where('status', 'active')->get();

        return view('admin.pages.plan.edit', compact('plan', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'plan_name'        => 'required|string|max:255',
            'image'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'min_investment'   => 'required|numeric|min:0',
            'max_investment'   => 'required|numeric|gte:min_investment',
            'return_type'      => 'required|in:daily,weekly,monthly',
            'duration'         => 'nullable|integer|min:1',
            'pnl_return'       => 'required|numeric|min:0',
            'pnl_bonus'   => 'required|numeric|min:0',
            'status'           => 'required|in:active,inactive',
        ]);

        $plan = Package::findOrFail($id);

        $data = $request->only([
            'category_id', 'plan_name', 'min_investment', 'max_investment',
            'return_type', 'duration', 'pnl_return', 'pnl_bonus', 'status'
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('plan_images', 'public');
        }

        $plan->update($data);

        $this->clearPackageCache();

        return redirect()->route('admin.plans.index')->with('success', 'Plan updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $items = Package::findorfail($id);
        $items->delete();
        $this->clearPackageCache();
        return back()->with('success', 'Item has been deleted');
    }

    private function clearPackageCache()
    {
        $filters = ['active', 'inactive', null];
        for ($page = 1; $page <= 10; $page++) {
            foreach ($filters as $filter) {
                $key = "packages_{$filter}_page_{$page}";
                Cache::forget($key);
            }
        }
    }

}
