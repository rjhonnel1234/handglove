<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $voteDetailsModel = new \App\Models\FacilityVoteDetailsModel();
        $gnaWinners = $voteDetailsModel->getTopCliniciansLastWeek('gna', 2);
        $nurseWinners = $voteDetailsModel->getTopCliniciansLastWeek('nurse', 2);

        $providerModel = new \App\Models\ProvidersModel;
        $statesModel = new \App\Models\StatesModel;
        $countriesModel = new \App\Models\CountriesModel;

        $demo_data['providers'] = $providerModel->findAll();
        $demo_data['countries'] = $countriesModel->where('id', 233)->findAll();
        $demo_data['states'] = $statesModel->where('country_id', 233)->findAll();

        // PAGE HEAD PROCESSING
        return view('components/header_v3', array(
            'title' => 'Handglove',
            'description' => 'Water for Every Filipino. 50 years in the pipe manufacturing industry and more than 30 years experience in bulk water supply, water distribution system and wastewater management.',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => array(
                'title' => 'Handglove',
                'description' => 'Water for Every Filipino. 50 years in the pipe manufacturing industry and more than 30 years experience in bulk water supply, water distribution system and wastewater management.',
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
                COMPILED_ASSETS_PATH . 'css/components/jquery-steps',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/components/theme',
                COMPILED_ASSETS_PATH . 'css/pages/pages',
                COMPILED_ASSETS_PATH . 'css/pages/home'
            )
        ))
        .view('home', array_merge([
            'gna_winners' => $gnaWinners,
            'nurse_winners' => $nurseWinners
        ], $demo_data))
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
                ASSETS_URL . 'js/plugins/jquery.steps.min.js',
                ASSETS_URL . 'js/plugins/jquery.validate.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/pages/home.min.js',
                ASSETS_URL . 'js/pages/demo.min.js',
            )
        ))
        .view('components/footer_v3');
    }
}
