<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Earnings Statement - <?= esc($clinician['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-bg: #f8f9fa;
            --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --green-active: #76A31B;
            --purple-tax: #9b59b6;
            --red-deduction: #e74c3c;
            --text-dark: #2d3436;
            --text-muted: #636e72;
            --accent: #00cec9;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--primary-bg);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .stub-wrapper {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 15px;
        }

        .premium-card {
            background: #fff;
            border-radius: 12px;
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .top-stats-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            align-items: center;
        }

        .chart-box {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }

        .chart-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .chart-label .percent {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--green-active);
            display: block;
        }

        .chart-label .text {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
        }

        .clinician-info h4 { font-weight: 700; margin-bottom: 5px; }
        .meta-info { font-size: 0.85rem; color: var(--text-muted); }

        .nav-tabs-premium {
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }
        .nav-tabs-premium .nav-link {
            border: none;
            color: var(--text-muted);
            font-weight: 600;
            padding: 10px 25px;
            transition: all 0.2s;
        }
        .nav-tabs-premium .nav-link.active {
            color: var(--accent);
            border-bottom: 3px solid var(--accent);
            background: none;
        }

        .net-pay-hero {
            padding: 30px;
            text-align: left;
        }
        .net-pay-hero .date { font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }
        .net-pay-hero .amount { font-size: 2.8rem; font-weight: 700; margin-top: 5px; }

        .accordion-premium .card {
            border: none;
            background: none;
        }
        .accordion-premium .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f1f1;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .accordion-premium .card-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        .accordion-premium .card-header .header-total {
            font-weight: 500;
            color: var(--text-muted);
        }

        .gross-pay-value { color: var(--green-active); font-size: 1.8rem; font-weight: 700; }
        .tax-value { color: #d63031; font-size: 1.8rem; font-weight: 700; }

        .progress-bar-premium {
            height: 6px;
            border-radius: 3px;
            margin: 10px 0;
        }

        .table-premium { font-size: 0.9rem; }
        .table-premium th { border-top: none; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }

        @media print {
            .btn-print, .nav-tabs-premium { display: none; }
            body { background: #fff; }
            .stub-wrapper { margin: 0; max-width: 100%; }
            .premium-card { box-shadow: none; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="stub-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-2 btn-print">
        <div>
            <?php if ($stub['status'] == 20): ?>
                <span class="badge badge-success px-3 py-2" style="font-size: 1rem;"><i class="fa fa-check-circle"></i> PAID</span>
            <?php else: ?>
                <a href="<?= base_url('admin/payroll/mark-as-paid/' . $stub['id']) ?>" class="btn btn-success px-4 mr-2"><i class="fa fa-money-bill-wave"></i> Mark as Paid</a>
            <?php endif; ?>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary px-4"><i class="fa fa-print"></i> Print Stub</button>
    </div>

    <!-- Header Section -->
    <div class="premium-card p-4">
        <div class="top-stats-container">
            <div class="chart-box">
                <canvas id="payChart"></canvas>
                <div class="chart-label">
                    <span class="percent"><?= round(($stub['net_pay'] / $stub['gross_pay']) * 100) ?>%</span>
                    <span class="text">Take Home</span>
                </div>
            </div>
            
            <div class="clinician-info text-center">
                <h4><?= esc($clinician['name']) ?></h4>
                <div class="meta-info">
                   Clinician ID: <?= str_pad($clinician['id'], 7, '0', STR_PAD_LEFT) ?><br>
                   <?= esc($clinician['address'] ?? 'Active Personnel') ?><br>
                   USA
                </div>
            </div>

            <div class="pay-details text-right meta-info">
                <strong>Pay Period</strong><br>
                <?= date('M d, Y', strtotime($period['start_date'])) ?> - <?= date('M d, Y', strtotime($period['end_date'])) ?><br>
                <strong>Pay Date</strong><br>
                <?= date('M d, Y', strtotime($period['pay_date'])) ?><br>
                <strong>Check #</strong><br>
                <?= str_pad($stub['id'], 8, '0', STR_PAD_LEFT) ?>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs nav-tabs-premium" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="current-tab" data-toggle="tab" href="#current" role="tab">Current</a>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            
            <!-- Net Pay Hero -->
            <div class="premium-card net-pay-hero">
                <div class="date"><?= date('F d, Y', strtotime($period['pay_date'])) ?></div>
                <div class="amount text-dark">$<?= number_format($stub['net_pay'], 2) ?></div>
            </div>

            <!-- Accordions -->
            <div class="accordion accordion-premium" id="payStubAccordion">
                
                <!-- Earnings / Gross Pay Section -->
                <div class="premium-card mb-3">
                    <div class="card-header" id="headingGross" data-toggle="collapse" data-target="#collapseGross">
                        <h5>Gross Pay</h5>
                        <div class="header-total">$<?= number_format($stub['gross_pay'], 2) ?> <i class="fa fa-chevron-down ml-2 small"></i></div>
                    </div>

                    <div id="collapseGross" class="collapse show" data-parent="#payStubAccordion">
                        <div class="card-body px-4 pb-4">
                            <div class="gross-pay-value mb-1">$<?= number_format($stub['gross_pay'], 2) ?></div>
                            <div class="progress progress-bar-premium">
                                <div class="progress-bar" style="width: 100%; background-color: var(--green-active);"></div>
                            </div>
                            <div class="small text-muted mb-4"><?= number_format($stub['total_hours'], 2) ?> Total Hours</div>

                            <table class="table table-premium mb-0">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Hours</th>
                                        <th class="text-right">Rate</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($details as $row): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($row['start_date'])) ?></td>
                                        <td><?= number_format($row['actual_hours'], 2) ?></td>
                                        <td class="text-right">$<?= number_format($row['rate'], 2) ?></td>
                                        <td class="text-right">$<?= number_format($row['actual_amount'], 2) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Taxes and Deductions Section -->
                <div class="premium-card mb-3">
                    <div class="card-header collapsed" id="headingTax" data-toggle="collapse" data-target="#collapseTax">
                        <h5>Taxes and Deductions</h5>
                        <div class="header-total text-danger">-$<?= number_format($stub['total_deductions'], 2) ?> <i class="fa fa-chevron-down ml-2 small"></i></div>
                    </div>

                    <div id="collapseTax" class="collapse" data-parent="#payStubAccordion">
                        <div class="card-body px-4 pb-4">
                            <div class="tax-value mb-1">$<?= number_format($stub['total_deductions'], 2) ?></div>
                            <div class="progress progress-bar-premium">
                                <div class="progress-bar" style="width: <?= ($stub['total_deductions'] / $stub['gross_pay']) * 100 ?>%; background-color: #d63031;"></div>
                            </div>
                            <div class="small text-muted mb-4"><?= round(($stub['total_deductions'] / $stub['gross_pay']) * 100, 1) ?>% of Gross Pay</div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="font-weight-bold small text-uppercase mb-3">Employee Taxes</h6>
                                    <?php if (empty($deductions)): ?>
                                        <div class="text-muted small">No taxes applied to this period.</div>
                                    <?php else: ?>
                                        <?php foreach ($deductions as $ded): ?>
                                        <div class="d-flex justify-content-between mb-2 small">
                                            <span><?= esc($ded['tax_name']) ?> (<?= number_format($ded['percentage'], 1) ?>%)</span>
                                            <span class="font-weight-500">$<?= number_format($ded['amount'], 2) ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pay Info Summary Section -->
                <div class="premium-card">
                    <div class="card-header collapsed" id="headingInfo" data-toggle="collapse" data-target="#collapseInfo">
                        <h5>Pay Info</h5>
                        <div class="header-total">Summary <i class="fa fa-chevron-down ml-2 small"></i></div>
                    </div>
                    <div id="collapseInfo" class="collapse" data-parent="#payStubAccordion">
                        <div class="card-body px-4 py-4">
                            <div class="row">
                                <div class="col-8">Earnings</div>
                                <div class="col-4 text-right">$<?= number_format($stub['gross_pay'], 2) ?></div>
                            </div>
                            <div class="row text-muted small mt-1 ml-2">
                                <div class="col-8">Gross Earnings</div>
                                <div class="col-4 text-right">$<?= number_format($stub['gross_pay'], 2) ?></div>
                            </div>
                            <hr class="my-2">
                            <div class="row text-danger">
                                <div class="col-8">Taxes</div>
                                <div class="col-4 text-right">($<?= number_format($stub['total_deductions'], 2) ?>)</div>
                            </div>
                            <hr class="my-2">
                            <div class="row font-weight-bold">
                                <div class="col-8 text-uppercase">Take Home Earnings</div>
                                <div class="col-4 text-right text-dark">$<?= number_format($stub['net_pay'], 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center mt-5 mb-5 small text-muted">
        This is a generated earnings statement by Handglove Administration.<br>
        For inquiries, please contact support@handglove.com
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Initialize Donut Chart
    const ctx = document.getElementById('payChart').getContext('2d');
    const netPay = <?= $stub['net_pay'] ?>;
    const taxes = <?= $stub['total_deductions'] ?>;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [netPay, taxes],
                backgroundColor: ['#76A31B', '#9b59b6'],
                borderWidth: 0,
                cutout: '85%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: { enabled: false }
            }
        }
    });

    // Auto-expand/collapse smooth behavior
    $('.accordion-premium .card-header').click(function() {
        $(this).find('i').toggleClass('fa-chevron-down fa-chevron-up');
    });
</script>

</body>
</html>
