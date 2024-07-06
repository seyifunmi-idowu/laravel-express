@extends('app.base')

@section('content')

      <div class="row">
        <div class="col-lg-4">
          <div class="row">
            <div class="col-xl-12">
              <div class="row">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                        <i class="fas fa-credit-card opacity-10"></i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <h6 class="text-center mb-0">Wallet</h6>
                      <span class="text-xs">Wallet Balance</span>
                      <hr class="horizontal dark my-3">
                      <h5 class="mb-0">₦{{ $wallet_balance }}</h5>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header mx-4 p-3 text-center">
                      <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                        <i class="ni ni-money-coins opacity-10"></i>
                      </div>
                    </div>
                    <div class="card-body pt-0 p-3 text-center">
                      <form method="POST" action="{{ route('business-fund-wallet') }}" role="form text-left">
                        @csrf    
                        <span class="text-xs">Enter amount to fund wallet</span>
                        <input type="number" class="form-control" placeholder="2000" name="amount" required>
                        <hr class="horizontal dark my-3">
                        <button type="submit" class="btn bg-gradient-dark mb-0"><i class="fas fa-plus"></i>&nbsp;&nbsp;Fund card</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card h-100">
            <div class="col-md-12 mb-lg-0 mb-4">
              <div class="card mt-4">
                <div class="card-header pb-0 p-3">
                  <div class="row">
                    <div class="col-6 d-flex align-items-center">
                      <h6 class="mb-0">Payment Method</h6>
                    </div>
                  </div>
                </div>
                <div class="card-body p-3">
                  <div class="row">
                    @foreach ($cards as $card)
                    <div class="col-md-6">
                      <div class="card card-body border card-plain border-radius-lg d-flex align-items-center flex-row">
                        @if ($card->brand == "visa")
                        <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/logos/visa.png') }}" alt="{{ $card->brand }}">
                        @else
                        <img class="w-10 me-3 mb-0" src="{{ asset('assets/img/logos/mastercard.png') }}" alt="{{ $card->brand }}">
                        @endif
                        <h6 class="mb-0">****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;****&nbsp;&nbsp;&nbsp;{{ $card->last_4 }}</h6>
                        <a class="btn btn-link text-danger text-gradient px-3 mb-0" href="{{ route('business-delete-card', ['card_id' => $card->id]) }}">
                          <i class="far fa-trash-alt me-2"></i>Delete
                        </a>

                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-7 mt-4">
            <div class="card h-100 mb-4">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Your Transactions</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Newest</h6>
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
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">@if ($transaction->transaction_type == "CREDIT") + @else - @endif ₦{{ $transaction->amount }}</button>
                            </div>
                            @elseif ($transaction->transaction_status == "PENDING")
                            <div class="d-flex align-items-center text-dark text-gradient text-sm font-weight-bold">
                                {{ $transaction->transaction_status }}
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">@if ($transaction->transaction_type == "CREDIT") + @else - @endif ₦{{ $transaction->amount }}</button>
                            </div>
                            @elseif ($transaction->transaction_status == "FAILED")
                            <div class="d-flex align-items-center text-danger text-gradient text-sm font-weight-bold">
                                {{ $transaction->transaction_status }}
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4">@if ($transaction->transaction_type == "CREDIT") + @else - @endif ₦{{ $transaction->amount }}</button>
                            </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
    
                    <ul class="pagination justify-content-center mt-4">
                        @if ($transactions->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link">Ft</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->url(1) }}">Ft</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->previousPageUrl() }}">Pr</a>
                        </li>
                        @endif
    
                        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        <li class="page-item {{ $transactions->currentPage() == $page ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                        @endforeach
    
                        @if ($transactions->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->nextPageUrl() }}">Nx</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="{{ $transactions->url($transactions->lastPage()) }}">Lt</a>
                        </li>
                        @else
                        <li class="page-item disabled">
                            <span class="page-link">Lt</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
            
      </div>


      @endsection
