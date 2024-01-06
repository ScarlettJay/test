<?php

namespace PHPMaker2022\project1;

// Page object
$ApplicantsDelete = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { applicants: currentTable } });
var currentForm, currentPageID;
var fapplicantsdelete;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fapplicantsdelete = new ew.Form("fapplicantsdelete", "delete");
    currentPageID = ew.PAGE_ID = "delete";
    currentForm = fapplicantsdelete;
    loadjs.done("fapplicantsdelete");
});
</script>
<script>
loadjs.ready("head", function () {
    // Write your table-specific client script here, no need to add script tags.
});
</script>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<form name="fapplicantsdelete" id="fapplicantsdelete" class="ew-form ew-delete-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="applicants">
<input type="hidden" name="action" id="action" value="delete">
<?php foreach ($Page->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode(Config("COMPOSITE_KEY_SEPARATOR"), $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?= HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="card ew-card ew-grid">
<div class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<table class="table table-bordered table-hover table-sm ew-table">
    <thead>
    <tr class="ew-table-header">
<?php if ($Page->applicant_id->Visible) { // applicant_id ?>
        <th class="<?= $Page->applicant_id->headerCellClass() ?>"><span id="elh_applicants_applicant_id" class="applicants_applicant_id"><?= $Page->applicant_id->caption() ?></span></th>
<?php } ?>
<?php if ($Page->registration_id->Visible) { // registration_id ?>
        <th class="<?= $Page->registration_id->headerCellClass() ?>"><span id="elh_applicants_registration_id" class="applicants_registration_id"><?= $Page->registration_id->caption() ?></span></th>
<?php } ?>
<?php if ($Page->full_name->Visible) { // full_name ?>
        <th class="<?= $Page->full_name->headerCellClass() ?>"><span id="elh_applicants_full_name" class="applicants_full_name"><?= $Page->full_name->caption() ?></span></th>
<?php } ?>
<?php if ($Page->ic_number->Visible) { // ic_number ?>
        <th class="<?= $Page->ic_number->headerCellClass() ?>"><span id="elh_applicants_ic_number" class="applicants_ic_number"><?= $Page->ic_number->caption() ?></span></th>
<?php } ?>
<?php if ($Page->_email->Visible) { // email ?>
        <th class="<?= $Page->_email->headerCellClass() ?>"><span id="elh_applicants__email" class="applicants__email"><?= $Page->_email->caption() ?></span></th>
<?php } ?>
<?php if ($Page->phone_number->Visible) { // phone_number ?>
        <th class="<?= $Page->phone_number->headerCellClass() ?>"><span id="elh_applicants_phone_number" class="applicants_phone_number"><?= $Page->phone_number->caption() ?></span></th>
<?php } ?>
<?php if ($Page->education_level->Visible) { // education_level ?>
        <th class="<?= $Page->education_level->headerCellClass() ?>"><span id="elh_applicants_education_level" class="applicants_education_level"><?= $Page->education_level->caption() ?></span></th>
<?php } ?>
<?php if ($Page->job_position->Visible) { // job_position ?>
        <th class="<?= $Page->job_position->headerCellClass() ?>"><span id="elh_applicants_job_position" class="applicants_job_position"><?= $Page->job_position->caption() ?></span></th>
<?php } ?>
<?php if ($Page->application_date->Visible) { // application_date ?>
        <th class="<?= $Page->application_date->headerCellClass() ?>"><span id="elh_applicants_application_date" class="applicants_application_date"><?= $Page->application_date->caption() ?></span></th>
<?php } ?>
    </tr>
    </thead>
    <tbody>
<?php
$Page->RecordCount = 0;
$i = 0;
while (!$Page->Recordset->EOF) {
    $Page->RecordCount++;
    $Page->RowCount++;

    // Set row properties
    $Page->resetAttributes();
    $Page->RowType = ROWTYPE_VIEW; // View

    // Get the field contents
    $Page->loadRowValues($Page->Recordset);

    // Render row
    $Page->renderRow();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php if ($Page->applicant_id->Visible) { // applicant_id ?>
        <td<?= $Page->applicant_id->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_applicant_id" class="el_applicants_applicant_id">
<span<?= $Page->applicant_id->viewAttributes() ?>>
<?= $Page->applicant_id->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->registration_id->Visible) { // registration_id ?>
        <td<?= $Page->registration_id->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_registration_id" class="el_applicants_registration_id">
<span<?= $Page->registration_id->viewAttributes() ?>>
<?= $Page->registration_id->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->full_name->Visible) { // full_name ?>
        <td<?= $Page->full_name->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_full_name" class="el_applicants_full_name">
<span<?= $Page->full_name->viewAttributes() ?>>
<?= $Page->full_name->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->ic_number->Visible) { // ic_number ?>
        <td<?= $Page->ic_number->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_ic_number" class="el_applicants_ic_number">
<span<?= $Page->ic_number->viewAttributes() ?>>
<?= $Page->ic_number->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->_email->Visible) { // email ?>
        <td<?= $Page->_email->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants__email" class="el_applicants__email">
<span<?= $Page->_email->viewAttributes() ?>>
<?= $Page->_email->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->phone_number->Visible) { // phone_number ?>
        <td<?= $Page->phone_number->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_phone_number" class="el_applicants_phone_number">
<span<?= $Page->phone_number->viewAttributes() ?>>
<?= $Page->phone_number->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->education_level->Visible) { // education_level ?>
        <td<?= $Page->education_level->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_education_level" class="el_applicants_education_level">
<span<?= $Page->education_level->viewAttributes() ?>>
<?= $Page->education_level->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->job_position->Visible) { // job_position ?>
        <td<?= $Page->job_position->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_job_position" class="el_applicants_job_position">
<span<?= $Page->job_position->viewAttributes() ?>>
<?= $Page->job_position->getViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($Page->application_date->Visible) { // application_date ?>
        <td<?= $Page->application_date->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_application_date" class="el_applicants_application_date">
<span<?= $Page->application_date->viewAttributes() ?>>
<?= $Page->application_date->getViewValue() ?></span>
</span>
</td>
<?php } ?>
    </tr>
<?php
    $Page->Recordset->moveNext();
}
$Page->Recordset->close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("DeleteBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
</div>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
