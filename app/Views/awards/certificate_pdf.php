<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }
        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 50px;
            box-sizing: border-box;
            border: 20px solid #f8f9fa;
            position: relative;
        }
        .certificate-border {
            border: 5px solid #ffc107;
            padding: 40px;
            height: 100%;
            box-sizing: border-box;
            text-align: center;
            background-color: #fff;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            width: 150px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 48pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 5px;
        }
        .subtitle {
            font-size: 18pt;
            font-style: italic;
            color: #555;
            margin-bottom: 50px;
        }
        .presented-to {
            font-size: 14pt;
            margin-bottom: 10px;
        }
        .recipient-name {
            font-size: 36pt;
            font-weight: bold;
            color: #ffc107;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .description {
            font-size: 16pt;
            margin-bottom: 50px;
            line-height: 1.6;
            padding: 0 50px;
        }
        .award-name {
            font-weight: bold;
            color: #2c3e50;
        }
        .footer {
            margin-top: 50px;
            width: 100%;
        }
        .signature-section {
            display: inline-block;
            width: 40%;
            border-top: 2px solid #333;
            margin: 0 5%;
            padding-top: 10px;
        }
        .signature-label {
            font-size: 12pt;
            font-weight: bold;
        }
        .date-section {
            font-size: 14pt;
            margin-top: 30px;
        }
        .medal-icon {
            position: absolute;
            bottom: 50px;
            right: 50px;
            width: 100px;
            opacity: 0.2;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="header">
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Handglove Logo" class="logo">
                <h1 class="title">Certificate</h1>
                <p class="subtitle">of Achievement</p>
            </div>

            <p class="presented-to">This certificate is proudly presented to</p>
            <div class="recipient-name"><?= htmlspecialchars($clinician['name']) ?></div>

            <p class="description">
                In recognition of exceptional performance and dedication as the<br>
                <span class="award-name"><?= htmlspecialchars($award['award_name']) ?></span><br>
                at <?= htmlspecialchars($facility['company_name']) ?>.
            </p>

            <div class="footer">
                <div class="signature-section">
                    <div class="signature-label"><?= htmlspecialchars($facility['company_name']) ?></div>
                    <div class="small">Awarding Facility</div>
                </div>
                <div class="signature-section">
                    <div class="signature-label">Handglove Management</div>
                    <div class="small">Verified Partner</div>
                </div>
                <div class="date-section">
                    Awarded on <?= date("F d, Y", strtotime($award['award_date'])) ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
