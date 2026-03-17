
<section class="page-title" style="background-image: url(<?= ASSETS_URL ?>img/background/page-title.jpg);">
    <div class="pattern-layer" style="background-image: url(<?= ASSETS_URL ?>img/shape/pattern-35.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <div class="title-box centred">
                <h1>Contact Us</h1>
                <ul class="bread-crumb clearfix">
                    <li><a href="<?= BASE_URL ?>">Home</a></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="contact-section">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-4 col-md-12 col-sm-12 info-column">
                <div class="contact-info-panel">
                    <div class="sec-title">
                        <span class="top-title">Contact Us</span>
                        <h2>Get In Touch</h2>
                    </div>
                    <div class="info-box">
                        <div class="single-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>Location</h4>
                            <p>Handglove Headquarters<br />State, Country</p>
                        </div>
                        <div class="single-info">
                            <i class="fas fa-phone"></i>
                            <h4>Phone</h4>
                            <p><a href="tel:0000000000">000 000 0000</a></p>
                        </div>
                        <div class="single-info">
                            <i class="fas fa-envelope"></i>
                            <h4>Email</h4>
                            <p><a href="mailto:info@handglove.com">info@handglove.com</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-12 col-sm-12 form-column">
                <div class="form-inner">
                    <form method="post" action="<?= BASE_URL ?>contact-us/submit" id="contact-form">
                        <div class="row clearfix">
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="text" name="username" placeholder="Your Name" required="">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="email" name="email" placeholder="Email Address" required="">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="text" name="phone" placeholder="Phone Number" required="">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 form-group">
                                <input type="text" name="subject" placeholder="Subject" required="">
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 form-group">
                                <textarea name="message" placeholder="Message"></textarea>
                            </div>
                            <div class="col-lg-12 col-md-12 col-sm-12 form-group message-btn">
                                <button type="submit" class="theme-btn-one" name="submit-form">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
