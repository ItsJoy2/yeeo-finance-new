@extends('admin.layouts.app')

@section('content')
    {{-- SweetAlert success message --}}
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Investment Plans</h4>
            <a href="{{ route('admin.plans.create') }}" class="btn btn-success btn-sm">+ Add New Package</a>
        </div>

        <div class="card-body table-responsive">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <select name="filter" class="form-control">
                            <option value="">-- Filter Packages --</option>
                            <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">Filter</button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-striped table-hover mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Plan Name</th>
                        <th>Trading Pair</th>
                        <th>Investment Range</th>
                        <th>PNL Return</th>
                        <th>PNL Bonus</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($plans as $index => $plan)
                        <tr>
                            <td>{{ $index + $plans->firstItem() }}</td>
                            <td>
                                @if($plan->image)
                                    <img src="{{ asset('storage/' . $plan->image) }}" alt="Plan Image" style="width:40px; height:40px; object-fit:cover; border-radius:5px;">
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $plan->plan_name }}</td>
                            <td>{{ $plan->category ? $plan->category->name : 'No Category' }} Pair</td>
                            <td>${{ number_format($plan->min_investment, 2) }} - ${{ number_format($plan->max_investment, 2) }}</td>
                            <td>{{ number_format($plan->pnl_return, 2) }}%</td>
                            <td>{{ number_format($plan->pnl_bonus, 2) }}</td>
                            <td>
                                <span class="badge {{ $plan->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-info">Edit</a>
                                {{-- delete button --}}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No packages found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $plans->links('admin.layouts.partials.__pagination') }}
            </div>
        </div>
    </div>
@endsection
