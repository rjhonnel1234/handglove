<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AdminMenu extends BaseConfig
{
    public $menus = [
        'Main' => [
            [
                'title' => 'Dashboard',
                'url'   => 'admin/dashboard',
                'icon'  => 'fa fa-tachometer-alt'
            ],
            [
                'title' => 'Leads',
                'url'   => 'admin/leads',
                'icon'  => 'fa fa-users-cog'
            ],
            [
                'title' => 'Facilities',
                'url'   => 'admin/facilities',
                'icon'  => 'fa fa-hospital-alt'
            ],
            [
                'title' => 'Shifts',
                'url'   => 'admin/shifts',
                'icon'  => 'fa fa-calendar'
            ],
            [
                'title' => 'Clinicians',
                'url'   => 'admin/clinicians',
                'icon'  => 'fa fa-user-md'
            ],
            [
                'title' => 'Board of Advisors',
                'url'   => 'admin/board-of-directors',
                'icon'  => 'fa fa-users-cog'
            ],
            [
                'title' => 'Donors / Sponsors',
                'url'   => 'admin/donors',
                'icon'  => 'fa fa-users-cog'
            ],
            [
                'title' => 'Schedules',
                'url'   => 'admin/schedules',
                'icon'  => 'fa fa-calendar-alt'
            ],
        ],
        'Settings' => [
            [
                'title' => 'Clinician Types',
                'url'   => 'admin/donors',
                'icon'  => 'fa fa-user-nurse'
            ],
            [
                'title' => 'Shift Types',
                'url'   => 'admin/donors',
                'icon'  => 'fa fa-file-medical'
            ],
            [
                'title' => 'Agency Providers',
                'url'   => 'admin/agencies',
                'icon'  => 'fa fa-building'
            ],
            [
                'title' => 'Credential Types',
                'url'   => 'admin/donors',
                'icon'  => 'fa fa-certificate'
            ],
            [
                'title' => 'Institutions',
                'url'   => 'admin/donors',
                'icon'  => 'fa fa-hospital-alt'
            ],
            [
                'title' => 'General Settings',
                'url'   => 'admin/settings',
                'icon'  => 'fa fa-cog'
            ],
        ],
        'HR & Payroll' => [
            [
                'title' => 'Tax Types',
                'url'   => 'admin/tax-types',
                'icon'  => 'fa fa-funnel-dollar'
            ],
            [
                'title' => 'Payroll Setup',
                'url'   => 'admin/payroll/setup',
                'icon'  => 'fa fa-calculator'
            ],
        ],
        'Accounting' => [
            [
                'title' => 'Invoices',
                'url'   => 'admin/invoices',
                'icon'  => 'fa fa-file-invoice-dollar'
            ],
        ]
    ];
}
