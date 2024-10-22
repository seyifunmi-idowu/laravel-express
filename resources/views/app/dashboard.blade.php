@extends('app.base')

@section('content')

    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Orders</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $total_orders }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Today's Orders</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $today_orders }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!--        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">-->
<!--            <div class="card">-->
<!--                <div class="card-body p-3">-->
<!--                    <div class="row">-->
<!--                        <div class="col-8">-->
<!--                            <div class="numbers">-->
<!--                                <p class="text-sm mb-0 text-capitalize font-weight-bold"></p>-->
<!--                                <h5 class="font-weight-bolder mb-0">-->
<!--                                    +3,462-->
<!--                                    <span class="text-danger text-sm font-weight-bolder">-2%</span>-->
<!--                                </h5>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-4 text-end">-->
<!--                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">-->
<!--                                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Wallet</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $wallet_balance }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-credit-card text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mt-4">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Latest Orders</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                      <tr>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order id</th>
                          <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Rider</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                          <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                          <th class="text-secondary opacity-7"></th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($orders as $order)
                      <tr>
                          <td>
                              <div class="d-flex px-2 py-1">
                                  <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-sm">{{ $order->order_id }}</h6>
                                  </div>
                              </div>
                          </td>
                          <td>
                              <p class="text-xs font-weight-bold mb-0">{{ $order->rider->user->display_name ??null }}</p>
                              <p class="text-xs text-secondary mb-0">{{ $order->rider->vehicle_plate_number ?? null}}</p>
                          </td>
                          <td class="align-middle text-center text-sm">
                              <p class="text-sm font-weight-bold mb-0">₦{{ $order->total_amount }}</p>
                          </td>
                          <td class="align-middle text-center">
                              <div class="d-flex align-items-center justify-content-center">
                                  <div>
                                      <div class="progress">
                                          <div class="progress-bar bg-gradient-success"
                                              role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                              style="width: {{ $order->getOrderProgress() }}%;"></div>
                                      </div>
                                  </div>
                              </div>
                          </td>
                          <td class="align-middle">
                              <a href="{{ route('business-view-order', ['order_id' => $order->id]) }}"
                                  class="text-secondary font-weight-bold text-xs" data-toggle="tooltip"
                                  data-original-title="View order">
                                  View
                              </a>
                          </td>
                      </tr>
                      @endforeach
                  </tbody>
              </table>
              
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mt-4">
          <div class="card h-100 mb-4">
            <div class="card-header pb-0 px-3">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="mb-0">Your Transactions</h6>
                </div>
                <div class="card-header pb-0 p-3">
            </div>
              </div>
            </div>
            <div class="card-body pt-4 p-3">
              <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Latest</h6>
              <ul class="list-group">
                  @foreach ($transactions as $transaction)
                  <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                      <div class="d-flex align-items-center">
                          @if ($transaction->transaction_status == "SUCCESS")
                          <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                              @if ($transaction->transaction_type == "CREDIT")
                              <i class="fas fa-arrow-up"></i>
                              @else
                              <i class="fas fa-arrow-down"></i>
                              @endif
                          </button>
                          @elseif ($transaction->transaction_status == "FAILED")
                          <button class="btn btn-icon-only btn-rounded btn-outline-danger mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                              @if ($transaction->transaction_type == "CREDIT")
                              <i class="fas fa-arrow-up"></i>
                              @else
                              <i class="fas fa-arrow-down"></i>
                              @endif
                          </button>
                          @elseif ($transaction->transaction_status == "PENDING")
                          <button class="btn btn-icon-only btn-rounded btn-outline-dark mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                              @if ($transaction->transaction_type == "CREDIT")
                              <i class="fas fa-arrow-up"></i>
                              @else
                              <i class="fas fa-arrow-down"></i>
                              @endif
                          </button>
                          @endif
                          <div class="d-flex flex-column">
                              <h6 class="mb-1 text-dark text-sm">{{ $transaction->reference }}</h6>
                              <span class="text-xs">{{ $transaction->created_at }}</span>
                          </div>
                      </div>
          
                      @if ($transaction->transaction_status == "SUCCESS")
                      <div class="d-flex align-items-center text-success text-gradient text-sm font-weight-bold">
                          {{ $transaction->transaction_status }}
                          <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">
                              @if ($transaction->transaction_type == "CREDIT")
                              + ₦{{ $transaction->amount }}
                              @else
                              - ₦{{ $transaction->amount }}
                              @endif
                          </button>
                      </div>
                      @elseif ($transaction->transaction_status == "PENDING")
                      <div class="d-flex align-items-center text-dark text-gradient text-sm font-weight-bold">
                          {{ $transaction->transaction_status }}
                          <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">
                              @if ($transaction->transaction_type == "CREDIT")
                              + ₦{{ $transaction->amount }}
                              @else
                              - ₦{{ $transaction->amount }}
                              @endif
                          </button>
                      </div>
                      @elseif ($transaction->transaction_status == "FAILED")
                      <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                          {{ $transaction->transaction_status }}
                          <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">
                              @if ($transaction->transaction_type == "CREDIT")
                              + ₦{{ $transaction->amount }}
                              @else
                              - ₦{{ $transaction->amount }}
                              @endif
                          </button>
                      </div>
                      @endif
                  </li>
                  @endforeach
              </ul>
              {{-- <ul class="pagination justify-content-center mt-4">
                  @if ($transactions->hasPreviousPage())
                  <li class="page-item">
                      <a class="page-link" href="{{ $transactions->url(1) }}">Ft</a>
                  </li>
                  <li class="page-item">
                      <a class="page-link" href="{{ $transactions->previousPageUrl() }}">Pr</a>
                  </li>
                  @endif
                  @foreach ($transactions->paginator->getPageRange() as $num)
                  <li class="page-item @if ($num == $transactions->currentPage()) active @endif">
                      <a class="page-link" href="{{ $transactions->url($num) }}">{{ $num }}</a>
                  </li>
                  @endforeach
                  @if ($transactions->hasNextPage())
                  <li class="page-item">
                      <a class="page-link" href="{{ $transactions->nextPageUrl() }}">Nx</a>
                  </li>
                  <li class="page-item">
                      <a class="page-link" href="{{ $transactions->url($transactions->lastPage()) }}">Lt</a>
                  </li>
                  @endif
              </ul> --}}
          </div>
                    </div>
        </div>
      </div>

@endsection
