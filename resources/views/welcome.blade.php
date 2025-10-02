<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>{{env('APP_NAME')}}</title>
    <meta content="" name="description">

    <meta name="keywords" content="{{env('APP_NAME')}}, Bitcoin Zambia, Lightning Network, crypto payments, pay bills with Bitcoin, send Bitcoin Zambia, ZESCO Bitcoin payment, DStv Bitcoin, Lusaka Water Bitcoin, mobile money crypto, Zambia blockchain payments">
    <meta name="author" content="MACROIT INFORMATION TECHNOLOGY">

    <!-- Favicons -->
    <link href="{{asset('ui/css/assets/img/fav.png')}}" rel="icon">
    <link href="{{asset('ui/css/assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{asset('ui/css/assets/vendor/aos/aos.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
    <link href="{{asset('ui/css/assets/css/style.css')}}" rel="stylesheet">
    
    <!-- Additional Mobile Responsive CSS -->
    <style>
        /* Mobile Navigation Improvements */
        @media (max-width: 991px) {
            .navbar ul {
                padding: 10px 0;
            }
            
            .navbar ul li {
                display: block;
                width: 100%;
                text-align: center;
                margin: 5px 0;
            }
            
            .getstarted {
                margin: 10px 0 !important;
                display: block;
                width: 100%;
            }
        }
        
        /* Feature Section Improvements */
        .feature .section-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
        }
        
        @media (max-width: 768px) {
            .feature .section-row {
                flex-direction: column;
                text-align: center;
            }
            
            .feature .section-row > div {
                width: 100% !important;
                margin-bottom: 30px;
            }
            
            .feature .section-row img {
                max-width: 100%;
                height: auto;
            }
        }
        
        /* Hero Section Improvements */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .hero h2 {
                font-size: 1.3rem;
            }
            
            .hero-img {
                text-align: center;
                margin-top: 30px;
            }
        }
        
        /* Contact Form Fix */
        .contact form .row.gy-4 {
            margin-bottom: 0;
        }
        
        /* Footer Improvements */
        @media (max-width: 768px) {
            .footer-top .row > div {
                margin-bottom: 30px;
                text-align: center;
            }
            
            .footer-contact {
                text-align: center !important;
            }
        }
        
        /* General Mobile Improvements */
        @media (max-width: 576px) {
            .container, .container-fluid {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .btn-get-started {
                width: 100%;
                justify-content: center;
            }
            
            .values .box {
                margin-bottom: 30px;
            }
        }
    </style>
</head>

<body>
@include('sweetalert::alert')

    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top">
        <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

            <a href="index.html" class="logo d-flex align-items-center">
                <img src="{{asset('ui/css/assets/img/logo.png')}}" alt="Logo" style="border-radius: 100%; max-height: 40px;">
                <span style="color:#333; font-size: 20px;font-weight: bolder;">{{env('APP_NAME')}}</span>
            </a>

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Home</a></li>
                    <li><a class="nav-link scrollto" href="#services">Services</a></li>
                    <li><a class="nav-link scrollto" href="#contact">Contact</a></li>
                    <li><a class="getstarted scrollto" href="{{ 'customer/register' }}">Sign up</a></li>
                    <li><a class="getstarted scrollto" href="{{ 'customer/login' }}">Sign in</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav><!-- .navbar -->

        </div>
    </header><!-- End Header -->

    <!-- ======= Hero Section ======= -->
    <section id="hero" class="hero d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up">Bitcoin to Kwacha instantly using {{env('APP_NAME')}}.</h1>
                    <h2 data-aos="fade-up" data-aos-delay="400">Pay bills in kwacha, using BitCoin.</h2>
                    <br>
                    <h5 data-aos="fade-up" data-aos-delay="400">Buy Zesco units, or pay for DSTV, GoTv, Lusaka Water in the comfort of your home using bitcoin on our platform</h5>
                    <div data-aos="fade-up" data-aos-delay="600">
                        <div class="text-center text-lg-start">
                            <a href="{{ 'customer/register' }}" class="btn-get-started scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                <span>Get Started</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-img" data-aos="zoom-out" data-aos-delay="200">
                    <img src="{{asset('ui/css/assets/img/hero.png')}}" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </section><!-- End Hero -->

    <main id="main">
        <!-- ======= Values Section ======= -->
        <section id="services" class="values">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Services</h2>
                </header>

                <div class="row">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="box">
                            <img src="{{asset('ui/css/assets/img/receive.png')}}" class="img-fluid" alt="">
                            <h3>Send Money</h3>
                            <p>Easily send money to anyone, anywhere in Zambia.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
                        <div class="box">
                            <img src="{{asset('ui/css/assets/img/loan.png')}}" class="img-fluid" alt="">
                            <h3>Pay Bill</h3>
                            <p>Pay for water using bitcoin on our self service portal.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="600">
                        <div class="box">
                            <img src="{{asset('ui/css/assets/img/send.png')}}" class="img-fluid" alt="">
                            <h3>Other Utilities</h3>
                            <p>Paying for DSTV, GoTv, or Zesco units? It's done with few clicks.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section><!-- End Values Section -->

        <!-- ======= Features ======= -->
        <section class="feature">
            <div class="container">
                <div class="section-row">
                    <div class="col-lg-6 col-md-12">
                        <h3>Ready made checkout interfaces</h3>
                        <br>
                        <p>Easily generate your lightning invoice on our portal. Copy the invoice address or Scan the QRcode on our platform using any lighting mobile/web application.
                        Confirm payments and Receive the money or bills purchased instantly.
                        This streamlined approach removes the complexity of converting Bitcoin into local currency yourself we handle it automatically.</p>
                    </div>
                    <div class="col-lg-6 col-md-12 text-center">
                        <img src="{{asset('ui/css/assets/img/phone.png')}}" class="img-fluid" style="max-width: 232px; height: auto;">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="feature">
            <div class="container">
                <div class="section-row">
                    <div class="col-lg-6 col-md-12 text-center">
                        <img src="{{asset('ui/css/assets/img/happy.jpg')}}" class="img-fluid">
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <h3>Pay Your Bills</h3>
                        <p>
                            With {{env('APP_NAME')}}, paying your everyday bills using Bitcoin is quick and hassle-free.
                            Whether it's your ZESCO electricity, Lusaka Water, DStv subscription, or other essential services,
                            our platform brings them all together in one convenient place.<br><br>
                            Simply enter your payment details, generate a Lightning invoice, and settle your bill directly from your Bitcoin wallet
                            no manual currency conversions or complicated steps required.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="feature">
            <div class="container">
                <div class="section-row">
                    <div class="col-lg-6 col-md-12">
                        <h3>Easy to integrate into your website or app</h3>
                        <p>We offer seamless bill payments with the {{env('APP_NAME')}} APIs. Add ZESCO, DStv, and more to your platform in minutes.</p>
                        <p> Whether you're running an e-commerce store, a fintech service, or a community platform
                            {{env('APP_NAME')}} empowers you to expand your payment options and attract customers who prefer using Bitcoin.<br><br>
                            With clear documentation, sample code, and dedicated support, integration is smooth and straightforward.
                            Start offering fast, reliable bill payments today and give your users a modern, crypto-powered payment experience.
                        </p>
                    </div>
                    <div class="col-lg-6 col-md-12 text-center">
                        <img src="{{asset('ui/css/assets/img/programmer.jpg')}}" class="img-fluid">
                    </div>
                </div>
            </div>
        </section>

        <!-- ======= Contact Section ======= -->
        <section id="contact" class="contact">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Contact</h2>
                    <p>Contact Us</p>
                </header>

                <div class="row gy-4">
                    <div class="col-lg-6">
                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-geo-alt"></i>
                                    <h3>Address</h3>
                                    <p>Olympia, 14 Zambezi road,<br>Lusaka, LSK 10101</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-telephone"></i>
                                    <h3>Call Us</h3>
                                    <p>+260 769891754.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-envelope"></i>
                                    <h3>Email Us</h3>
                                    <p>{{env('APP_NAME')}}@macroit.org</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-clock"></i>
                                    <h3>Open Hours</h3>
                                    <p>Monday - Friday<br>9:00AM - 05:00PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <form action="{{route('contactUs')}}" method="post" class="php-email-form">
                            @csrf
                            <div class="row gy-4">
                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                                </div>

                                <div class="col-md-6">
                                    <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                                </div>

                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                                </div>

                                <div class="col-md-12">
                                    <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="loading">Loading</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Your message has been sent. Thank you!</div>

                                    <button type="submit" class="btn btn-primary">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section><!-- End Contact Section -->
    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    <footer id="footer" class="footer">
        <div class="footer-newsletter">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 text-center">
                        <h4>Our Newsletter</h4>
                        <p>Don't miss important updates from us.</p>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{route('emailSubscription')}}" method="post">
                            @csrf
                            <input type="email" name="email" class="form-control" placeholder="Your email" required>
                            <input type="submit" class="btn btn-primary mt-2" value="Subscribe">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-top">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-12 footer-info">
                        <a href="index.html" class="logo d-flex align-items-center">
                            <img src="{{asset('css/assets/img/logo.png')}}" alt="{{env('APP_NAME')}}" class="img-fluid">
                            <span>{{env('APP_NAME')}}</span>
                        </a>
                        <p>Simplifying payments in Zambia.</p>
                        <div class="social-links mt-3">
                            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-6 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Home</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">About us</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Services</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Terms of service</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Privacy policy</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-2 col-6 footer-links">
                        <h4>Our Services</h4>
                        <ul>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Online checkout</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Bill Payments</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Money transfer</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
                        <h4>Contact Us</h4>
                        <p>
                            Downtown mall <br>
                            Lusaka, LSK 10101<br>
                            Zambia <br><br>
                            <strong>Phone:</strong> +260 769891754<br>
                            <strong>Email:</strong> bitkwik@macroit.org<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="copyright">
                &copy; Copyright <strong><span>{{env('APP_NAME')}}</span></strong>. All Rights Reserved
            </div>
        </div>
    </footer><!-- End Footer -->

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{asset('ui/css/assets/vendor/purecounter/purecounter.js')}}"></script>
    <script src="{{asset('ui/css/assets/vendor/aos/aos.js')}}"></script>
    <script src="{{asset('ui/css/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('ui/css/assets/vendor/glightbox/js/glightbox.min.js')}}"></script>
    <script src="{{asset('ui/css/assets/vendor/isotope-layout/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('ui/css/assets/vendor/swiper/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('css/assets/vendor/php-email-form/validate.js')}}"></script>

    <!-- Template Main JS File -->
    <script src="{{asset('ui/css/assets/js/main.js')}}"></script>

</body>

</html>