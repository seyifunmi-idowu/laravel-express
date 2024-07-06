@extends('app.base')

@section('content')

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Orders table</h6>
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
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
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
                            <span class="me-2 text-xs font-weight-bold">{{ $order->getStatusDisplay() }}</span>
                              <div>
                                  <div class="progress">
                                      <div class="progress-bar bg-gradient-success"
                                          role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                          style="width: {{ $order->getOrderProgress() }}%;"></div>
                                  </div>
                              </div>
                          </div>
                      </td>
                      <td class="align-middle text-center">
                        <span class="text-secondary text-xs font-weight-bold">{{ $order->created_at }}</span>
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
      </div>

      @endsection
