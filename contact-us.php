<?php include 'include/url.inc.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <!-- Basic Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Page Title -->
    <title>Contact CloudFlox — Hosting Support & Sales Assistance</title>
    <!-- SEO Meta -->
    <meta name="description"
        content="Contact CloudFlox for support with web hosting, VPS, dedicated servers, or domain services. Our team is ready to help with sales, technical assistance, and account questions.">
    <meta name="keywords"
        content="contact CloudFlox, hosting support, web hosting help, vps support, domain support, cloud hosting contact">
    <meta name="author" content="CloudFlox">
    <link rel="canonical" href="https://cloudflox.com/contact-us">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Contact CloudFlox Support Team">
    <meta property="og:description" content="Reach CloudFlox for hosting assistance, technical support, or sales inquiries.">
    <meta property="og:url" content="https://cloudflox.com/contact-us">
    <meta property="og:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.jpg">
    <meta property="og:site_name" content="CloudFlox">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact CloudFlox">
    <meta name="twitter:description" content="Get help with hosting, domains, and servers from CloudFlox support.">
    <meta name="twitter:image" content="https://cloudflox.com/assets/imgs/banners/dedicated-server-components.jpg">

    <!-- =========== CSS Area =========== -->
    <?php include 'include/css.inc.php'; ?>
    <!-- =========== CSS Area =========== -->
    <!-- toast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />
</head>

