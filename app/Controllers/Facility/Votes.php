<?php

namespace App\Controllers\Facility;

use App\Controllers\BaseController;
use App\Models\CliniciansModel;
use App\Models\UserModel;
use App\Models\FacilityModel;
use App\Models\FacilityVotesModel;
use App\Models\FacilityVoteDetailsModel;
use \Datetime;
use CodeIgniter\Files\File;

class Votes extends BaseController
{
    protected $clinicianModel;
    protected $userModel;
    protected $facilityModel;
    protected $votesModel;
    protected $voteDetailsModel;
    protected $session;

    public function __construct()
    {
        $this->clinicianModel = new CliniciansModel();
        $this->userModel = new UserModel();
        $this->facilityModel = new FacilityModel();
        $this->votesModel = new FacilityVotesModel();
        $this->voteDetailsModel = new FacilityVoteDetailsModel();
        $this->session = session();
    }

    public function index()
    {
        // Redirect if not a facility user
        if ($this->session->get('facility_id') == 0) {
            return redirect()->to('/profile');
        }

        $facility = $this->facilityModel->find($this->session->get('facility_id'));
        if (!$facility) {
            return redirect()->to('/profile');
        }

        $data = [
            'session' => $this->session,
            'facility' => $facility,
            'clinicians' => [],
            'page' => 'votes'
        ];

        return view('components/header', [
            'title' => 'Handglove',
            'description' => '',
            'url' => BASE_URL,
            'keywords' => '',
            'meta' => [
                'title' => 'Handglove',
                'description' => '',
                'image' => IMG_URL . ''
            ],
            'styles' => [
                'plugins/font_awesome',
                'plugins/datatables',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap',
                COMPILED_ASSETS_PATH . 'css/components/fontawesome',
                COMPILED_ASSETS_PATH . 'css/components/owl',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-main',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-select',
                COMPILED_ASSETS_PATH . 'css/components/bootstrap-datepicker',
                COMPILED_ASSETS_PATH . 'css/components/toastr',
                COMPILED_ASSETS_PATH . 'css/components/global',
                COMPILED_ASSETS_PATH . 'css/components/animations',
                COMPILED_ASSETS_PATH . 'css/components/buttons',
                COMPILED_ASSETS_PATH . 'css/components/navigation_bar',
                COMPILED_ASSETS_PATH . 'css/components/footer',
                COMPILED_ASSETS_PATH . 'css/pages/facility_profile'
            ],
            'session' => $this->session
        ])
        . view('facility/manage', $data)
        . view('components/scripts_render', [
            'scripts' => [
                'https://code.jquery.com/jquery-3.5.1.min.js' => [
                    'integrity' => 'sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=',
                    'crossorigin' => 'anonymous'
                ],
                'https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.2/datatables.min.js',
                ASSETS_URL . 'js/plugins/popper.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-4.5.2/bootstrap.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-select.min.js',
                ASSETS_URL . 'js/components/global.min.js',
                ASSETS_URL . 'js/plugins/bootstrap-datepicker.js',
                ASSETS_URL . 'js/plugins/owl.carousel.min.js',
                ASSETS_URL . 'js/components/navigation_bar.min.js',
                ASSETS_URL . 'js/plugins/toastr.min.js',
                ASSETS_URL . 'js/components/notifications.min.js',
                ASSETS_URL . 'js/pages/facility_votes.min.js',
            ]
        ])
        . view('components/footer');
    }

    public function list()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $votes = $this->votesModel->where('client_id', $facilityId)->findAll();
        $formattedData = [];

