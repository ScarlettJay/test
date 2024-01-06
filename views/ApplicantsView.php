<?php

namespace PHPMaker2022\project1;

// Page object
$ApplicantsView = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { applicants: currentTable } });
var currentForm, currentPageID;
var fapplicantsview;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fapplicantsview = new ew.Form("fapplicantsview", "view");
    currentPageID = ew.PAGE_ID = "view";
    currentForm = fapplicantsview;
    loadjs.done("fapplicantsview");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php } ?>
<?php if (!$Page->isExport()) { ?>
<div class="btn-toolbar ew-toolbar">
<?php $Page->ExportOptions->render("body") ?>
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fapplicantsview" id="fapplicantsview" class="ew-form ew-view-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="applicants">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<table class="table table-striped table-bordered table-hover table-sm ew-view-table">
<?php if ($Page->applicant_id->Visible) { // applicant_id ?>
    <tr id="r_applicant_id"<?= $Page->applicant_id->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_applicant_id"><?= $Page->applicant_id->caption() ?></span></td>
        <td data-name="applicant_id"<?= $Page->applicant_id->cellAttributes() ?>>
<span id="el_applicants_applicant_id">
<span<?= $Page->applicant_id->viewAttributes() ?>>
<?= $Page->applicant_id->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->registration_id->Visible) { // registration_id ?>
    <tr id="r_registration_id"<?= $Page->registration_id->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_registration_id"><?= $Page->registration_id->caption() ?></span></td>
        <td data-name="registration_id"<?= $Page->registration_id->cellAttributes() ?>>
<span id="el_applicants_registration_id">
<span<?= $Page->registration_id->viewAttributes() ?>>
<?= $Page->registration_id->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->full_name->Visible) { // full_name ?>
    <tr id="r_full_name"<?= $Page->full_name->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_full_name"><?= $Page->full_name->caption() ?></span></td>
        <td data-name="full_name"<?= $Page->full_name->cellAttributes() ?>>
<span id="el_applicants_full_name">
<span<?= $Page->full_name->viewAttributes() ?>>
<?= $Page->full_name->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->photo->Visible) { // photo ?>
    <tr id="r_photo"<?= $Page->photo->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_photo"><?= $Page->photo->caption() ?></span></td>
        <td data-name="photo"<?= $Page->photo->cellAttributes() ?>>
<span id="el_applicants_photo">
<span>
<?= GetFileViewTag($Page->photo, $Page->photo->getViewValue(), false) ?>
</span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->ic_number->Visible) { // ic_number ?>
    <tr id="r_ic_number"<?= $Page->ic_number->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_ic_number"><?= $Page->ic_number->caption() ?></span></td>
        <td data-name="ic_number"<?= $Page->ic_number->cellAttributes() ?>>
<span id="el_applicants_ic_number">
<span<?= $Page->ic_number->viewAttributes() ?>>
<?= $Page->ic_number->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->_email->Visible) { // email ?>
    <tr id="r__email"<?= $Page->_email->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants__email"><?= $Page->_email->caption() ?></span></td>
        <td data-name="_email"<?= $Page->_email->cellAttributes() ?>>
<span id="el_applicants__email">
<span<?= $Page->_email->viewAttributes() ?>>
<?= $Page->_email->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->phone_number->Visible) { // phone_number ?>
    <tr id="r_phone_number"<?= $Page->phone_number->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_phone_number"><?= $Page->phone_number->caption() ?></span></td>
        <td data-name="phone_number"<?= $Page->phone_number->cellAttributes() ?>>
<span id="el_applicants_phone_number">
<span<?= $Page->phone_number->viewAttributes() ?>>
<?= $Page->phone_number->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->education_level->Visible) { // education_level ?>
    <tr id="r_education_level"<?= $Page->education_level->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_education_level"><?= $Page->education_level->caption() ?></span></td>
        <td data-name="education_level"<?= $Page->education_level->cellAttributes() ?>>
<span id="el_applicants_education_level">
<span<?= $Page->education_level->viewAttributes() ?>>
<?= $Page->education_level->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->job_position->Visible) { // job_position ?>
    <tr id="r_job_position"<?= $Page->job_position->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_job_position"><?= $Page->job_position->caption() ?></span></td>
        <td data-name="job_position"<?= $Page->job_position->cellAttributes() ?>>
<span id="el_applicants_job_position">
<span<?= $Page->job_position->viewAttributes() ?>>
<?= $Page->job_position->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->certificate->Visible) { // certificate ?>
    <tr id="r_certificate"<?= $Page->certificate->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_certificate"><?= $Page->certificate->caption() ?></span></td>
        <td data-name="certificate"<?= $Page->certificate->cellAttributes() ?>>
<span id="el_applicants_certificate">
<span>
<?= GetFileViewTag($Page->certificate, $Page->certificate->getViewValue(), false) ?>
</span>
</span>
</td>
    </tr>
<?php } ?>
<?php if ($Page->application_date->Visible) { // application_date ?>
    <tr id="r_application_date"<?= $Page->application_date->rowAttributes() ?>>
        <td class="<?= $Page->TableLeftColumnClass ?>"><span id="elh_applicants_application_date"><?= $Page->application_date->caption() ?></span></td>
        <td data-name="application_date"<?= $Page->application_date->cellAttributes() ?>>
<span id="el_applicants_application_date">
<span<?= $Page->application_date->viewAttributes() ?>>
<?= $Page->application_date->getViewValue() ?></span>
</span>
</td>
    </tr>
<?php } ?>
</table>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<?php if (!$Page->isExport()) { ?>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
