<?php

namespace App\Controllers;


use App\Models\CliniciansModel;
use App\Models\CredentialTypesModel;
use App\Models\ClinicianCredentialsModel;
use App\Models\UserModel;
use App\Models\FacilityModel;
use App\Models\FacilityVotesModel;
use App\Models\FacilityVoteDetailsModel;
use App\Models\FacilityUnitsModel;
use App\Models\FacilityVotingModel;
use App\Models\ClientRatingsModel;
use App\Models\ShiftsModel;
use App\Models\FacilityOnboardingModel;
use App\Models\FacilityOnboardingSettingsModel;
use App\Models\ShiftUploadsModel;
use \Datetime;
use CodeIgniter\Files\File;

class Facility extends BaseController
{
    public function index()
    {
        $session = session();
        $data['session'] = $session;
        if( is_null($session->get('isLoggedIn')) || $session->get('isLoggedIn') != 1){
            return redirect()->to('/');
        }else{
            if($session->get('facility_id') != 0){
                $facilityModel = new FacilityModel;
                $data['facility'] = $facilityModel->find($session->get('facility_id'));
                if(!$data['facility']){
                    return redirect()->to('/profile');
                }
            }else{
                return redirect()->to('/profile');
            }
            $onboardingSettingsModel = new FacilityOnboardingSettingsModel;
            $data['onboardingSettings'] = $onboardingSettingsModel->where('client_id', $session->get('facility_id'))->first();

            $objUnits = new FacilityUnitsModel;
            $units = $objUnits->where('client_id', $session->get('facility_id'))->findAll();

            $objVotes = new FacilityVotesModel;
            $votingArr_ = $objVotes->where('"'.date("Y-m-d") . '" BETWEEN voting_start_date AND voting_end_date')->where('client_id', $session->get('facility_id'))->findAll();
            if(!empty($votingArr_)){
                $votingArr = [];
                foreach($votingArr_ as $voting){
                    $objVoteDetails = new FacilityVoteDetailsModel;
                    $voting['details'] = $objVoteDetails->where('voting_id', $voting['id'])->findAll();
                    if(!empty($voting['details'])){
                        $total_votes = 0;
                        foreach($voting['details'] as $ctr => $detail){
                            $total_votes += $detail['votes'];
                            $clinModel = new CliniciansModel;
                            $voting['details'][$ctr]['clinician_details'] = $clinModel->find($detail['clinician_id']);
                        }
                        $voting['total_votes'] = $total_votes;

                        foreach($voting['details'] as $ctr => $detail){
                            $voting['details'][$ctr]['vote_percentage'] = $total_votes > 0 ? ($detail['votes'] / $total_votes) * 100 : 0;
                        }
                    }
                    $votingArr[] = $voting;
                }
                $data['votingArr'] = $votingArr;
            }

            $objShifts = new ShiftsModel();
            $hasActiveShift = $objShifts->hasActiveShift($session->get('facility_id'));
            $hasActiveShiftThisWeek = $objShifts->hasActiveShiftThisWeek($session->get('facility_id'));

            $hasActiveVoting = $objVotes->hasActiveVoting($session->get('facility_id'));
            $workFriendlyVal = 0;
            //Check if facility has active shift for this week, add 30 points if yes
            if($hasActiveVoting){
                $workFriendlyVal += 20;
            }
            if($hasActiveShift){
                $workFriendlyVal += 20;
            }
            if($hasActiveShiftThisWeek){
                $workFriendlyVal += 30;
            }

            $objRatings = new ClientRatingsModel();
            $aggregatesData = $objRatings->select('AVG(cleanliness) as cleanliness, AVG(work_environment) as work_environment, AVG(tools_needed) as tools_needed, AVG(average) as average')
                ->where('client_id', $session->get('facility_id'))
                ->first();

            $aggregates = [
                'cleanliness' => number_format($aggregatesData['cleanliness'] ?? 0, 2),
                'work_environment' => number_format($aggregatesData['work_environment'] ?? 0, 2),
                'tools_needed' => number_format($aggregatesData['tools_needed'] ?? 0, 2),
                'average' => number_format($aggregatesData['average'] ?? 0, 2),
                'average_percentage' => ($aggregatesData['average'] ?? 0) / 5 * 100
            ];

            if ($aggregates['average_percentage'] > 0) {
                // Get the percentage value of average_percentage relative to 30 points
                $ratingBonus = ($aggregates['average_percentage'] / 100) * 30;
                $workFriendlyVal += ceil($ratingBonus);
            }
            
            $data['workFriendly'] = $workFriendlyVal;

            $data['units'] = $units;

            $view = 'private_profile';
            $scripts = array(
                'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ),
                'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                ASSETS_URL . 'js/pages/facility_profile.min.js',
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
            );

            $styles = array(
                'plugins/font_awesome',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/toastr',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
            );

            if(session()->get('type') == 5){
                $view = 'scheduler/profile';
                $scripts[] = 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js';
                $scripts[] = 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js';
                $scripts[] = ASSETS_URL . 'js/pages/facility_scheduler_profile.min.js';

                $styles[] = COMPILED_ASSETS_PATH . 'css/components/dropzone';
                $styles[] = COMPILED_ASSETS_PATH . 'css/pages/facility_scheduler_profile';
            }else{
                $scripts[] = ASSETS_URL . 'js/pages/facility_shifts.min.js';
            }
            // PAGE HEAD PROCESSING
            return view('components/header', array(
                'title' => 'Handglove',
                'description' => '',
                'url' => BASE_URL,
                'keywords' => '',
                'meta' => array(
                    'title' => 'Handglove',
                    'description' => '',
                    'image' => IMG_URL . ''
                ),
                'styles' => $styles,
                'session' => $data['session']
            ))
            .view('facility/'.$view, $data)
            .view('components/scripts_render', array(
                'scripts' => $scripts,
            ))
            .view('components/footer');
        }
    }

