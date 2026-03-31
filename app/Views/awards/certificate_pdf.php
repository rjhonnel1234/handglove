<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Certificate of Achievement</title>
    <style>
        /* DOMPDF CSS Support: 
           - cert.png serves as the overall background template.
           - Absolute positioning is used to place dynamic details.
        */
        @page {
            margin: 0;
            size: A4 landscape;
        }

        @font-face {
            font-family: 'Romantics';
            src: url('<?= FCPATH . "assets/webfonts/Romantics.ttf" ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Iowan';
            src: url('<?= FCPATH . "assets/webfonts/Iowan/Iowan-Old-Style.ttf" ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Iowan', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #333;
        }

        /* Certificate Container using cert.png as Background */
        .certificate-container {
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Dynamic Details Overlay */
        .recipient-name {
            position: absolute;
            top: 33%;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Romantics', 'Brush Script MT', cursive;
            font-size: 85pt;
            color: #2c2c2c;
            width: 100%;
            text-align: center;
            z-index: 10;
        }

        .recognition-overlay {
            position: absolute;
            top: 52%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10pt;
            line-height: 1.2;
            color: #555;
            width: 50%;
            text-align: center;
            z-index: 5;
        }

        .date-complete {
            font-weight: bold;
            color: #333;
        }

        /* Logos Section - Over placeholders */
        .logo-box {
            position: absolute;
            bottom: 125pt;
            z-index: 15;
        }

        .issuer-logo {
            left: 21%;
            width: 60pt;
            height: 60pt;
            text-align: center;
        }

        .partner-logo {
            left: 30%;
            width: 60pt;
            height: 60pt;
            text-align: center;
        }

        .logo-img {
            max-width: 100%;
            max-height: 100%;
            display: block;
            margin: 0 auto;
        }

        /* Signatory line name (over xxxxx NAME xxxxx) */
        .signatory-overlay {
            position: absolute;
            bottom: 147pt;
            right: 21%;
            width: 180pt;
            text-align: center;
            font-size: 11pt;
            color: #2c3e50;
            font-weight: bold;
            letter-spacing: 0.5pt;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="overlay"><img src="<?= base_url('assets/img/cert.png') ?>" style="width: 100%; height: 100%;"></div>
        <!-- Recipient Name in the script field -->
        <div class="recipient-name">
            <?= htmlspecialchars($clinician['name']) ?>
        </div>

        <!-- Recognition Text and Dynamic Date -->
        <div class="recognition-overlay">
            In recognition of your outstanding dedication, compassion, and commitment to excellence in patient care.
            Thank you for your tireless efforts, your heart for service, and for being an inspiration to those around
            you. Awarded this <span class="date-complete"><?= date('jS', strtotime($award['award_date'])) ?> of
                <?= date('F, Y', strtotime($award['award_date'])) ?></span>
        </div>

        <!-- Logos Overlay - Matching placeholders in cert.png -->
        <div class="logo-box issuer-logo">
            <?php if (!empty($facility['company_logo'])): ?>
                <img src="<?= $facility['company_logo'] ?>" class="logo-img">
            <?php endif; ?>
        </div>

        <div class="logo-box partner-logo">
            <img src="<?= base_url('assets/img/handglove-logo.png') ?>" class="logo-img">
        </div>

        <!-- Signatory Line Overlay -->
        <div class="signatory-overlay">
            <?php if (!empty($don)): ?>
                <?php if (!empty($don['signature'])): ?>
                    <img src="<?= $don['signature'] ?>" style="position: absolute; bottom: 15pt; left: 50%; transform: translateX(-50%); width: 120pt; max-height: 50pt;">
                <?php endif; ?>
                <?= htmlspecialchars(strtoupper($don['first_name'] . ' ' . $don['last_name'])) ?>
            <?php else: ?>
                HANDGLOVE MANAGEMENT
            <?php endif; ?>
        </div>
    </div>
</body>

</html>