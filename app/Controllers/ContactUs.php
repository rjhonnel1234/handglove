<?php

namespace App\Controllers;

class ContactUs extends BaseController
{
    public function index()
    {
        return view('components/header_v3', array(
            'title' => 'Contact Us | Handglove',
            'description' => 'Staffing Partners: Reliable & Cost Efficient Recruitment Agency',
            'url' => BASE_URL . 'contact-us',
            'keywords' => '',
            'meta' => array(
                'title' => 'Contact Us | Handglove',
                'description' => 'Staffing Partners: Reliable & Cost Efficient Recruitment Agency',
                'image' => IMG_URL . ''
            ),
            'styles' => array(
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/colors',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/components/theme',
                COMPILED_ASSETS_PATH . 'css/pages/home'
            )
        ))
        .view('public/contact_us')
        .view('components/scripts_render', array(
            'scripts' => array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/appear.js',
                ASSETS_URL . 'js/plugins/isotope.js',
                ASSETS_URL . 'js/plugins/tweenmax.js',
                ASSETS_URL . 'js/plugins/scrollbar.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/createjs.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/pages/home.min.js',
            )
        ))
        .view('components/footer_v3');
    }

    public function submit()
    {
        // Handle form submission here
        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
}
