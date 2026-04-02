<?php
$profilePic = $clinician['profile_pic_url'];
if (empty($profilePic)) {
    $profilePic = base_url('assets/img/blank-img.png');
}

$requestId = $shift['pending_replacements'][$clinician['id']] ?? null;
$isPendingReplacement = !empty($requestId);

$personnelClass = 'personnel-details dropdown';
if ($isPendingReplacement) {
    $personnelClass .= ' pending-replacement';
} else {
    if ($clinician['from_callout'] == 1) {
        $personnelClass .= ' replacement';
    }
}
?>

<div class="<?= $personnelClass ?>" data-shift-clinician-id="<?= $clinician['id'] ?>">

    <?php $detailsClickable = ($clinician['type'] == 'clinician'); ?>
    <div class="clinician-details" <?= $detailsClickable ? 'style="cursor: pointer;"' : '' ?>>
        <img class="img-responsive" src="<?= $profilePic ?>">
        <div class="clinician-name"><?= $clinician['display_name'] ?></div>

        <?php if ($isPendingReplacement): ?>
            <div class="badge badge-warning"
                style="position: absolute; top: -10px; right: -10px; font-size: 10px; border: 1px solid #fff;">Replacement
                Pending</div>
        <?php endif; ?>
    </div>

    <?php if (!empty($shift['alternative_units']) && !$clinician['is_clocked_in']): ?>
        <div class="transfer-unit-toggler dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            style="position: absolute; right: 5px; top: 5px; cursor: pointer; color: #718096;"><i
                class="fa fa-exchange-alt"></i></div>
        <div class="dropdown-menu">
            <h6 class="dropdown-header">Transfer to...</h6>
            <?php foreach ($shift['alternative_units'] as $altUnit): ?>
                <a class="dropdown-item transfer-unit-action" href="javascript:;" data-id="<?= $clinician['id'] ?>"
                    data-shift-id="<?= $altUnit['shift_id'] ?>"><?= $altUnit['unit_name'] ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="timeline-container shadow-sm rounded border"
        style="display: none; position: absolute; z-index: 1000; background: #fff; width: 250px; left: 0; top: 100%;">
    </div>
    <div class="clinician-type"><?= $clinician['display_type'] ?></div>

    <div class="clinician-actions">
        <ul>

            <?php if (session()->get('type') != 4): ?>
                <li class="">
                    <?php if ($clinician['type'] == 'clinician'): ?>
                        <?php
                        $hasPcc = ($clinician['has_pcc'] ?? false);
                        $pccIcon = (isset($clinician['pcc_status']) && $clinician['pcc_status'] == 20) || $hasPcc ? '<i class="fa fa-unlock"></i>' : '<i class="fa fa-lock"></i>';
                        $pccUsername = $clinician['pcc_username'] ?? '';
                        $pccPassword = $clinician['pcc_password'] ?? '';
                        ?>
                        <a href="javascript:;" class="pcc-action text-success" data-shift="<?= $shift['id'] ?>"
                            data-clin="<?= $clinician['clinician_id'] ?>" data-has-pcc="<?= $hasPcc ? '1' : '0' ?>"
                            data-username="<?= $pccUsername ?>" data-password="<?= $pccPassword ?>"
                            data-clinician-name="<?= $clinician['display_name'] ?>"><?= $pccIcon ?> PCC</a>
                    <?php endif; ?>
                </li>
                <li>
                    <a href="javascript:;" class="co-action text-warning" data-shift="<?= $shift['id'] ?>"
                        data-clin="<?= $clinician['id'] ?>"><i class="fa fa-phone-slash"></i> C/O</a>
                </li>
            <?php endif; ?>

            <?php if (session()->get('type') == 4): ?>
                <li class="">
                    <?php if ($clinician['type'] == 'clinician'): ?>
                        <a href="javascript:;" class="check-action text-success" data-shift="<?= $shift['id'] ?>"
                            data-clin="<?= $clinician['clinician_id'] ?>"><i class="fa fa-tasks"></i> CHECK</a>
                    <?php endif; ?>
                </li>
                <li>
                    <a href="javascript:;" class="dnr-action text-danger" data-shift="<?= $shift['id'] ?>"
                        data-clin="<?= $clinician['clinician_id'] ?>"><i class="fa fa-eye-slash"></i> DNR</a>
                </li>
            <?php endif; ?>


        </ul>
    </div>

</div>