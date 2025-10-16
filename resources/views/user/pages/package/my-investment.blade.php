 @extends('user.layouts.app')

@section('userContent')

            <div class="page-header">
              <h3 class="page-title">Trading Plans </h3>
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="#">Plans</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Trading Pairs</li>
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
                            <th> Date </th>
                            <th> Trading Pair </th>
                            <th> Trade Amount</th>
                            <th> PNL(roi) </th>
                            <th> Duration </th>
                            <th> Received </th>
                            <th> Status</th>
                          </tr>
                        </thead>
                        <tbody>
                        @forelse ($investors as $investor)
                            <tr>
                            <td>{{ $investor->start_date->format('d M Y') }}</td>
                            <td>{{ $investor->package->category->name ?? 'NO' }} Pair</td>
                            <td>${{ number_format($investor->amount, 2) }}</td>
                            <td>{{ $investor->package->pnl_return }}% ({{ ucfirst($investor->return_type) }})</td>
                            <td>
                                {{ $investor->duration }}
                                {{ $investor->return_type === 'daily' ? 'Days' : 'Months' }}
                            </td>
                            <td>
                                {{ $investor->received_count }}
                            </td>
                            <td>
                                <span   span class="badge badge-{{
                                    $investor->status === 'running' ? 'success' : ($investor->status === 'completed' ? 'primary' : ($investor->status === 'cancelled' ? 'danger' : 'secondary')) }}">

                                {{ ucfirst($investor->status) }}
                                </span>
                            </td>
                            </tr>
                        @empty
                            <tr>
                            <td colspan="6" class="text-center">No investment plans found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                      </table>
                    </div>
                    <div class="mt-3">
                        {{ $investors->links('user.layouts.partials.__pagination') }}
                    </div>
                  </div>
                </div>
             </div>


@endsection