<body class="bg-secondary">

    <!-- =========== Header Area =========== -->
    <?php include 'include/header.inc.php'; ?>
    <!-- =========== Header Area =========== -->

    <!-- Hero -->
    <section class="pt-60">
        <div class="">
            <div class="home-10-hero" style="background-image: url(assets/imgs/banners/dedicated-server-components.jpg);">
                <div class="pt-120 pb-120 px-2 position-relative bg-main-sec-home-color2">
                    <div class="container">
                        <div class="row align-items-center g-4">
                            <div class="col-12 text-center text-lg-start">

                                <!-- Breadcrumb -->
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb custom-breadcrumb mb-2">

                                        <li class="breadcrumb-item">
                                            <a href="/" class="text-white">Home</a>
                                        </li>

                                        <li class="breadcrumb-item active text-primary" aria-current="page">
                                            Contact Us
                                        </li>

                                    </ol>
                                </nav>

                                <!-- Title -->
                                <h1 class="text-white mb-3">
                                    Contact Cloudflox
                                </h1>


                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section><!-- Hero -->

    <!-- Contact Form -->
    <section class="pt-120 pb-120 bg-white">
        <div class="container">
            <div class="row align-items-center g-4">

                <!-- Form -->
                <div class="col-lg-6">
                    <h2 class="h3 mb-2"
                        data-sal="slide-up" data-sal-duration="500" data-sal-delay="300">
                        Contact CloudFlox Support
                    </h2>

                    <p class="mb-6"
                        data-sal="slide-up" data-sal-duration="500" data-sal-delay="300">
                        Need help with hosting, domains, billing, or technical setup? Send us a message and our team will respond as soon as possible.
                        Required fields are marked *
                    </p>

                    <form id="c-f">

                        <div class="row align-items-center g-3">

                            <div class="col-lg-6"
                                data-sal="slide-up" data-sal-duration="500" data-sal-delay="300">
                                <label class="text-black fs-14 fw-bold mb-2">
                                    Full Name <span class="text-primary">*</span>
                                </label>
                                <input type="text" id="name"
                                    placeholder="Enter your full name"
                                    class="form-control px-6 py-4 border bg-white bg-opacity-50">
                            </div>

                            <div class="col-lg-6"
                                data-sal="slide-up" data-sal-duration="500" data-sal-delay="400">
                                <label class="text-black fs-14 fw-bold mb-2">
                                    Email Address <span class="text-primary">*</span>
                                </label>
                                <input type="email" id="email"
                                    placeholder="you@example.com"
                                    class="form-control px-6 py-4 border bg-white bg-opacity-50">
                            </div>

                            <div class="col-lg-6"
                                data-sal="slide-up" data-sal-duration="500" data-sal-delay="400">
                                <label class="text-black fs-14 fw-bold mb-2">
                                    Phone (Optional)
                                </label>
                                <input type="tel" id="phone"
                                    placeholder="XX XXX XXXX"
                                    class="form-control px-6 py-4 border bg-white bg-opacity-50">
                            </div>

                            <div class="col-lg-6"
                                data-sal="slide-up" data-sal-duration="500" data-sal-delay="300">
                                <label class="text-black fs-14 fw-bold mb-2">
                                    Subject
                                </label>
                                <input type="text" id="subject"
                                    placeholder="Example: Shared Hosting setup / VPS upgrade"
                                    class="form-control px-6 py-4 border bg-white bg-opacity-50">
                            </div>

                            <div class="col-lg-12"
                                data-sal="slide-up" data-sal-duration="500" data-sal-delay="300">
                                <label class="text-black fs-14 fw-bold mb-2">
                                    Message <span class="text-primary">*</span>
                                </label>
                                <textarea id="message"
                                    placeholder="Tell us what you need help with (domain, hosting plan, error message, etc.)"
                                    class="form-control px-6 py-4 border h-30 bg-white bg-opacity-50"></textarea>
                            </div>

                            <div class="col-md-12 mb-4">
                                <div class="g-recaptcha" data-sitekey="6Le_PGssAAAAAIXSCvbRBvKbhGpoyiLAxd3fMA_H"></div>
                            </div>

                        </div>

                        <button type="button" id="form-btn" onclick="form_submit();"
                            class="btn btn-dark btn-arrow btn-arrow-lg btn-lg fs-14 fw-medium rounded mt-6"
                            data-sal="slide-up" data-sal-duration="1000" data-sal-delay="300">
                            <span class="btn-arrow__text">
                                Send Message
                                <span class="btn-arrow__icon">
                                    <i class="las la-arrow-right"></i>
                                </span>
                            </span>
                        </button>
                        <button type="button" id="form-btn2"
                            class="btn btn-dark btn-arrow btn-arrow-lg btn-lg fs-14 fw-medium rounded mt-6 d-none"
                            data-sal="slide-up" data-sal-duration="1000" data-sal-delay="300">
                            <span class="btn-arrow__text">
                                Please wait...
                                <span class="btn-arrow__icon">
                                    <i class="las la-arrow-right"></i>
                                </span>
                            </span>
                        </button>

                    </form>

                    <div class="contact-content cf-b-n-css d-none" id="th-c-f" style="background: #f7f7f7;">
                        <div class="card-body p-5">
                            <div>
                                <div class="d-flex justify-content-center">
                                    <img src="assets/imgs/success.gif" alt="Thank you" style="width: 300px;">
                                </div>
                                <h2 style="text-align: center; font-size: 23px;margin-top: 34px;">Thank you!</h2>
                                <p class="text-center mt-2">Your message has been successfully sent. We appreciate you reaching out to us and will get back to you as soon as possible.

                                </p>
                                <div class="d-flex justify-content-center pt-2">

                                    <a class="btn btn-primary mt-2" style="background: #2d3777; border-color: #4ba6de;" href="<?php echo $url; ?>">Back to Home</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Image -->
                <div class="col-lg-6">
                    <img src="assets/imgs/banners/contact.png"
                        alt="Contact CloudFlox Support"
                        class="img-fluid"
                        data-sal="fade" data-sal-duration="500" data-sal-delay="300">
                </div>
                </br></br></br>
                <p>Tel : 0112 617 664 / 070 211 59 38 / 070 211 59 37  |  Office  : BeesPoint(Pvt)Ltd, 140/1, Moratuwa Rd Piliyandala</p>

            </div>
        </div>
    </section>

    <!-- /Contact Form -->

    <!-- =========== Footer Area =========== -->
    <?php include 'include/footer.inc.php'; ?>
    <!-- =========== Footer Area =========== -->

    <!-- =========== JS Area =========== -->
    <?php include 'include/js.inc.php'; ?>
    <!-- =========== JS Area =========== -->

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        function displayToast(heading, text, icon, loaderBg) {
            $.toast({
                heading: heading,
                text: text,
                position: "bottom-right",
                loaderBg: loaderBg,
                icon: icon,
                hideAfter: 15000,
                stack: 3,
            });
        }

        function form_submit() {

            var recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                displayToast("Error", "Please verify that you are not a robot.", "error", "#c46a69");
                return;
            }

            document.getElementById("form-btn").classList.add("d-none");
            document.getElementById("form-btn2").classList.remove("d-none");

            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var phone = document.getElementById('phone').value;
            var subject = document.getElementById('subject').value;
            var message = document.getElementById('message').value;

            var form = new FormData();
            form.append("name", name);
            form.append("email", email);
            form.append("phone", phone);
            form.append("subject", subject);
            form.append("message", message);
            form.append("g-recaptcha-response", recaptchaResponse);

            var r = new XMLHttpRequest();
            r.onreadystatechange = function() {
                if (r.readyState == 4) {
                    var t = r.responseText;

                    if (t == "success") {
                        displayToast("Success", "Thank you for contacting us. We’ll get back to you shortly.", "success", "#3b7f5e");
                        document.getElementById("c-f").classList.add("d-none");
                        document.getElementById("th-c-f").classList.remove("d-none");
                    } else {
                        document.getElementById("form-btn").classList.remove("d-none");
                        document.getElementById("form-btn2").classList.add("d-none");
                        displayToast("Error", t, "error", "#c46a69");
                        grecaptcha.reset();
                    }
                }
            };

            r.open("POST", "include/send-mail/contact-send-email.php", true);
            r.send(form);
        }
    </script>

</body>

</html>