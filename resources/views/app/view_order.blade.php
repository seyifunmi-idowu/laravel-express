@extends('app.base')

@section('content')

        <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url(' {{ $order->vehicle->file_url }}'); background-position-y: 50%;">
        <span class="mask bg-gradient-primary opacity-6"></span>
      </div>
      <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
        <div class="row gx-4">
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">
                #{{ $order->order_id }}
              </h5>
              <p class="mb-0 font-weight-bold text-sm">
                ₦ {{ $order->total_amount }}
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
            <div class="nav-wrapper position-relative end-0">
              <ul class="nav nav-pills nav-fill p-1 bg-transparent" role="tablist">
                <li class="nav-item">
                  <a class="nav-link mb-0 px-0 py-1 " data-bs-toggle="tab" href="javascript:;" role="tab" aria-selected="false">
                    <span class="ms-1">March 25, 2024, 4:26 p.m</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

       <div class="row py-4">
        <div class="col-12 col-xl-4">
            <div class="card h-100">
              <div class="card-header pb-0 p-3">
                <div class="row">
                  <div class="col-md-8 d-flex align-items-center">
                    <h6 class="mb-0">Order Details</h6>
                  </div>
                </div>
              </div>
              <div class="card-body p-3">
                <ul class="list-group">
                  <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Status:</strong> &nbsp; {{ $order->getStatusDisplay() }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Total Amount:</strong> &nbsp; ₦{{ $order->total_amount }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Distance</strong> &nbsp; {{ $distance }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Duration:</strong> &nbsp; {{ $duration }}</li>
                </ul>
              </div>
            </div>
          </div>
        <div class="col-12 col-xl-4">
            <div class="card h-100">
              <div class="card-header pb-0 p-3">
                <h6 class="mb-0">Order Locations</h6>
              </div>
              <div class="card-body p-3">
                <h6 class="text-uppercase text-body text-xs font-weight-bolder">Pick up</h6>
                <ul class="list-group">
                  <li class="list-group-item border-0 px-0">
                    <div class="form-check form-switch ps-0">
                      <h3 class="form-check-label text-body ms-3 w-80 mb-0" style="font-size">{{ $order->pickup_location }}</h3>
                    </div>
                  </li>
                  <li class="list-group-item border-0 px-0">
                    <div class="form-check form-switch ps-0">
                      <h4 class="form-check-label text-body ms-3 text-truncate w-80 mb-0" >{{ $order->pickup_contact_name }}</h4>
                    </div>
                  </li>
                  <li class="list-group-item border-0 px-0">
                    <div class="form-check form-switch ps-0">
                      <h4 class="form-check-label text-body ms-3 text-truncate w-80 mb-0" >{{ $order->pickup_number }}</h4>
                    </div>
                  </li>
                </ul>
                <h6 class="text-uppercase text-body text-xs font-weight-bolder mt-4">Delivery</h6>
                <ul class="list-group">
                  <li class="list-group-item border-0 px-0">
                    <div class="form-check form-switch ps-0">
                      <h4 class="form-check-label text-body ms-3 text-truncate w-80 mb-0" >{{ $order->delivery_location }}</h4>
                    </div>
                  </li>
                  <li class="list-group-item border-0 px-0">
                    <div class="form-check form-switch ps-0">
                      <h4 class="form-check-label text-body ms-3 text-truncate w-80 mb-0" >{{ $order->delivery_contact_name }}</h4>
                    </div>
                  </li>
                  <li class="list-group-item border-0 px-0 pb-0">
                    <div class="form-check form-switch ps-0">
                      <h4 class="form-check-label text-body ms-3 text-truncate w-80 mb-0" >{{ $order->delivery_number }}</h4>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
         @if ($order->rider)
        <div class="col-12 col-xl-4">
            <div class="card h-100">
              <div class="card-header pb-0 p-3">
                <div class="row">
                  <div class="col-md-8 d-flex align-items-center">
                    <h6 class="mb-0">Rider Information</h6>
                  </div>
                  <div class="col-md-4 text-end">
                    <a href="javascript:;">
                      <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-body p-3">
                <p class="text-sm">
                  {{ $order->rider->rider_info }}
                </p>
                <hr class="horizontal gray-light my-4">
                <ul class="list-group">
                  <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {{ $order->rider->user->display_name }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; {{ $order->rider->user->phone_number }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {{ $order->rider->user->email }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Location:</strong> &nbsp; {{ $order->rider->city }}</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Vehicle:</strong> {{ $order->rider->vehicle->name }}</li>
                </ul>
              </div>
            </div>
          </div>
          @else
        <div class="col-12 col-xl-4">
            <div class="card h-100">
              <div class="card-header pb-0 p-3">
                <div class="row">
                  <div class="col-md-8 d-flex align-items-center">
                    <h6 class="mb-0">Rider Information</h6>
                  </div>
                  <div class="col-md-4 text-end">
                    <a href="javascript:;">
                      <i class="fas fa-user-edit text-secondary text-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Profile"></i>
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-body p-3">
                <p class="text-sm">
                  Pending rider info
                </p>
                <hr class="horizontal gray-light my-4">
                <ul class="list-group">
                  <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; Pending</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; Pending</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; Pending</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Location:</strong> &nbsp; Pending</li>
                  <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Vehicle:</strong> Pending</li>
                </ul>
              </div>
            </div>
          </div>
         @endif
        <div class="col-12 mt-4">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>Orders Timeline</h6>
              <p class="text-sm">
                <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                <span class="font-weight-bold">{{ $order->getOrderProgress() }}%</span> Order progress
              </p>
            </div>
            <div class="card-body p-3">
              <div class="timeline timeline-one-side">
                @foreach ($order->orderTimeline as $timeline)
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="ni ni-{{ $timeline->getStatusIcon() }} text-{{ $timeline->getStatusColour() }} text-gradient"></i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $timeline->getStatusDisplay() }} </h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">{{ $timeline->created_at }}</p>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      @endsection