    public function profile($client_id = '')
    {
        $session = session();
        $data['session'] = $session;
        if(empty($client_id)){
            return redirect()->to('/profile');
        }else{
            if($client_id != 0){
                $facilityModel = new FacilityModel;
                $data['facility'] = $facilityModel->find($client_id);
                if(!$data['facility']){
                    return redirect()->to('/profile');
                }
            }

            $onboardingSettingsModel = new FacilityOnboardingSettingsModel;
            $data['onboardingSettings'] = $onboardingSettingsModel->where('client_id', $client_id)->first();
            
            if(!empty($data['onboardingSettings']['accepted_per_diem_network'])){
                $data['onboardingSettings']['accepted_per_diem_network'] = json_decode($data['onboardingSettings']['accepted_per_diem_network'], true);
            } else {
                $data['onboardingSettings']['accepted_per_diem_network'] = [];
            }

            $clinModel = new CliniciansModel;
            $data['profileData'] = $clinModel->where('email', session()->get('email'))->first();

            $objVotes = new FacilityVotesModel;
            $votingArr_ = $objVotes->where('"'.date("Y-m-d") . '" BETWEEN voting_start_date AND voting_end_date')->where('client_id', $client_id)->findAll();
            if(!empty($votingArr_)){
                $votingArr = [];
                foreach($votingArr_ as $voting){
                    $hasVoted = [];
                    if(!empty($data['profileData'])){
                        $objVotingModel = new FacilityVotingModel;
                        $hasVoted = $objVotingModel->where('voting_id', $voting['id'])->where('profile_id', $data['profileData']['id'])->findAll();
                    }
                    if(!empty($hasVoted)){
                        $voting['hasVoted'] = true;
                    }else{
                        $voting['hasVoted'] = false;
                    }

                    $objVoteDetails = new FacilityVoteDetailsModel;
                    $voting['details'] = $objVoteDetails->where('voting_id', $voting['id'])->findAll();
                    if(!empty($voting['details'])){
                        $total_votes = 0;
                        foreach($voting['details'] as $ctr => $detail){
                            $total_votes += $detail['votes'];
                            $clinModel = new CliniciansModel;
                            $voting['details'][$ctr]['clinician_details'] = $clinModel->find($detail['clinician_id']);
                        }
                        $voting['total_votes'] = $total_votes;

                        foreach($voting['details'] as $ctr => $detail){
                            $voting['details'][$ctr]['vote_percentage'] = $total_votes > 0 ? ($detail['votes'] / $total_votes) * 100 : 0;
                        }
                    }
                    $votingArr[] = $voting;
                }
                $data['votingArr'] = $votingArr;
            }

            $objShifts = new ShiftsModel();
            $hasActiveShift = $objShifts->hasActiveShift($client_id);
            $hasActiveShiftThisWeek = $objShifts->hasActiveShiftThisWeek($client_id);

            $hasActiveVoting = $objVotes->hasActiveVoting($client_id);
            $workFriendlyVal = 0;
            
            if($hasActiveVoting){
                $workFriendlyVal += 20;
            }
            if($hasActiveShift){
                $workFriendlyVal += 20;
            }
            if($hasActiveShiftThisWeek){
                $workFriendlyVal += 30;
            }


            // FETCH REVIEWS
            $objRatings = new ClientRatingsModel();

            //add query to get total rows only
            $totalReviews = $objRatings->select('COUNT(*) as total')
                ->where('tbl_client_ratings.client_id', $client_id)
                ->first();
            $totalCount = $totalReviews['total'];
            $aggregatesData = $objRatings->select('AVG(cleanliness) as cleanliness, AVG(work_environment) as work_environment, AVG(tools_needed) as tools_needed, AVG(average) as average')
                ->where('client_id', $client_id)
                ->first();

            $aggregates = [
                'cleanliness' => number_format($aggregatesData['cleanliness'] ?? 0, 2),
                'work_environment' => number_format($aggregatesData['work_environment'] ?? 0, 2),
                'tools_needed' => number_format($aggregatesData['tools_needed'] ?? 0, 2),
                'average' => number_format($aggregatesData['average'] ?? 0, 2),
                'count' => $totalCount,
                'average_percentage' => ($aggregatesData['average'] ?? 0) / 5 * 100
            ];
            $data['aggregates'] = $aggregates;
            
            if ($aggregates['average_percentage'] > 0) {
                // Get the percentage value of average_percentage relative to 30 points
                $ratingBonus = ($aggregates['average_percentage'] / 100) * 30;
                $workFriendlyVal += ceil($ratingBonus);
            }
            $data['workFriendly'] = $workFriendlyVal;
            
            // PAGE HEAD PROCESSING
            return view('components/header', array(
                'title' => 'Handglove',
                'description' => '',
                'url' => BASE_URL,
                'keywords' => '',
                'meta' => array(
                    'title' => 'Handglove',
                    'description' => '',
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
                    COMPILED_ASSETS_PATH . 'css/components/footer',
                    COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
                ),
                'session' => $data['session']
            ))
            .view('facility/public_profile', $data)
            .view('components/scripts_render', array(
                'scripts' => array(
                    'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                        'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                        'crossorigin' => 'anonymous'
                    ),
                    ASSETS_URL . 'js/plugins/popper.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                    ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                    ASSETS_URL . 'js/components/global.min.js',
                    ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                    ASSETS_URL . 'js/components/notifications.min.js',
                    ASSETS_URL . 'js/components/navigation_bar.min.js',
                    ASSETS_URL . 'js/plugins/toastr.min.js',
                    ASSETS_URL . 'js/pages/facility_profile.min.js',
                )
            ))
            .view('components/footer');
        }
    }

    public function get_reviews()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $clientId = $this->request->getPost('client_id');
        $page = $this->request->getPost('page') ?: 1;
        $limit = 3;
        $offset = ($page - 1) * $limit;

        $objRatings = new ClientRatingsModel();
        $reviews = $objRatings->select('tbl_client_ratings.*, tbl_clinicians.name as clinician_name, tbl_clinicians.profile_pic_url')
            ->join('tbl_clinicians', 'tbl_clinicians.id = tbl_client_ratings.clinician_id', 'inner')
            ->where('tbl_client_ratings.client_id', $clientId)
            ->orderBy('datetime_added', 'DESC')
            ->limit($limit, $offset)
            ->findAll();

        $html = '';
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $profilePic = !empty($review['profile_pic_url']) ? $review['profile_pic_url'] : base_url('assets/img/blank-img.png');
                $dateFormatted = date("F d, Y", strtotime($review['datetime_added']));
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= '<i class="fa' . ($i <= round($review['average']) ? 's' : 'r') . ' fa-star"></i>';
                }

                $comment = htmlspecialchars($review['comment']);
                $name = htmlspecialchars($review['clinician_name']);

                $html .= "
                    <div class='review-item mb-4 pb-4 border-bottom'>
                        <div class='d-flex align-items-start'>
                            <img src='{$profilePic}' class='rounded-circle mr-3' style='width: 60px; height: 60px; object-fit: cover;'>
                            <div>
                                <div class='rating-stars text-warning mb-1'>
                                    {$stars}
                                </div>
                                <h6 class='mb-1'>{$name}: <span class='font-weight-normal'>{$comment}</span></h6>
                                <p class='mb-0 text-muted small'>{$dateFormatted} • Verified clinician</p>
                            </div>
                        </div>
                    </div>";
            }
        }

        return $this->response->setJSON([
            'success' => 1,
            'html' => $html,
            'has_more' => count($reviews) == $limit,
            'total_pages' => ceil($objRatings->where('client_id', $clientId)->countAllResults() / $limit),
            'current_page' => $page
        ]);
    }

    public function vote(){
        $data = [
            'success' => 0, 
            'message' => 'Invalid requests.'
        ];

        $session = session();
        if( $session->get('isLoggedIn') == 1){
            if($this->request->isAJAX() && $this->request->getPost('votingID') && $this->request->getPost('clinicianID')){
                $clinModel = new CliniciansModel;
                $profileData = $clinModel
                                        ->select('tbl_clinicians.*, tbl_clinician_types.name as type_name')
                                        ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type', 'INNER')
                                        ->where('tbl_clinicians.email', session()->get('email'))
                                        ->first();
                if(!empty($profileData)){

                    $votingModel = new FacilityVotingModel;
                    $voteDetailsModel = new FacilityVoteDetailsModel;

                    $item = [
                        'voting_id' => $this->request->getPost('votingID'),
                        'clinician_id' => $this->request->getPost('clinicianID'),
                        'profile_id' => $profileData['id']
                    ];
                    $votingModel->save($item);

                    $test = $voteDetailsModel->where('voting_id', $this->request->getPost('votingID'))->where('clinician_id', $this->request->getPost('clinicianID'))->set('votes','tbl_client_voting_details.votes+1',false)->update();

                    $data['success'] = 1;
                    $data['message_header'] = 'Voting';
                    $data['message'] = 'We got your vote! We appreciate your participation in this voting process. Thank you.';
                }
            }
        }
        echo json_encode($data);
        exit();
    }


    public function onboarding($facility_id, $onboarding_id){
        $facilityModel = new FacilityModel;
        $facility = $facilityModel->find($facility_id);
        if(!empty($facility)){

            $onboardingModel = new FacilityOnboardingModel;
            $onboarding = $onboardingModel->find($onboarding_id);
            if(!empty($onboarding)){
                $data['onboarding'] = $onboarding;
                $data['facility'] = $facility;
                
                $clinModel = new CliniciansModel;
                $data['profileData'] = $clinModel->where('email', session()->get('email'))->first();


                $objVotes = new FacilityVotesModel;
                

                $objShifts = new ShiftsModel();
                $hasActiveShift = $objShifts->hasActiveShift($facility_id);
                $hasActiveShiftThisWeek = $objShifts->hasActiveShiftThisWeek($facility_id);

                $hasActiveVoting = $objVotes->hasActiveVoting($facility_id);
                $workFriendlyVal = 0;
                //Check if facility has active shift for this week, add 30 points if yes
                if($hasActiveVoting){
                    $workFriendlyVal += 20;
                }
                if($hasActiveShift){
                    $workFriendlyVal += 20;
                }
                if($hasActiveShiftThisWeek){
                    $workFriendlyVal += 30;
                }

                $objRatings = new ClientRatingsModel();
                $aggregatesData = $objRatings->select('AVG(cleanliness) as cleanliness, AVG(work_environment) as work_environment, AVG(tools_needed) as tools_needed, AVG(average) as average')
                    ->where('client_id', $facility_id)
                    ->first();

                $aggregates = [
                    'cleanliness' => number_format($aggregatesData['cleanliness'] ?? 0, 2),
                    'work_environment' => number_format($aggregatesData['work_environment'] ?? 0, 2),
                    'tools_needed' => number_format($aggregatesData['tools_needed'] ?? 0, 2),
                    'average' => number_format($aggregatesData['average'] ?? 0, 2),
                    'average_percentage' => ($aggregatesData['average'] ?? 0) / 5 * 100
                ];

                if ($aggregates['average_percentage'] > 0) {
                    // Get the percentage value of average_percentage relative to 30 points
                    $ratingBonus = ($aggregates['average_percentage'] / 100) * 30;
                    $workFriendlyVal += ceil($ratingBonus);
                }
                
                $data['workFriendly'] = $workFriendlyVal;

                
                // PAGE HEAD PROCESSING
                $session = session();
                $data['session'] = $session;
                
                return view('components/header', array(
                    'title' => 'Handglove',
                    'description' => '',
                    'url' => BASE_URL,
                    'keywords' => '',
                    'meta' => array(
                        'title' => 'Handglove',
                        'description' => '',
                        'image' => IMG_URL . ''
                    ),
                    'styles' => array(
                        'plugins/font_awesome',
                        'plugins/datatables',
                        COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                        COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                        COMPILED_ASSETS_PATH . 'css/components/owl',
                        COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                        COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                        COMPILED_ASSETS_PATH . 'css/components/bootstrap-datepicker',
                        COMPILED_ASSETS_PATH . 'css/components/global',
                        COMPILED_ASSETS_PATH . 'css/components/animations',
                        COMPILED_ASSETS_PATH . 'css/components/buttons',
                        COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                        COMPILED_ASSETS_PATH . 'css/components/footer',
                        COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
                    ),
                    'session' => $data['session']
                ))
                .view('facility/onboarding', $data)
                .view('components/scripts_render', array(
                    'scripts' => array(
                        'https://code.jquery.com/jquery-3.5.1.min.js' => array(
                            'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                            'crossorigin' => 'anonymous'
                        ),
                        'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
                        ASSETS_URL . 'js/plugins/popper.min.js',
                        ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                        ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                        ASSETS_URL . 'js/components/global.min.js',
                        ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                        ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                        ASSETS_URL . 'js/components/navigation_bar.min.js',
                    )
                ))
                .view('components/footer');
            }else{
                // $this->response->setStatusCode(404, 'Not Found');
                // return view('errors/html/error_404', $data);
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }else{
            // $this->response->setStatusCode(404, 'Not Found');
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }

    }

    public function onboarding_pdf($facility_id, $onboarding_id){
        $facilityModel = new FacilityModel;
        $facility = $facilityModel->find($facility_id);
        if(!empty($facility)){
            $onboardingModel = new FacilityOnboardingModel;
            $onboarding = $onboardingModel->find($onboarding_id);
            if(!empty($onboarding)){
                if (! file_exists($onboarding['filename'])) {
                    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
                }

                $file = new \CodeIgniter\Files\File($onboarding['filename']);
                return $this->response
                    ->setHeader('Content-Type', $file->getMimeType())
                    ->setHeader('Content-disposition', 'inline; filename="' . $file->getBasename() . '"')
                    ->setStatusCode(200)
                    ->setBody(file_get_contents($onboarding['filename']));
            }else{
                // $this->response->setStatusCode(404, 'Not Found');
                // return view('errors/html/error_404', $data);
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }else{
            // $this->response->setStatusCode(404, 'Not Found');
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();

        }
    }

}
