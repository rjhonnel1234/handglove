<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Schedule - <?= date('m/d/Y', strtotime($date)) ?></title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }
        .date-header {
            font-weight: bold;
            font-size: 11pt;
            float: left;
        }
        .logo-container {
            text-align: center;
            width: 100%;
            position: absolute;
            top: 0;
        }
        .logo {
            width: 40px;
        }
        .print-time {
            float: right;
            font-size: 8pt;
            color: #666;
        }
        .clear {
            clear: both;
        }
        .unit-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .unit-title {
            text-align: center;
            font-size: 22pt;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 1px solid #000;
        }
        .schedule-table th {
            background-color: #fff;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
        }
        .schedule-table td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 0;
        }
        .shift-column {
            width: 33.33%;
        }
        .inner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .inner-table th {
            background-color: #fff;
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            font-size: 9pt;
            padding: 4px;
            text-align: left;
        }
        .inner-table th:last-child {
            border-right: none;
        }
        .inner-table td {
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #000;
            padding: 4px 6px;
            font-size: 9pt;
            line-height: 1.4;
        }
        .inner-table td:last-child {
            border-right: none;
        }
        .inner-table tr:last-child td {
            border-bottom: none;
        }
        
        .col-name { width: 45%; }
        .col-other { width: 25%; }
        .col-pos { width: 30%; text-align: right; }
        
        .staff-name {
            font-weight: normal;
        }
        .staff-pos {
            font-size: 9pt;
            font-weight: normal;
        }
        .empty-notice {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 20px;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="date-header"><?= date('Y-m-d', strtotime($date)) ?> - <?= date('l', strtotime($date)) ?></div>
        <div class="logo-container">
            <!-- Handglove Logo would go here -->
        </div>
        <div class="print-time">Time of print: <?= date('F d') ?> at <?= date('h:i A') ?></div>
        <div class="clear"></div>
    </div>

    <?php foreach ($units as $unit): ?>
        <?php 
        $unitId = $unit['id'];
        $hasAssignments = isset($groupedAssignments[$unitId]) && (
            !empty($groupedAssignments[$unitId]['day']) || 
            !empty($groupedAssignments[$unitId]['mid']) || 
            !empty($groupedAssignments[$unitId]['night'])
        );
        if (!$hasAssignments) continue; 
        ?>
        <div class="unit-section">
            <div class="unit-title"><?= htmlspecialchars($unit['name']) ?></div>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Day Shift</th>
                        <th>Evening Shift</th>
                        <th>Night Shift</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="shift-column">
                            <?php if (!empty($groupedAssignments[$unitId]['day'])): ?>
                                <table class="inner-table">
                                    <thead>
                                        <tr>
                                            <th class="col-name">Name</th>
                                            <th class="col-other">Other</th>
                                            <th class="col-pos">Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedAssignments[$unitId]['day'] as $staff): ?>
                                            <tr>
                                                <td class="col-name"><span class="staff-name"><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></span></td>
                                                <td class="col-other"><?= htmlspecialchars($staff['role_detail'] ?? '') ?></td>
                                                <td class="col-pos"><span class="staff-pos"><?= htmlspecialchars($staff['clinician_type_name']) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-notice">No staff assigned</div>
                            <?php endif; ?>
                        </td>
                        <td class="shift-column">
                            <?php if (!empty($groupedAssignments[$unitId]['mid'])): ?>
                                <table class="inner-table">
                                    <thead>
                                        <tr>
                                            <th class="col-name">Name</th>
                                            <th class="col-other">Other</th>
                                            <th class="col-pos">Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedAssignments[$unitId]['mid'] as $staff): ?>
                                            <tr>
                                                <td class="col-name"><span class="staff-name"><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></span></td>
                                                <td class="col-other"><?= htmlspecialchars($staff['role_detail'] ?? '') ?></td>
                                                <td class="col-pos"><span class="staff-pos"><?= htmlspecialchars($staff['clinician_type_name']) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-notice">No staff assigned</div>
                            <?php endif; ?>
                        </td>
                        <td class="shift-column">
                            <?php if (!empty($groupedAssignments[$unitId]['night'])): ?>
                                <table class="inner-table">
                                    <thead>
                                        <tr>
                                            <th class="col-name">Name</th>
                                            <th class="col-other">Other</th>
                                            <th class="col-pos">Position</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupedAssignments[$unitId]['night'] as $staff): ?>
                                            <tr>
                                                <td class="col-name"><span class="staff-name"><?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></span></td>
                                                <td class="col-other"><?= htmlspecialchars($staff['role_detail'] ?? '') ?></td>
                                                <td class="col-pos"><span class="staff-pos"><?= htmlspecialchars($staff['clinician_type_name']) ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="empty-notice">No staff assigned</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
</body>
</html>
