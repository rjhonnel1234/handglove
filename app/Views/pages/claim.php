<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>

<!--Page Title-->
<section class="page-title" style="background-image: url(assets/images/background/page-title-2.jpg);">
    <div class="pattern-layer" style="background-image: url(assets/images/shape/pattern-35.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <div class="title-box centred">
                <h1>Claim Your Facility</h1>
                <!-- <p>Thanks to our team, it is now possible for us to get the finest & services</p> -->
            </div>
            <ul class="bread-crumb clearfix">
                <li><a href="index.html">Home</a></li>
                <li>Claim Your Facility</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->


<div id="claim" class="main-pages d-flex align-items-center" style="min-height: 50vh;">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center" id="claim-container">
                <div class="claim-form">
                    <div id="claim-progress" class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="claim-form-container">
                        <form id="form-claim" action="#">
                            <h3></h3>
                            <section>
                                <div class="form-group">
                                    <label for="" class="label-parent">What company do you work for?</label>
                                    <select name="company_name" id="company_name" class="form-control required" data-title="Select Company name.." data-live-search="true">
                                        <?php foreach($providers as $provider){ ?>
                                            <option value="<?php echo $provider['id']; ?>" data-state="<?php echo $provider['state']; ?>" data-zip="<?php echo $provider['zip']; ?>" data-country="<?php echo $provider['country']; ?>"><?php echo $provider['name']; ?></option>
                                        <?php } ?>
                                        <!-- <option value="Others">Other company</option> -->
                                    </select>
                                </div>
                                <!-- <div class="form-group">
                                    <div id="otherCompany">
                                        <input type="text" name="other_company_name" id="other_company_name" class="form-control required"  placeholder="Company name">
                                    </div>
                                </div> -->
                            </section>
                            <h3></h3>
                            <section>
                                <div class="mb-4">
                                    <label for="" style="font-size: 20px; font-weight: bold;">What's your name?</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="label-parent" style="font-size: 16px;">First name</label>
                                                <input type="text" name="first_name" id="first_name" class="form-control required">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="label-parent" style="font-size: 16px;">Last name</label>
                                                <input type="text" name="last_name" class="form-control required">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="" class="label-parent">Your position in the company?</label>
                                    <select name="position" id="position" class="form-control required" data-title="Select your position">
                                        <option value="DON">DON</option>
                                        <option value="HR">HR</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Scheduler">Scheduler</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </section>
                            <h3></h3>
                            <section>
                                <div class="mb-4">
                                    <label for="" style="font-size: 20px; font-weight: bold;">Let's continue <span id="firstNameLabel"></span>, where should we send your information?</label>
                                    <div class="form-group">
                                        <label for="" class="label-parent" style="font-size: 16px;">Email Address</label>
                                        <input type="email" name="email_address" id="email_address" class="form-control required">
                                    </div>
                                </div>
                            </section>

                            <h3></h3>
                            <section>

                                <div class="fields">
                                    <div class="row">
                                        <div class="col-md-12">
                                            Please enter the One-Time Password (OTP) sent to <a href="javascript:;"><span id="email_address_text"></span></a>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="otp-notif"></div>
                                            <div id="otps" class="inputs">
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp" type="text" inputmode="numeric" maxlength="1" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <small>
                                                <div>Didn't receive any code?</div>
                                                <div id="resendLink" class="disabled"><a href="javascript:;" id="resendCode">Resend code</a> <span class="timer"></span></div>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <h3></h3>
                            <section>
                                <div class="mb-4">
                                    <label for="" style="font-size: 20px; font-weight: bold;">Almost there, when would you like to schedule a call?</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="booking_date" class="label-parent" style="font-size: 16px;">Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                    <input type="text" name="booking_date" id="booking_date" class="form-control datepicker required" readonly autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="booking_time" class="label-parent" style="font-size: 16px;">Time</label>
                                                <select name="booking_time" id="booking_time" class="form-control required">
                                                    <option value="">Select time..</option>
                                                    <option value="09:00 AM">09:00 AM</option>
                                                    <option value="10:00 AM">10:00 AM</option>
                                                    <option value="11:00 AM">11:00 AM</option>
                                                    <option value="12:00 PM">12:00 PM</option>
                                                    <option value="01:00 PM">01:00 PM</option>
                                                    <option value="02:00 PM">02:00 PM</option>
                                                    <option value="03:00 PM">03:00 PM</option>
                                                    <option value="04:00 PM">04:00 PM</option>
                                                    <option value="05:00 PM">05:00 PM</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                        
                            <h3></h3>
                            <section>
                                <div class="mb-4">
                                    <label for="" style="font-size: 20px; font-weight: bold;">Wrapping up, what's your phone number?</label>
                                    <div class="form-group">
                                        <label for="" class="label-parent" style="font-size: 16px;">Phone number</label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control required">
                                    </div>
                                </div>
                            </section>

                            <!-- <h3></h3>
                            <section>

                                <div class="fields">
                                    <div class="row">
                                        <div class="col-md-12">
                                            Please enter the One-Time Password (OTP) sent to <a href="javascript:;"><span id="contact_number_text"></span></a>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <div class="otp-notif-sms"></div>
                                            <div id="otps2" class="inputs">
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                                <input class="otp2" type="text" inputmode="numeric" maxlength="1" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12">
                                            <small>
                                                <div>Didn't receive any code?</div>
                                                <div id="resendSMSLink" class="disabled"><a href="javascript:;" id="resendSMSCode">Resend code</a> <span class="timerSMS"></span></div>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </section> -->
                            <h3>Finish</h3>
                            <section>
                                <div class="form-group">
                                    <input id="acceptTerms" name="acceptTerms" type="checkbox" class="required"> <label for="acceptTerms">I agree with the Terms and Conditions.</label>
                                </div>
                            </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
