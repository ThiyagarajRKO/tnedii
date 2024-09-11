

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>
        <meta name="format-detection" content="telephone=no">
        <meta name="apple-mobile-web-app-capable" content="yes">

        <!-- Fonts-->
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" type="text/css">
        <!-- CSS Library-->

        <style>
            :root {
                --color-1st: #7CBA0A;
                --primary-font: 'Roboto', sans-serif;
            }
            
        </style>

        <title>TNEDII</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta property="og:site_name" content="">
        <meta property="og:title" content="">
        <meta property="og:description" content="">
        <meta property="og:url" content="http://tnediimis.com">
        <meta property="og:type" content="article">
        <meta name="twitter:title" content="">
        <meta name="twitter:description" content="">

        <link media="all" type="text/css" rel="stylesheet" href="/vendor/core/plugins/language/css/language-public.css?v=1.0.0">
        <link media="all" type="text/css" rel="stylesheet" href="/themes/edii/plugins/bootstrap/css/bootstrap.min.css">
        <link media="all" type="text/css" rel="stylesheet" href="/themes/edii/plugins/font-awesome/css/font-awesome.min.css">
        <link media="all" type="text/css" rel="stylesheet" href="/themes/edii/plugins/ionicons/css/ionicons.min.css">
        <link media="all" type="text/css" rel="stylesheet" href="/themes/edii/css/style.css?v=5.15">
        <link style="style" media="all" type="text/css" rel="stylesheet" href="/vendor/theme/css/theme_common_style.css?v=1.0.0">
        <link media="all" type="text/css" rel="stylesheet" href="/vendor/core/plugins/crud/css/module_custom_styles.css?v=1.0.0">
    
        <script type="text/javascript">jQuery.noConflict(true);</script>
        <style>
            .card-header {
                background-color: var(--color-1st);
                border-bottom: 1px solid var(--color-1st);
            }

        </style>
    </head>

    <body>
    
        <header class="header" id="header">
            <div class="header-wrap">
                <nav class="nav-top">
                    <div class="container">
                        <div class="pull-left">
                        
                            <div class="header-top-left">
                                <ul class="heading">
                                <li><a id="decfont">A-</a></li>
                                    <li><a id="normfont">A</a></li>
                                    <li><a id="incfont">A+</a></li>
                                    <li><a href="#skip_cont">Skip to main content</a></li>
                                    <li><a href="https://www.editn.in/pages/view/screen-reader-access">Screen Reader Access</a></li>
                                    <li><a href="https://www.editn.in/pages/view/sitemap">Sitemap</a></li>
                                </ul>
                        
                            </div>

                            <div class="hi-icon-wrap hi-icon-effect-3 hi-icon-effect-3a"></div>
                        </div>
                        
                    </div>
                </nav>
            </div>
        </header>
        <header data-sticky="false" data-sticky-checkpoint="200" data-responsive="991" class="page-header page-header--light">
            <div class="container">
                <!-- LOGO-->
                <div class="page-header__center">
                    <a href="http://tnediimis.com" class="page-logo">
                        <img src="http://tnediimis.com/storage/image-1-1-1.png" alt="" >
                    </a>
                </div>
            </div>
                    
        </header>
        <div id="page-wrap">
            <div>
                <section class="section p-30">
                
                    <div class="post-group post-group--single col-md-12 pt-50">
                        
                        <div class="container">
                            <div class="meta-boxes form-actions form-actions-default action-horizontal">
                                <div class="row">  
                                    <div class="col-md-2"></div>                              
                                    <div class="col-md-8">
                                        
                                        @if (isset($error_chk))
                                            <div class = "alert alert-danger">
                                                <ul>
                                                    <li>{{ $error_chk }}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        

                                        <div class="card card-info">
                                            <div class="card-header">
                                                    {{ env('APP_NAME') }}
                                            </div>
                
                                            @if(!isset($error_chk))
                                            <div class="card-body">
                                                    @if($message = Session::get('error'))
                                                    <div class="alert alert-danger alert-dismissible in" role="alert">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <strong>Error!</strong> {{ $message }}
                                                    </div>
                                                    @endif
                                                    @if($payment)
                                                    @if($message = Session::get('success'))
                                                    <div class="alert alert-success alert-dismissible {{ Session::has('success') ? 'show' : 'in' }}" role="alert" id="modalConfirmDelete">
                                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                        <strong>Success!</strong> {{ $message }}
                                                    </div>
                                                    @endif
                                                    @endif
                                                <table class="table table-responsive">
                                                    @if($payment)
                                                    <tr>
                                                        <td><label for="training_name">Payment Hash</label></td>
                                                        <td>{{$payment}}</td>
                                                    </tr>
                                                    @endif
                                                    <tr>
                                                        <td><label for="training_name">Training Name</label></td>
                                                        <td>{{$training->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="training_name">Training Code</label></td>
                                                        <td>{{$training->code}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="training_name">Training Venue</label></td>
                                                        <td>{{$training->venue}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label for="training_name">Training Fee</label></td>
                                                        <td>{{$training->fee_amount}}</td>
                                                    </tr>
                                                </table>


                                                <form action="{{ route('razorpay.payment.store') }}" method="POST" class="text-center" >
                                                    @csrf
                                                    @if(!$payment && !$training->payment_status)
                                                    <script src="https://checkout.razorpay.com/v1/checkout.js"
                                                            data-key="{{ env('RAZORPAY_KEY') }}"
                                                            data-amount="{{ $training->amount }}"
                                                            data-buttontext="Pay {{ $training->amount/100 }} INR"
                                                            data-name="{{ env('APP_NAME') }}"
                                                            data-description="Rozerpay"
                                                            data-image="{{ asset('/storage/editn_logo.png') }}"
                                                            data-prefill.name="{{ $training->candidate_name }}"
                                                            data-prefill.email="{{ $training->candidate_email }}"
                                                            data-prefill.contact="{{ $training->candidate_mobile }}"
                                                            data-notes.annual_action_plan_id="{{ $training->annual_action_plan_id }}"
                                                            data-notes.training_title_id="{{ $training->id }}"
                                                            data-notes.division_id="{{ $training->division_id }}"
                                                            data-notes.financial_year_id="{{ $training->division_id }}"
                                                            data-theme.color="#ff7529">
                                                    </script>
                                                    @endif
                                                    <a href="/admin/training-titles" class="btn btn-warning" name="cancel">Back</a>
                                                </form>
                                            </div>
                                            @else
                                            <div class="text-center">
                                                <a href="/admin/training-titles" class="btn btn-warning" name="cancel">Cancel</a>
                                            </div>
                                            @endif
                                        </div>


                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </section>
            </div>
        </div>

        <p></p>
        <!-- JS Library-->
        <script src="/themes/edii/plugins/jquery/jquery.min.js"></script>
        <script src="/themes/edii/plugins/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(function () {
                $(".close").click(function () { 
                    $("#modalConfirmDelete").hide();
                });

                setTimeout(() => {
                    $(".razorpay-payment-button").addClass('btn btn-success');
                    console.log('called');
                }, 1000);
            });
        </script>
    </body>
</html>
