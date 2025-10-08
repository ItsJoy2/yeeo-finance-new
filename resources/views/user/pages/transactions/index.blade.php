@extends('user.layouts.app')

@section('userContent')

<div class="page-header">
  <h3 class="page-title">Transactions</h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Finance</a></li>
      <li class="breadcrumb-item active" aria-current="page">Transactions</li>
    </ol>
  </nav>
</div>

<div class="col-lg-12 grid-margin stretch-card">
  <div class="card">
    <div class="card-body">

      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Date</th>
              <th>Remark</th>
              <th>Amount</th>
              <th>Details</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          @forelse ($transactions as $transaction)
              <tr>
                <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}</td>
                <td>{{ ucfirst($transaction->remark) }}</td>
                <td>${{ number_format($transaction->amount, 2) }}</td>
                <td>{{ ucfirst($transaction->details) }}</td>
                <td>
                    @php
                        $status = strtolower($transaction->status);

                        $badgeClass = match($status) {
                            'pending' => 'warning',
                            'paid' => 'primary',
                            'completed' => 'success',
                            'rejected' => 'danger',
                            default => 'secondary',
                        };
                    @endphp

                    <span class="badge badge-{{ $badgeClass }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
          @empty
              <tr>
                <td colspan="5" class="text-center">No transactions found.</td>
              </tr>
          @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-3">
          {{ $transactions->links('user.layouts.partials.__pagination') }}
      </div>
    </div>
  </div>
</div>

@endsection
