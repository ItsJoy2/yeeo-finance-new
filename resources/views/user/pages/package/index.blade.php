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
                                <img src="{{ asset('public/storage/' . $package->image) }}" alt="Icon" class="package-icon-img">
                                <div class="package-title">{{ $package->plan_name }}</div>
                                <div class="price-tag">${{ $package->min_investment }} - ${{ $package->max_investment }}</div>
                                <hr style="border-top: 1px solid #ccc; margin: 1rem 0;">
                                <div class="stats-container mt-4">
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
                                </div>
                                <!-- Change the button type from submit to button -->
                                <button type="button" class="btn btn-purchase w-100 buy-now-btn"
                                    data-package-id="{{ $package->id }}"
                                    data-package-name="{{ $package->plan_name }}"
                                    data-pnl="{{ $package->pnl_return }}"
                                    data-min="{{ $package->min_investment }}"
                                    data-max="{{ $package->max_investment }}">
                                    Buy Now
                                </button>

                                {{-- <form method="POST" action="{{ route('user.packages.buy') }}">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                                    <input type="hidden" name="amount" class="invest-amount-hidden">
                                    <button type="submit" class="btn btn-purchase w-100">Buy Now</button>
                                </form> --}}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        </div>
    </div>
</div>


<!-- Purchase Confirmation Modal -->
<div class="modal fade" id="purchaseModal" tabindex="-1" aria-labelledby="purchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('user.packages.buy') }}" id="confirmPurchaseForm">
            @csrf
            <input type="hidden" name="package_id" id="modalPackageId">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="purchaseModalLabel">Confirm Plan Invest</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                </div>

                <div class="modal-body">
                    <p><strong>Plan:</strong> <span id="modalPackageName"></span></p>

                    <div class="mb-3">
                        <label for="modalAmountInput" class="form-label">
                            <i class="fas fa-dollar-sign"></i> Invest Amount
                        </label>
                        <input type="number" class="form-control text-white" name="amount" id="modalAmountInput" placeholder="Enter amount">
                        <small class="text-muted" id="modalAmountRange"></small>
                    </div>

                    <div class="mb-3 d-flex gap-2">
                        <label><i class="fas fa-coins"></i> Expected Return:</label>
                        <div><strong>$<span id="modalReturn">0.00</span></strong></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm Invest</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>




@endsection


{{-- <script>
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
</script> --}}

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentPackage = {};

        document.querySelectorAll('.buy-now-btn').forEach(button => {
            button.addEventListener('click', function () {
                // Get package data from button
                const packageId = this.dataset.packageId;
                const packageName = this.dataset.packageName;
                const pnl = parseFloat(this.dataset.pnl);
                const min = parseFloat(this.dataset.min);
                const max = parseFloat(this.dataset.max);

                currentPackage = { packageId, packageName, pnl, min, max };

                // Fill modal fields
                document.getElementById('modalPackageId').value = packageId;
                document.getElementById('modalPackageName').textContent = packageName;
                document.getElementById('modalAmountInput').value = '';
                document.getElementById('modalReturn').textContent = '0.00';
                document.getElementById('modalAmountRange').textContent = `Min: $${min} | Max: $${max}`;

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('purchaseModal'));
                modal.show();
            });
        });

        // Amount input handler inside modal
        const amountInput = document.getElementById('modalAmountInput');
        amountInput.addEventListener('input', function () {
            const amount = parseFloat(this.value);
            const returnDisplay = document.getElementById('modalReturn');

            if (isNaN(amount) || amount < currentPackage.min || amount > currentPackage.max) {
                returnDisplay.textContent = '0.00';
                return;
            }

            const calculatedReturn = (amount * currentPackage.pnl) / 100;
            returnDisplay.textContent = calculatedReturn.toFixed(2);
        });

        // Form submission validation
        const form = document.getElementById('confirmPurchaseForm');
        form.addEventListener('submit', function (e) {
            const amount = parseFloat(amountInput.value);
            if (isNaN(amount) || amount < currentPackage.min || amount > currentPackage.max) {
                e.preventDefault();
                alert(`Please enter a valid amount between $${currentPackage.min} and $${currentPackage.max}`);
            }
        });
    });
</script>

