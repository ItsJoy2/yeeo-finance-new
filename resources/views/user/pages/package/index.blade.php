@extends('user.layouts.app')

@section('userContent')
<div class="page-header">
    <h3 class="page-title">Trading Plans</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.packages') }}">Plans</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trading Plans</li>
        </ol>
    </nav>
</div>

@include('user.layouts.alert')

<div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">

            @foreach($categories as $category)
                <h4 class="mb-4 mt-5 font-weight-bold text-{{ $loop->index == 0 ? 'success' : ($loop->index == 1 ? 'danger' : 'warning') }}">
                    {{ $category->name }}
                </h4>

                <div class="row justify-content-center g-4">
                    @foreach($category->packages as $package)
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="package-card w-100">
                                <img src="{{ $package->image ?? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' }}" alt="Icon" class="package-icon-img">
                                <div class="package-title">{{ $package->plan_name }}</div>
                                <div class="price-tag">${{ $package->min_investment }} - ${{ $package->max_investment }}</div>
                                <hr style="border-top: 1px solid #ccc; margin: 1rem 0;">
                                <div class="stats-container mt-4">
                                    <div class="stat-item">
                                        <label><i class="fas fa-dollar-sign"></i> Invest Amount</label>
                                        <input type="number" name="amount_{{ $package->id }}" class="invest-amount form-control form-control-sm" placeholder="Enter amount" min="{{ $package->min_investment }}" max="{{ $package->max_investment }}" style="width: 120px;" value="{{ old('amount') }}">

                                    </div>
                                    <div class="stat-item">
                                        <span><i class="fas fa-cart-shopping"></i> Return Type</span>
                                        <span>{{ ucfirst($package->return_type) }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span><i class="fas fa-calendar-plus"></i> Duration</span>
                                        <span>{{ $package->duration }} {{ $package->return_type === 'daily' ? 'Days' : 'Months' }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <span><i class="fas fa-clock-rotate-left"></i> PNL Return</span>
                                        <span class="pnl" data-pnl="{{ $package->pnl_return }}">{{ $package->pnl_return }}%</span>
                                    </div>
                                    <div class="stat-item">
                                        <span><i class="fas fa-coins"></i> Return</span>
                                        <span class="calculated-return">$0.00</span>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('user.packages.buy') }}">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                    <input type="hidden" name="amount" class="invest-amount-hidden">
                                    <button type="submit" class="btn btn-purchase w-100">Buy Now</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.package-card').forEach(pkg => {
            const input = pkg.querySelector('.invest-amount');
            const output = pkg.querySelector('.calculated-return');
            const hiddenInput = pkg.querySelector('.invest-amount-hidden');
            const pnlElement = pkg.querySelector('.pnl');
            const pnl = parseFloat(pnlElement.dataset.pnl);

            input.addEventListener('input', () => {
                const amount = parseFloat(input.value);
                if (isNaN(amount) || amount <= 0) {
                    output.textContent = '$0.00';
                    hiddenInput.value = '';
                    return;
                }

                const calculated = (amount * pnl) / 100;
                output.textContent = `$${calculated.toFixed(2)}`;
                hiddenInput.value = amount;
            });
            const form = pkg.querySelector('form');
            form.addEventListener('submit', function (e) {
                const amount = parseFloat(input.value);
                if (isNaN(amount)) {
                    e.preventDefault();
                    alert('Please enter a valid amount');
                    return;
                }
                hiddenInput.value = amount;
            });
        });
    });
</script>

