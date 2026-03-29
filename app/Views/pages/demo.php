<script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.6.2/dist/dotlottie-wc.js" type="module"></script>
<div id="demo" class="main-pages">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center" id="demo-container">
                <h1>Book A Demo</h1>
                <div class="demo-form">
                    <div id="demo-progress" class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div id="demo-form-container">
                        <form id="form-demo" action="#">
                            <h3></h3>
                            <section>
                                <div class="form-group">
                                    <label for="" class="label-parent">What is your average census?</label>
                                    <div class="option">
                                        <label for="census_1">
                                            <input type="radio" id="census_1" name="census" value="1:5" class="requrired">
                                            <span class="custom-radio"></span>
                                            1:5
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="census_2">
                                            <input type="radio" id="census_2" name="census" value="1:10" class="requrired">
                                            <span class="custom-radio"></span>
                                            1:10
                                        </label>
                                    </div>

                                    <div class="option">
                                        <label for="census_3">
                                            <input type="radio" id="census_3" name="census" value="1:15" class="requrired">
                                            <span class="custom-radio"></span>
                                            1:15
                                        </label>
                                    </div>

                                    <div class="option">
                                        <label for="census_4">
                                            <input type="radio" id="census_4" name="census" value="1:20" class="requrired">
                                            <span class="custom-radio"></span>
                                            1:20
                                        </label>
                                    </div>

                                    <div class="option">
                                        <label for="census_5">
                                            <input type="radio" id="census_5" name="census" value="1:25" class="requrired">
                                            <span class="custom-radio"></span>
                                            1:25
                                        </label>
                                    </div>
                                </div>
                            </section>
                            <h3></h3>
                            <section>
                                <div class="form-group">
                                    <label for="" class="label-parent">Great, and what features are you interested in?</label>
                                    <div class="option">
                                        <label for="feature_1">
                                            <input type="checkbox" id="feature_1" name="features[]" value="Saving cost">
                                            <span class="custom-check"></span>
                                            Saving cost
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="feature_2">
                                            <input type="checkbox" id="feature_2" name="features[]" value="Retainer staff">
                                            <span class="custom-check"></span>
                                            Retainer staff
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="feature_3">
                                            <input type="checkbox" id="feature_3" name="features[]" value="Tracking Staff Behavior">
                                            <span class="custom-check"></span>
                                            Tracking Staff Behavior
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="feature_4">
                                            <input type="checkbox" id="feature_4" name="features[]" value="Feature for DON">
                                            <span class="custom-check"></span>
                                            Feature for DON
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="feature_5">
                                            <input type="checkbox" id="feature_5" name="features[]" value="Feature For Scheduler">
                                            <span class="custom-check"></span>
                                            Feature For Scheduler
                                        </label>
                                    </div>
                                    <div class="option">
                                        <label for="feature_6">
                                            <input type="checkbox" id="feature_6" name="features[]" value="Feature For Supervisor">
                                            <span class="custom-check"></span>
                                            Feature For Supervisor
                                        </label>
                                    </div>

                                </div>
                            </section>
                            <h3></h3>
                            <section>
                                <div class="form-group">
                                    <label for="" class="label-parent">And what company do you work for?</label>
                                    <select name="company_name" id="company_name" class="form-control required" data-title="Select Company name.." data-live-search="true">
                                        <?php foreach($providers as $provider){ ?>
                                            <option value="<?php echo $provider['id']; ?>" data-state="<?php echo $provider['state']; ?>" data-zip="<?php echo $provider['zip']; ?>" data-country="<?php echo $provider['country']; ?>"><?php echo $provider['name']; ?></option>
                                        <?php } ?>
                                        <option value="Others">Other company</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div id="otherCompany">
                                        <input type="text" name="other_company_name" id="other_company_name" class="form-control required"  placeholder="Company name">
                                    </div>
                                </div>
                            </section>
                            <h3></h3>
                            <section>
                                <div class="form-group">
                                    <label>OK, and where are you located?</label>
                                    <div id="company_address_container">
                                        <label for="" class="parent">Company Address</label>
                                        <input type="text" name="address" id="address" class="form-control required">
                                    </div>
                                    <div>
                                        <label class="label-parent mb-1"><small>Country</small></label>
                                        <select name="country" id="country" class="form-control required">
                                        <?php foreach($countries as $country){ ?>
                                            <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="label-parent mb-1"><small>State</small></label>
                                            <select name="state" id="state" class="form-control required" data-live-search="true" data-title="Select state">
                                                <?php foreach($states as $state){ ?>
                                                    <option value="<?php echo $state['id']; ?>" data-country_id="<?php echo $state['country_id']; ?>"><?php echo $state['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="label-parent"><small>Zip code</small></label>
                                                <input type="text" name="zip" id="zip" class="form-control required">
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                        <?php foreach($user_types as $user_type){ ?>
                                            <option value="<?php echo $user_type['id']; ?>"><?php echo $user_type['name']; ?></option>
                                        <?php } ?>
                                        <option value="8">Other</option>
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
                                    <label for="" style="font-size: 20px; font-weight: bold;">Wrapping up, what's your phone number?</label>
                                    <div class="form-group">
                                        <label for="" class="label-parent" style="font-size: 16px;">Phone number</label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control required">
                                    </div>
                                </div>
                            </section>

                            <h3></h3>
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
                            </section>
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