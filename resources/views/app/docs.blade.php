<!DOCTYPE html>
<html lang="en">
<head>
    <title>Fele Express Business Documentation</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <!-- FontAwesome JS -->
    <script defer src="{% static 'docs/assets/fontawesome/js/all.js' %}"></script>

    <!-- Global CSS -->
    <link href="{{ asset('docs/assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Plugins CSS -->
    <link href="{{ asset('docs/assets/plugins/simplelightbox/simple-lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('docs/assets/plugins/elegant_font/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('docs/assets/plugins/prism/prism.css') }}" rel="stylesheet">

    <!-- Theme CSS -->
    <link href="{{ asset('docs/assets/css/styles.css') }}" rel="stylesheet">

</head>

<body class="body-green">
    <div class="page-wrapper">
        <!-- ******Header****** -->
        <header id="header" class="header">
            <div class="container">
                <div class="branding">
                    <h1 class="logo">
                        <a href="index.html">
                            <span class="text-highlight">Fele Express Business</span><span class="text-bold">Docs</span>
                        </a>
                    </h1>

                </div><!--//branding-->

                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('business-dashboard') }}">Home</a></li>

                    <li class="breadcrumb-item active">Quick Start</li>
                </ol>

            </div><!--//container-->
        </header><!--//header-->
        <div class="doc-wrapper">
            <div class="container">
                <div id="doc-header" class="doc-header text-center">
                    <h1 class="doc-title"><i class="icon fa fa-paper-plane"></i> Quick Start</h1>
                    <div class="meta"><i class="far fa-clock"></i> Last updated: July 6th 2024</div>
                </div><!--//doc-header-->
                <div class="doc-body row">
                    <div class="doc-content col-md-9 col-12 order-1">
                        <div class="content-inner">
                            <section id="introduction" class="doc-section">
                                <h2 class="section-title">Introduction</h2>
                                <div class="section-block">
                                    <p>Welcome to the API documentation for our service! This documentation provides comprehensive information about the endpoints available in our system. To access the endpoints, you will need a valid bearer token, which can be obtained from your profile settings after authentication.</p>
                                    <a href="#" class="btn btn-green" target="_blank"><i class="fas fa-download"></i>Download PostmanDoc</a>
                                </div>
                                <div id="authentication"  class="section-block">
                                    <h3 class="block-title">Authentication</h3>
                                    <p>All endpoints in this API require authentication using a bearer token. Please ensure that you include the token in the request header for each API call</p>
                                    <div class="code-block">
                                        <h6>Base Url:</h6>
                                        <p><code> `{{ $base_url }}` </code></p>
                                    </div><!--//code-block-->
                                </div><!--//section-block-->
                            </section><!--//doc-section-->

                            <section id="search-address" class="doc-section">
                                <h2 class="section-title">Search Address</h2>
                                <div class="section-block">
                                    <p>
                                        Search for an address. To get the correct longitude nd latitude of a location.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`POST /api/v1/maps/address-info` </code></p>
                                    </div><!--//code-block-->
                                    <div id="search-address-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{
    "address": "Smile View Hotel",
}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">address</th>
                                                <td>object</td>
                                                <td>required</td>
                                                <td>The location address</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--//table-responsive-->
                                <div id="search-address-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="search-address-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "data": [
        {
            "name": "Smile View Hotel Extension",
            "latitude": 7.707562899999999,
            "longitude": 8.5194119,
            "formatted_address": "PG59+2QR, Father Hunter Street, High Level, Asuir 970101, Benue, Nigeria"
        },
        {
            "name": "Smile View Hotel Anex",
            "latitude": 7.714780199999999,
            "longitude": 8.5138892,
            "formatted_address": "PG77+WGG, Township, Makurdi 970101, Benue, Nigeria"
        },
        {
            "name": "Smile View Hotel",
            "latitude": 7.500584899999999,
            "longitude": 9.6063657,
            "formatted_address": "GJ24+6GQ Site Quarters, Zaki Biam 980109, Benue, Nigeria"
        },
        {
            "name": "Smile View Hotel",
            "latitude": 7.166666999999999,
            "longitude": 9.283332999999999,
            "formatted_address": "578M+M88, General Hospital Road, Katsina Ala, Nigeria"
        },
    ],
    "message": "Address information"
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                    <div class="code-block">
                                        <div id="search-address-bad-request-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Bad request: 404</h6>
                                                <pre><code class="language-python">{
    "message": "Cannot locate address"
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="initiate-order" class="doc-section">
                                <h2 class="section-title">Initiate Order</h2>
                                <div class="section-block">
                                    <p>
                                        Initiates a new order with pickup and delivery locations, along with optional stopovers, and calculates the total price for the trip.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`POST /api/v1/order/business` </code></p>
                                    </div><!--//code-block-->
                                    <div id="initiate-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{
  "pickup": {
    "latitude": "6.5358762",
    "longitude": "3.3829932",
    "contact_phone_number": "123456789",
    "contact_name": "John Doe",
    "address_details": "Smile View Hotel Extension"
  },
  "delivery": {
    "latitude": "6.5702086",
    "longitude": "3.3509155",
    "contact_phone_number": "987654321",
    "contact_name": "Jane Doe",
    "address_details": "Lekki toll-gate"
  },
  "vehicle_id": "ABC123"
}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">pickup</th>
                                                <td>object</td>
                                                <td>required</td>
                                                <td>Pick up details</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[latitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Latitude of pick up address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[longitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Longitude of pick up address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[contact_phone_number]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Pick up person (restaurant, vendor etc) phone number</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[contact_name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Pick up person (restaurant, vendor etc) name</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery</th>
                                                <td>object</td>
                                                <td>required</td>
                                                <td>delivery</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[latitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Latitude of delivery address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[longitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Longitude of delivery address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[contact_phone_number]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Phone number of person to be delivered to</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[contact_name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Name of person to deliver to</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">vehicle_id</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>The id of the chosen mode of delivery. See <a href="#available-vehicles">available vehicles</a> to get all available vehicles.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--//table-responsive-->
                                <div id="initiate-order-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="initiate-order-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "data": {
        "order_id": "hco2fdce86",
        "pickup": {
            "latitude": 6.5358762,
            "longitude": 3.3829932,
        },
        "delivery": {
            "latitude": 6.5702086,
            "longitude": 3.3509155,
        },
        "total_price": 4525.733333333334,
    },
    "message": "Order Information",
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                    <div class="code-block">
                                        <div id="initiate-order-bad-request-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Bad request: 404</h6>
                                                <pre><code class="language-python">{
    "message": "Cannot locate address"
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="place-order" class="doc-section">
                                <h2 class="section-title">Place Order</h2>
                                <div class="section-block">
                                    <p>
                                        Initiates a new order with pickup and delivery locations, along with optional stopovers, and calculates the total price for the trip.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`POST /api/v1/order/business/{order_id}/place-order` </code></p>
                                    </div><!--//code-block-->
                                    <div id="place-order-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{
  "note_to_driver": "Please be fast"
}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">note_to_driver</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Add a note for the rider to see</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--//table-responsive-->
                                <div id="place-order-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="place-order-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "data": {
        "order_id": "76v8ll12rn",
        "status": "PENDING",
        "rider": None,
        "pickup": {
            "latitude": "6.4426897",
            "longitude": "3.4652487",
            "address": "7 Fola Osibo Rd, Lekki Phase 1, Lagos 106104, Lagos, Nigeria",
            "contact_name": "Ramoni",
            "contact_phone_number": "+234123456789"
        },
        "delivery": {
            "latitude": "6.518989899999999",
            "longitude": "3.3691391",
            "address": "8 Jibowu St, Jibowu, Lagos 101245, Lagos, Nigeria",
            "contact_name": "Drake",
            "contact_phone_number": "+23498765432"
        },
        "total_amount": "3924.49",
        "tip_amount": null,
        "note_to_driver": "please be fast yeah?",
        "distance": "18.8 km",
        "duration": "28 mins",
        "timeline": [],
        "created_at": "2024-03-25 15:26:25"
    },
    "message": "Order Information"
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="get-order" class="doc-section">
                                <h2 class="section-title">Get Order</h2>
                                <div class="section-block">
                                    <p>
                                        Get all of your orders. Response is paginated.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`GET /api/v1/order/business` </code></p>
                                    </div><!--//code-block-->
                                    <div id="get-order-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div id="get-order-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="get-order-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "count": 1,
    "total_pages": 1,
    "current_page": 1,
    "data": [
        {
            "order_id": "76v8ll12rn",
            "status": "RIDER_PICKED_UP_ORDER",
            "pickup": {
                "address": "7 Fola Osibo Rd, Lekki Phase 1, Lagos 106104, Lagos, Nigeria",
                "time": "2024-March-25 19:06:18"
            },
            "delivery": {
                "address": "8 Jibowu St, Jibowu, Lagos 101245, Lagos, Nigeria",
                "time": null
            },
            "rider": {
                "name": "Messi Messi",
                "contact": "+2348105474514",
                "avatar_url": "https://feleexpress.s3.amazonaws.com/backend-dev/rider_document/passport_photo/7d09c9a2e6774d0f9ba727c6752f14c2.pdf",
                "rating": 2.0,
                "vehicle": "car",
                "vehicle_type": null,
                "vehicle_make": null,
                "vehicle_model": null,
                "vehicle_plate_number": "eky24sky",
                "vehicle_color": "red"
            }
        },
        {
            "order_id": "glfut9cv8s",
            "status": "ORDER_DELIVERED",
            "pickup": {
                "address": "Chicken Republic, Yaba",
                "time": "2024-March-25 19:06:18"
            },
            "delivery": {
                "address": "ICM, Lagos, Nigeria",
                "time": "2024-March-25 19:38:18"
            },
            "rider": {
                "name": "Messi Messi",
                "contact": "+2348105474514",
                "avatar_url": "https://feleexpress.s3.amazonaws.com/backend-dev/rider_document/passport_photo/7d09c9a2e6774d0f9ba727c6752f14c2.pdf",
                "rating": 2.0,
                "vehicle": "car",
                "vehicle_type": null,
                "vehicle_make": null,
                "vehicle_model": null,
                "vehicle_plate_number": "eky24sky",
                "vehicle_color": "red"
            }
        }
    ],
    "message": "Available vehicles",
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="get-single-order" class="doc-section">
                                <h2 class="section-title">Get Single Order</h2>
                                <div class="section-block">
                                    <p>
                                        Get all available vehicles for order.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`GET /api/v1/order/business/{order_id}` </code></p>
                                    </div><!--//code-block-->
                                    <div id="get-single-order-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div id="get-single-order-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="get-single-order-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "data": {
        "order_id": "76v8ll12rn",
        "status": "RIDER_PICKED_UP_ORDER",
        "rider": {
            "name": "Leonel Messi",
            "contact": "+2348105474514",
            "avatar_url": "https://feleexpress.s3.amazonaws.com/backend-dev/rider_document/passport_photo/7d09c9a2e6774d0f9ba727c6752f14c2.pdf",
            "rating": 2.0,
            "vehicle": "car",
            "vehicle_type": null,
            "vehicle_make": null,
            "vehicle_model": null,
            "vehicle_plate_number": "eky24sky",
            "vehicle_color": "red"
        },
        "pickup": {
            "latitude": "6.4426897",
            "longitude": "3.4652487",
            "address": "7 Fola Osibo Rd, Lekki Phase 1, Lagos 106104, Lagos, Nigeria",
            "contact_name": "Ramoni",
            "contact_phone_number": "+234123456789"
        },
        "delivery": {
            "latitude": "6.518989899999999",
            "longitude": "3.3691391",
            "address": "8 Jibowu St, Jibowu, Lagos 101245, Lagos, Nigeria",
            "contact_name": "Drake",
            "contact_phone_number": "+23498765432"
        },
        "total_amount": "3924.49",
        "tip_amount": null,
        "note_to_driver": "please be fast yeah?",
        "distance": "18.8 km",
        "duration": "28 mins",
        "timeline": [
            {
                "status": "RIDER_ACCEPTED_ORDER",
                "proof_url": null,
                "reason": null,
                "date": "2024-03-25 16:59:41"
            },
            {
                "status": "RIDER_AT_PICK_UP",
                "proof_url": null,
                "reason": null,
                "date": "2024-03-25 17:09:29"
            },
            {
                "status": "RIDER_PICKED_UP_ORDER",
                "proof_url": "https://feleexpress.s3.amazonaws.com/backend-dev/order/76v8ll12rn/18c15093b10b47d9a3f8776bc8a3aaaa.pdf",
                "reason": null,
                "date": "2024-03-25 19:06:18"
            }
        ],
        "created_at": "2024-03-25 15:26:25"
    },
    "message": "Order Information"
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="available-vehicles" class="doc-section">
                                <h2 class="section-title">Available Vehicles</h2>
                                <div class="section-block">
                                    <p>
                                        Get all available vehicles for order.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>`GET /api/v1/order/business/available-vehicles` </code></p>
                                    </div><!--//code-block-->
                                    <div id="available-vehicles-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div id="available-vehicles-responses" class="section-block">
                                    <div class="code-block">
                                        <div id="available-vehicles-success-response" class="section-block">
                                            <div class="code-block">
                                                <h6>Success Response: 200</h6>
                                                <pre><code class="language-python">{
    "data": [
        {
            "id": "8c43444ffbd84074889f0d8dd1447234",
            "name": "Cars (Sedan)",
            "note": "Ex. Corolla. For small boxes, cakes, multiple parcels.",
            "file_url": None,
        },
        {
            "id": "e4808d9d30f7409085e7bbc59e2f2153",
            "name": "Motorcycle",
            "note": "For small items such as food, documents and paperbags",
            "file_url": "https://feleexpress.s3.amazonaws.com/backend-dev/available-vehicles/tesla7107aded26d74837b250670564fa1a63.png",
        },
        {
            "id": "dba6af2862574d44bd8e6814f8ba259b",
            "name": "Bus",
            "note": "Ex. HiAce Bus - For multiple boxes or pile of stocks.",
            "file_url": None,
        },
    ],
    "message": "Available vehicles",
}</code></pre>
                                            </div><!--//code-block-->
                                        </div>
                                    </div>
                                </div>

                            </section><!--//doc-section-->
                            <section id="webhook" class="doc-section">
                                <h2 class="section-title">Webhook</h2>
                                <div class="section-block">
                                    <p>
                                        Webhook request.
                                    </p>
                                    <div class="code-block">
                                        <h6>Endpoint:</h6>
                                        <p><code>POST `https://your-webhook-url.org/api/v1/` </code></p>
                                    </div><!--//code-block-->
                                    <div id="webhook-request" class="section-block">
                                    <div class="code-block">
                                        <h6>Request Sample</h6>
                                        <pre><code class="language-python">{
    "order_id": "76v8ll12rn",
    "status": "ORDER_ARRIVED",
    "rider": {
        "name": "Messi Messi",
        "contact": "+2348105474514",
        "avatar_url": "https://feleexpress.s3.amazonaws.com/backend-dev/rider_document/passport_photo/7d09c9a2e6774d0f9ba727c6752f14c2.pdf",
        "rating": 2.0,
        "vehicle": "car",
        "vehicle_type": null,
        "vehicle_make": null,
        "vehicle_model": null,
        "vehicle_plate_number": "eky24sky",
        "vehicle_color": "red"
    },
    "pickup": {
        "latitude": "6.4426897",
        "longitude": "3.4652487",
        "address": "7 Fola Osibo Rd, Lekki Phase 1, Lagos 106104, Lagos, Nigeria",
        "contact_name": "Ramoni",
        "contact_phone_number": "+234123456789"
    },
    "delivery": {
        "latitude": "6.518989899999999",
        "longitude": "3.3691391",
        "address": "8 Jibowu St, Jibowu, Lagos 101245, Lagos, Nigeria",
        "contact_name": "Drake",
        "contact_phone_number": "+23498765432"
    },
    "total_amount": "3924.49",
    "tip_amount": null,
    "note_to_driver": "please be fast yeah?",
    "distance": "18.8 km",
    "duration": "28 mins",
    "timeline": [
        {
            "status": "RIDER_ACCEPTED_ORDER",
            "proof_url": null,
            "reason": null,
            "date": "2024-03-25 16:59:41"
        },
        {
            "status": "RIDER_AT_PICK_UP",
            "proof_url": null,
            "reason": null,
            "date": "2024-03-25 17:09:29"
        },
        {
            "status": "RIDER_PICKED_UP_ORDER",
            "proof_url": "https://feleexpress.s3.amazonaws.com/backend-dev/order/76v8ll12rn/18c15093b10b47d9a3f8776bc8a3aaaa.pdf",
            "reason": null,
            "date": "2024-03-25 19:06:18"
        },
        {
            "status": "ORDER_ARRIVED",
            "proof_url": null,
            "reason": null,
            "date": "2024-04-04 12:43:25"
        }
    ],
    "created_at": "2024-03-25 15:26:25"
}</code></pre>
                                    </div><!--//code-block-->
                                </div>

                                </div><!--//section-block-->
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">order_id</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Unique identifier for the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">status</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>The current status of the order. See <a href="#status">status</a> for possible values.</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider</th>
                                                <td>object</td>
                                                <td>optional</td>
                                                <td>Full name of the rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Full name of the rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[contact]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Phone number of the rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[avatar_url]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>URL to the rider's profile picture</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[rating]</th>
                                                <td>int</td>
                                                <td>optional</td>
                                                <td>Rating of the rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Type of vehicle used by the rider (e.g., car, bike)</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle_type]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Rating of the rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle_make]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Type of the rider's vehicle</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle_model]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Model of the rider's vehicle</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle_plate_number]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>License plate number of the rider's vehicle</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">rider[vehicle_color]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Color of the rider's vehicle</td>
                                            </tr>
                                                                                        <tr>
                                                <th scope="row">pickup</th>
                                                <td>object</td>
                                                <td>required</td>
                                                <td>Pick up details</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[latitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Latitude of pick up address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[longitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Longitude of pick up address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[address]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Address of the pickup location</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[contact_phone_number]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Pick up person (restaurant, vendor etc) phone number</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">pickup[contact_name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Pick up person (restaurant, vendor etc) name</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery</th>
                                                <td>object</td>
                                                <td>required</td>
                                                <td>delivery</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[latitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Latitude of delivery address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[longitude]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Longitude of delivery address</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[longitude]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Address of the delivery location</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[contact_phone_number]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Phone number of person to be delivered to</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[contact_name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Name of person to deliver to</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">delivery[contact_name]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Name of person to deliver to</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">total_amount</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Total amount associated with the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">tip_amount</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Optional tip amount for the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">note_to_driver</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Additional instructions or notes for the rider/driver</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">distance</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Estimated distance for the delivery</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">duration</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Estimated duration for the delivery</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">timeline</th>
                                                <td>list</td>
                                                <td>optional</td>
                                                <td>List of events or status updates related to the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">timeline[status]</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Status of the order at a specific point in time</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">timeline[proof_url]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>URL of the document providing proof related to the order status change. This field is optional and may be included in webhook responses when the order status transitions to `RIDER_PICKED_UP_ORDER` or `ORDER_DELIVERED`</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">timeline[reason]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Reason or explanation for the status update</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">timeline[date]</th>
                                                <td>string</td>
                                                <td>optional</td>
                                                <td>Date and time when the status update occurred</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">date</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Date and time of the webhook request in ISO 8601 format (YYYY-MM-DD HH:MM:SS).</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">created_at</th>
                                                <td>string</td>
                                                <td>required</td>
                                                <td>Date and time when the order was created</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--//table-responsive-->

                            </section><!--//doc-section-->
                            <section id="status" class="doc-section">
                                <h2 class="section-title">Status</h2>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tbody>
                                            <tr>
                                                <th scope="row">PENDING</th>
                                                <td>The order is pending and has not yet been processed</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">PROCESSING_ORDER</th>
                                                <td>The order is currently being processed</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">PENDING_RIDER_CONFIRMATION</th>
                                                <td>The order is awaiting confirmation from the assigned rider</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">RIDER_ACCEPTED_ORDER</th>
                                                <td>The rider has accepted the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">RIDER_AT_PICK_UP</th>
                                                <td>The rider is currently at the pick-up location to collect the order</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">RIDER_PICKED_UP_ORDER</th>
                                                <td>The rider has picked up the order from the pick-up location</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">ORDER_ARRIVED</th>
                                                <td>The order has arrived at the delivery location</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">ORDER_DELIVERED</th>
                                                <td>The order has been successfully delivered to the recipient</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">ORDER_COMPLETED</th>
                                                <td>The order process is completed</td>
                                            </tr>
                                            <tr>
                                                <th scope="row">ORDER_CANCELLED</th>
                                                <td>The order has been cancelled</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div><!--//table-responsive-->

                            </section><!--//doc-section-->
                        </div><!--//content-inner-->
                    </div><!--//doc-content-->
                    <div class="doc-sidebar col-md-3 col-12 order-0 d-none d-md-flex">
                        <div id="doc-nav" class="doc-nav">
	                            <nav id="doc-menu" class="nav doc-menu flex-column sticky">
		                            <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#introduction">Introduction</a>
		                            </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#authentication">Authentication</a>
	                                    </li>
                                    </nav><!--//nav-->

                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#search-address">Search Address</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#search-address-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#search-address-success-response">Response</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#search-address-bad-request-response">Bad Request</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#initiate-order">Initiate Order</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#initiate-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#initiate-order-success-response">Response</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#initiate-order-bad-request-response">Bad Request</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#place-order">Place Order</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#place-order-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#place-order-success-response">Response</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#get-order">Get Order</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#get-order-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#get-order-success-response">Response</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#get-single-order">Get Single Order</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#get-single-order-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#get-single-order-success-response">Response</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#available-vehicles">Available Vehicles</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#available-vehicles-request">Request</a>
	                                    </li>
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#available-vehicles-success-response">Response</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#webhook">Webhook</a>
                                    </li>
                                    <nav class="nav doc-sub-menu nav flex-column">
	                                    <li class="nav-item">
                                            <a class="nav-link scrollto" href="#webhook-request">Request</a>
	                                    </li>
                                    </nav><!--//nav-->
                                    <li class="nav-item">
	                                    <a class="nav-link scrollto" href="#status">Status</a>
                                    </li>
	                            </nav><!--//doc-menu-->

                        </div>
                    </div><!--//doc-sidebar-->
                </div><!--//doc-body-->
            </div><!--//container-->
        </div><!--//doc-wrapper-->

    </div><!--//page-wrapper-->

    <footer id="footer" class="footer text-center">
        <div class="container">
            <!--/* This template is free as long as you keep the footer attribution link. If you'd like to use the template without the attribution link, you can buy the commercial license via our website: themes.3rdwavemedia.com Thank you for your support. :) */-->
            <small class="copyright">Designed with <span class="sr-only">love</span><i class="fas fa-heart"></i> by <a href="https://themes.3rdwavemedia.com/" target="_blank">Xiaoying Riley</a> for developers</small>

        </div><!--//container-->
    </footer><!--//footer-->


    <!-- Main Javascript -->
    <link href="{{ asset('docs/assets/plugins/gumshoe/gumshoe.polyfills.min.js') }}" rel="stylesheet">
    <link href="{{ asset('docs/assets/plugins/stickyfill/dist/stickyfill.min.js') }}" rel="stylesheet">
    <link href="{{ asset('docs/assets/plugins/prism/prism.js') }}" rel="stylesheet">
    <link href="{{ asset('docs/assets/js/main.js') }}" rel="stylesheet">

</body>
</html>
