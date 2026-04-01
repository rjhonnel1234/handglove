
<!--Page Title-->
<section class="page-title" style="background-image: url(assets/images/background/page-title-2.jpg);">
    <div class="pattern-layer" style="background-image: url(assets/images/shape/pattern-35.png);"></div>
    <div class="auto-container">
        <div class="content-box">
            <div class="title-box centred">
                <h1>FAQ’S</h1>
                <p>Things You Need to Know and We Know You Need</p>
            </div>
            <ul class="bread-crumb clearfix">
                <li><a href="index.html">Home</a></li>
                <li>Employers</li>
                <li>FAQ’S</li>
            </ul>
        </div>
    </div>
</section>
<!--End Page Title-->

<!-- faq-section -->
<section class="faq-section">
    <div class="auto-container">
        <div class="row clearfix">
            <div class="col-lg-6 col-md-12 col-sm-12 image-column">
                <figure class="image-box"><img src="<?php echo base_url('assets/img/resource/faq-1.png'); ?>" alt=""></figure>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 inner-column">
                <div class="inner-box">
                    <div class="sec-title">
                        <span class="top-title">Employer Faq’s</span>
                        <h2>You'll Find Answers Here!</h2>
                    </div>
                    <ul class="accordion-box">
                        <?php if (empty($faqs)): ?>
                            <li class="accordion block">
                                <div class="acc-btn">
                                    <h5>No FAQs available at the moment.</h5>
                                </div>
                            </li>
                        <?php else: ?>
                            <?php foreach ($faqs as $index => $faq): ?>
                                <li class="accordion block <?= $index === 0 ? 'active-block' : '' ?>">
                                    <div class="acc-btn <?= $index === 0 ? 'active' : '' ?>">
                                        <div class="icon-outer"></div>
                                        <h5><?= sprintf('%02d', $index + 1) ?>. <?= esc($faq['question']) ?></h5>
                                    </div>
                                    <div class="acc-content <?= $index === 0 ? 'current' : '' ?>">
                                        <div class="text">
                                            <p><?= nl2br(esc($faq['answer'])) ?></p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- faq-section end -->