        foreach ($votes as $vote) {
            $formattedData[] = [
                'name' => sprintf(
                    '<div><strong>%s - %s</strong></div>',
                    date("M d, Y", strtotime($vote['voting_start_date'])),
                    date("M d, Y", strtotime($vote['voting_end_date']))
                ),
                'description' => $vote['description'],
                'action' => sprintf(
                    '<div class="text-center">
                        <a href="javascript:;" data-id="%s" class="view-unit btn btn-yellow pl-2 pr-2 pt-1 pb-1" title="View Results"><i class="fa fa-search"></i></a>
                        <a href="javascript:;" data-id="%s" class="remove-unit btn btn-danger pl-2 pr-2 pt-1 pb-1" title="Delete"><i class="fa fa-trash"></i></a>
                    </div>',
                    $vote['id'],
                    $vote['id']
                )
            ];
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'data' => $formattedData
        ]);
    }

    public function insert()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $rules = [
            'voting_week' => [
                'label' => 'Voting Week',
                'rules' => 'required'
            ],
            'voting_type' => [
                'label' => 'Voting Type',
                'rules' => 'required'
            ],
            'description' => [
                'label' => 'Description',
                'rules' => 'required'
            ]
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => 0,
                'message_header' => 'Votes',
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $votingWeek = $this->request->getPost('voting_week');
        $dates = explode('|', $votingWeek);
        
        $item = [
            'client_id' => $facilityId,
            'voting_start_date' => $dates[0],
            'voting_end_date' => $dates[1],
            'description' => $this->request->getPost('description'),
            'voting_type' => $this->request->getPost('voting_type'),
            'status' => 10
        ];

        if ($this->votesModel->save($item)) {
            $voteId = $this->votesModel->getInsertID();
            $clinicians = $this->request->getPost('clincian') ?: [];

            foreach ($clinicians as $clinicianId) {
                $this->voteDetailsModel->save([
                    'voting_id' => $voteId,
                    'clinician_id' => $clinicianId
                ]);
            }

            return $this->response->setJSON([
                'success' => 1,
                'message_header' => 'Votes',
                'message' => 'Vote session successfully created.'
            ]);
        }

        return $this->response->setJSON([
            'success' => 0,
            'message_header' => 'Votes',
            'message' => 'Unable to save. Please try again later.'
        ]);
    }

    public function update()
    {
        // Logic seems similar to Units or placeholder, keeping it minimal as in original
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Update functionality not fully implemented.']);
    }

    public function get()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $voteId = $this->request->getPost('unitID'); // unitID used in original
        if (!$voteId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing parameter.']);
        }

        $vote = $this->votesModel->find($voteId);
        if (!$vote) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Record not found.']);
        }

        $vote['start_date'] = date("M d, Y", strtotime($vote['voting_start_date']));
        $vote['end_date'] = date("M d, Y", strtotime($vote['voting_end_date']));
        $vote['details'] = $this->voteDetailsModel->where('voting_id', $vote['id'])->findAll();

        $awardModel = new \App\Models\ClinicianAwardsModel();
        $award = $awardModel->where('voting_id', $voteId)->first();
        $vote['has_certificate'] = !empty($award);
        $vote['winner_clinician_id'] = $award ? $award['clinician_id'] : 0;

        if (!empty($vote['details'])) {
            $totalVotes = 0;
            $maxVotes = -1;
            $potentialWinnerId = 0;

            foreach ($vote['details'] as &$detail) {
                $totalVotes += $detail['votes'];
                if ($detail['votes'] > $maxVotes) {
                    $maxVotes = $detail['votes'];
                    $potentialWinnerId = $detail['clinician_id'];
                }
                $detail['clinician_details'] = $this->clinicianModel->select('tbl_clinicians.*,tbl_clinician_types.name as clinician_type_name, tbl_clinician_types.grouping as clinician_type_grouping')->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type')->find($detail['clinician_id']);
            }
            unset($detail);

            $vote['total_votes'] = $totalVotes;
            $vote['potential_winner_id'] = $potentialWinnerId;

            foreach ($vote['details'] as &$detail) {
                $detail['vote_percentage'] = $totalVotes > 0 ? ($detail['votes'] / $totalVotes) * 100 : 0;
            }
            unset($detail);
        }

        return $this->response->setJSON([
            'success' => 1,
            'message' => '',
            'unit' => $vote // returning as 'unit' to match original key
        ]);
    }

    public function generate_certificate()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $voteId = $this->request->getPost('unitID');
        if (!$voteId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing parameter.']);
        }

        $vote = $this->votesModel->where('client_id', $facilityId)->find($voteId);
        if (!$vote) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Record not found.']);
        }

        $awardModel = new \App\Models\ClinicianAwardsModel();
        $existing = $awardModel->where('voting_id', $voteId)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Certificate already generated for this session.']);
        }

        $details = $this->voteDetailsModel->where('voting_id', $voteId)->orderBy('votes', 'DESC')->first();
        if (!$details || $details['votes'] == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'No winner found or no votes cast yet.']);
        }

        $item = [
            'voting_id' => $voteId,
            'clinician_id' => $details['clinician_id'],
            'client_id' => $facilityId,
            'award_name' => 'Employee of the Week', // Default award name
            'award_date' => $vote['voting_end_date'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($awardModel->save($item)) {
            return $this->response->setJSON([
                'success' => 1,
                'message_header' => 'Success',
                'message' => 'Certificate successfully generated for the winner.'
            ]);
        }

        return $this->response->setJSON(['success' => 0, 'message' => 'Unable to generate certificate.']);
    }

    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $voteId = $this->request->getPost('unitID');
        if (!$voteId) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Missing parameter.']);
        }

        $vote = $this->votesModel->where('client_id', $facilityId)->find($voteId);
        if (!$vote) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Record not found or unauthorized.']);
        }

        // Delete details first
        $this->voteDetailsModel->where('voting_id', $voteId)->delete();
        
        // Delete award if any
        $awardModel = new \App\Models\ClinicianAwardsModel();
        $awardModel->where('voting_id', $voteId)->delete();
        
        // Delete main record
        if ($this->votesModel->delete($voteId)) {
            return $this->response->setJSON([
                'success' => 1,
                'message_header' => 'Votes',
                'message' => 'Voting session successfully deleted.'
            ]);
        }

        return $this->response->setJSON([
            'success' => 0,
            'message_header' => 'Votes',
            'message' => 'Unable to delete. Please try again later.'
        ]);
    }

    public function get_clinicians_by_week()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Invalid request.']);
        }

        $facilityId = $this->session->get('facility_id');
        if ($facilityId == 0) {
            return $this->response->setJSON(['success' => 0, 'message' => 'Unauthorized.']);
        }

        $votingWeek = $this->request->getPost('voting_week');
        $votingType = $this->request->getPost('voting_type');

        if (!$votingWeek || !$votingType) {
            return $this->response->setJSON(['success' => 1, 'clinicians' => []]);
        }

        $dates = explode('|', $votingWeek);
        $startDate = $dates[0];
        $endDate = $dates[1];

        $clinicians = $this->clinicianModel->select('tbl_clinicians.*, tbl_clinician_types.name as clinician_type_name, tbl_clinician_types.grouping as clinician_type_grouping')
            ->join('tbl_clinician_types', 'tbl_clinician_types.id = tbl_clinicians.type')
            ->join('tbl_shift_clinicians', 'tbl_shift_clinicians.clinician_id = tbl_clinicians.id')
            ->join('tbl_shifts', 'tbl_shifts.id = tbl_shift_clinicians.shift_id')
            ->where('tbl_shifts.client_id', $facilityId)
            ->where('tbl_shifts.start_date >=', $startDate)
            ->where('tbl_shifts.start_date <=', $endDate)
            ->where('tbl_clinician_types.grouping', $votingType)
            ->groupBy('tbl_clinicians.id')
            ->findAll();

        return $this->response->setJSON([
            'success' => 1,
            'clinicians' => $clinicians
        ]);
    }
}
