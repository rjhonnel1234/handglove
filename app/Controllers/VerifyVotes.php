<?php

namespace App\Controllers;

use App\Models\FacilityVotesModel;
use App\Models\FacilityVoteDetailsModel;

class VerifyVotes extends BaseController
{
    public function index()
    {
        $votesModel = new FacilityVotesModel();
        $detailsModel = new FacilityVoteDetailsModel();

        $lastWeekStart = date('Y-m-d', strtotime('last monday -7 days'));
        $lastWeekEnd = date('Y-m-d', strtotime('last sunday'));

        $voteId = $votesModel->insert([
            'client_id' => 3,
            'voting_start_date' => $lastWeekStart,
            'voting_end_date' => $lastWeekEnd,
            'description' => 'Test GNA Vote',
            'voting_type' => 'gna',
            'status' => 10
        ]);

        $detailsModel->insert([
            'voting_id' => $voteId,
            'clinician_id' => 7, // Jennifer Mercene (GNA)
            'votes' => 5
        ]);

        $detailsModel->insert([
            'voting_id' => $voteId,
            'clinician_id' => 8, // Maya Raphael (GNA)
            'votes' => 3
        ]);

        $voteIdNurse = $votesModel->insert([
            'client_id' => 3,
            'voting_start_date' => $lastWeekStart,
            'voting_end_date' => $lastWeekEnd,
            'description' => 'Test Nurse Vote',
            'voting_type' => 'nurse',
            'status' => 10
        ]);

        $detailsModel->insert([
            'voting_id' => $voteIdNurse,
            'clinician_id' => 6, // Nurse
            'votes' => 10
        ]);

        $detailsModel->insert([
            'voting_id' => $voteIdNurse,
            'clinician_id' => 5, // Nurse
            'votes' => 8
        ]);

        echo "Test data inserted for GNA and Nurse winners. GNA ID: $voteId, Nurse ID: $voteIdNurse\n";
    }
}
        