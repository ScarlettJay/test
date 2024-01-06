<?php

namespace PHPMaker2022\project1;

// Page object
$ApplicantsList = &$Page;
?>
<?php if (!$Page->isExport()) { ?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { applicants: currentTable } });
var currentForm, currentPageID;
var fapplicantslist;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fapplicantslist = new ew.Form("fapplicantslist", "list");
    currentPageID = ew.PAGE_ID = "list";
    currentForm = fapplicantslist;
    fapplicantslist.formKeyCountName = "<?= $Page->FormKeyCountName ?>";
    loadjs.done("fapplicantslist");
});
var fapplicantssrch, currentSearchForm, currentAdvancedSearchForm;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object for search
    fapplicantssrch = new ew.Form("fapplicantssrch", "list");
    currentSearchForm = fapplicantssrch;

    // Dynamic selection lists

    // Filters
    fapplicantssrch.filterList = <?= $Page->getFilterList() ?>;
    loadjs.done("fapplicantssrch");
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
<?php if ($Page->TotalRecords > 0 && $Page->ExportOptions->visible()) { ?>
<?php $Page->ExportOptions->render("body") ?>
<?php } ?>
<?php if ($Page->ImportOptions->visible()) { ?>
<?php $Page->ImportOptions->render("body") ?>
<?php } ?>
<?php if ($Page->SearchOptions->visible()) { ?>
<?php $Page->SearchOptions->render("body") ?>
<?php } ?>
<?php if ($Page->FilterOptions->visible()) { ?>
<?php $Page->FilterOptions->render("body") ?>
<?php } ?>
</div>
<?php } ?>
<?php
$Page->renderOtherOptions();
?>
<?php if ($Security->canSearch()) { ?>
<?php if (!$Page->isExport() && !$Page->CurrentAction && $Page->hasSearchFields()) { ?>
<form name="fapplicantssrch" id="fapplicantssrch" class="ew-form ew-ext-search-form" action="<?= CurrentPageUrl(false) ?>">
<div id="fapplicantssrch_search_panel" class="mb-2 mb-sm-0 <?= $Page->SearchPanelClass ?>"><!-- .ew-search-panel -->
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="applicants">
<div class="ew-extended-search container-fluid">
<div class="row mb-0">
    <div class="col-sm-auto px-0 pe-sm-2">
        <div class="ew-basic-search input-group">
            <input type="search" name="<?= Config("TABLE_BASIC_SEARCH") ?>" id="<?= Config("TABLE_BASIC_SEARCH") ?>" class="form-control ew-basic-search-keyword" value="<?= HtmlEncode($Page->BasicSearch->getKeyword()) ?>" placeholder="<?= HtmlEncode($Language->phrase("Search")) ?>" aria-label="<?= HtmlEncode($Language->phrase("Search")) ?>">
            <input type="hidden" name="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" id="<?= Config("TABLE_BASIC_SEARCH_TYPE") ?>" class="ew-basic-search-type" value="<?= HtmlEncode($Page->BasicSearch->getType()) ?>">
            <button type="button" data-bs-toggle="dropdown" class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-split" aria-haspopup="true" aria-expanded="false">
                <span id="searchtype"><?= $Page->BasicSearch->getTypeNameShort() ?></span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "" ? " active" : "" ?>" form="fapplicantssrch" data-ew-action="search-type"><?= $Language->phrase("QuickSearchAuto") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "=" ? " active" : "" ?>" form="fapplicantssrch" data-ew-action="search-type" data-search-type="="><?= $Language->phrase("QuickSearchExact") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "AND" ? " active" : "" ?>" form="fapplicantssrch" data-ew-action="search-type" data-search-type="AND"><?= $Language->phrase("QuickSearchAll") ?></button>
                <button type="button" class="dropdown-item<?= $Page->BasicSearch->getType() == "OR" ? " active" : "" ?>" form="fapplicantssrch" data-ew-action="search-type" data-search-type="OR"><?= $Language->phrase("QuickSearchAny") ?></button>
            </div>
        </div>
    </div>
    <div class="col-sm-auto mb-3">
        <button class="btn btn-primary" name="btn-submit" id="btn-submit" type="submit"><?= $Language->phrase("SearchBtn") ?></button>
    </div>
</div>
</div><!-- /.ew-extended-search -->
</div><!-- /.ew-search-panel -->
</form>
<?php } ?>
<?php } ?>
<?php $Page->showPageHeader(); ?>
<?php
$Page->showMessage();
?>
<?php if ($Page->TotalRecords > 0 || $Page->CurrentAction) { ?>
<div class="card ew-card ew-grid<?php if ($Page->isAddOrEdit()) { ?> ew-grid-add-edit<?php } ?> applicants">
<form name="fapplicantslist" id="fapplicantslist" class="ew-form ew-list-form" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="applicants">
<div id="gmp_applicants" class="<?= ResponsiveTableClass() ?>card-body ew-grid-middle-panel">
<?php if ($Page->TotalRecords > 0 || $Page->isGridEdit()) { ?>
<table id="tbl_applicantslist" class="table table-bordered table-hover table-sm ew-table"><!-- .ew-table -->
<thead>
    <tr class="ew-table-header">
<?php
// Header row
$Page->RowType = ROWTYPE_HEADER;

// Render list options
$Page->renderListOptions();

// Render list options (header, left)
$Page->ListOptions->render("header", "left");
?>
<?php if ($Page->applicant_id->Visible) { // applicant_id ?>
        <th data-name="applicant_id" class="<?= $Page->applicant_id->headerCellClass() ?>"><div id="elh_applicants_applicant_id" class="applicants_applicant_id"><?= $Page->renderFieldHeader($Page->applicant_id) ?></div></th>
<?php } ?>
<?php if ($Page->registration_id->Visible) { // registration_id ?>
        <th data-name="registration_id" class="<?= $Page->registration_id->headerCellClass() ?>"><div id="elh_applicants_registration_id" class="applicants_registration_id"><?= $Page->renderFieldHeader($Page->registration_id) ?></div></th>
<?php } ?>
<?php if ($Page->full_name->Visible) { // full_name ?>
        <th data-name="full_name" class="<?= $Page->full_name->headerCellClass() ?>"><div id="elh_applicants_full_name" class="applicants_full_name"><?= $Page->renderFieldHeader($Page->full_name) ?></div></th>
<?php } ?>
<?php if ($Page->ic_number->Visible) { // ic_number ?>
        <th data-name="ic_number" class="<?= $Page->ic_number->headerCellClass() ?>"><div id="elh_applicants_ic_number" class="applicants_ic_number"><?= $Page->renderFieldHeader($Page->ic_number) ?></div></th>
<?php } ?>
<?php if ($Page->_email->Visible) { // email ?>
        <th data-name="_email" class="<?= $Page->_email->headerCellClass() ?>"><div id="elh_applicants__email" class="applicants__email"><?= $Page->renderFieldHeader($Page->_email) ?></div></th>
<?php } ?>
<?php if ($Page->phone_number->Visible) { // phone_number ?>
        <th data-name="phone_number" class="<?= $Page->phone_number->headerCellClass() ?>"><div id="elh_applicants_phone_number" class="applicants_phone_number"><?= $Page->renderFieldHeader($Page->phone_number) ?></div></th>
<?php } ?>
<?php if ($Page->education_level->Visible) { // education_level ?>
        <th data-name="education_level" class="<?= $Page->education_level->headerCellClass() ?>"><div id="elh_applicants_education_level" class="applicants_education_level"><?= $Page->renderFieldHeader($Page->education_level) ?></div></th>
<?php } ?>
<?php if ($Page->job_position->Visible) { // job_position ?>
        <th data-name="job_position" class="<?= $Page->job_position->headerCellClass() ?>"><div id="elh_applicants_job_position" class="applicants_job_position"><?= $Page->renderFieldHeader($Page->job_position) ?></div></th>
<?php } ?>
<?php if ($Page->application_date->Visible) { // application_date ?>
        <th data-name="application_date" class="<?= $Page->application_date->headerCellClass() ?>"><div id="elh_applicants_application_date" class="applicants_application_date"><?= $Page->renderFieldHeader($Page->application_date) ?></div></th>
<?php } ?>
<?php
// Render list options (header, right)
$Page->ListOptions->render("header", "right");
?>
    </tr>
</thead>
<tbody>
<?php
if ($Page->ExportAll && $Page->isExport()) {
    $Page->StopRecord = $Page->TotalRecords;
} else {
    // Set the last record to display
    if ($Page->TotalRecords > $Page->StartRecord + $Page->DisplayRecords - 1) {
        $Page->StopRecord = $Page->StartRecord + $Page->DisplayRecords - 1;
    } else {
        $Page->StopRecord = $Page->TotalRecords;
    }
}
$Page->RecordCount = $Page->StartRecord - 1;
if ($Page->Recordset && !$Page->Recordset->EOF) {
    // Nothing to do
} elseif ($Page->isGridAdd() && !$Page->AllowAddDeleteRow && $Page->StopRecord == 0) {
    $Page->StopRecord = $Page->GridAddRowCount;
}

// Initialize aggregate
$Page->RowType = ROWTYPE_AGGREGATEINIT;
$Page->resetAttributes();
$Page->renderRow();
while ($Page->RecordCount < $Page->StopRecord) {
    $Page->RecordCount++;
    if ($Page->RecordCount >= $Page->StartRecord) {
        $Page->RowCount++;

        // Set up key count
        $Page->KeyCount = $Page->RowIndex;

        // Init row class and style
        $Page->resetAttributes();
        $Page->CssClass = "";
        if ($Page->isGridAdd()) {
            $Page->loadRowValues(); // Load default values
            $Page->OldKey = "";
            $Page->setKey($Page->OldKey);
        } else {
            $Page->loadRowValues($Page->Recordset); // Load row values
            if ($Page->isGridEdit()) {
                $Page->OldKey = $Page->getKey(true); // Get from CurrentValue
                $Page->setKey($Page->OldKey);
            }
        }
        $Page->RowType = ROWTYPE_VIEW; // Render view

        // Set up row attributes
        $Page->RowAttrs->merge([
            "data-rowindex" => $Page->RowCount,
            "id" => "r" . $Page->RowCount . "_applicants",
            "data-rowtype" => $Page->RowType,
            "class" => ($Page->RowCount % 2 != 1) ? "ew-table-alt-row" : "",
        ]);
        if ($Page->isAdd() && $Page->RowType == ROWTYPE_ADD || $Page->isEdit() && $Page->RowType == ROWTYPE_EDIT) { // Inline-Add/Edit row
            $Page->RowAttrs->appendClass("table-active");
        }

        // Render row
        $Page->renderRow();

        // Render list options
        $Page->renderListOptions();
?>
    <tr <?= $Page->rowAttributes() ?>>
<?php
// Render list options (body, left)
$Page->ListOptions->render("body", "left", $Page->RowCount);
?>
    <?php if ($Page->applicant_id->Visible) { // applicant_id ?>
        <td data-name="applicant_id"<?= $Page->applicant_id->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_applicant_id" class="el_applicants_applicant_id">
<span<?= $Page->applicant_id->viewAttributes() ?>>
<?= $Page->applicant_id->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->registration_id->Visible) { // registration_id ?>
        <td data-name="registration_id"<?= $Page->registration_id->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_registration_id" class="el_applicants_registration_id">
<span<?= $Page->registration_id->viewAttributes() ?>>
<?= $Page->registration_id->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->full_name->Visible) { // full_name ?>
        <td data-name="full_name"<?= $Page->full_name->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_full_name" class="el_applicants_full_name">
<span<?= $Page->full_name->viewAttributes() ?>>
<?= $Page->full_name->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->ic_number->Visible) { // ic_number ?>
        <td data-name="ic_number"<?= $Page->ic_number->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_ic_number" class="el_applicants_ic_number">
<span<?= $Page->ic_number->viewAttributes() ?>>
<?= $Page->ic_number->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->_email->Visible) { // email ?>
        <td data-name="_email"<?= $Page->_email->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants__email" class="el_applicants__email">
<span<?= $Page->_email->viewAttributes() ?>>
<?= $Page->_email->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->phone_number->Visible) { // phone_number ?>
        <td data-name="phone_number"<?= $Page->phone_number->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_phone_number" class="el_applicants_phone_number">
<span<?= $Page->phone_number->viewAttributes() ?>>
<?= $Page->phone_number->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->education_level->Visible) { // education_level ?>
        <td data-name="education_level"<?= $Page->education_level->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_education_level" class="el_applicants_education_level">
<span<?= $Page->education_level->viewAttributes() ?>>
<?= $Page->education_level->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->job_position->Visible) { // job_position ?>
        <td data-name="job_position"<?= $Page->job_position->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_job_position" class="el_applicants_job_position">
<span<?= $Page->job_position->viewAttributes() ?>>
<?= $Page->job_position->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
    <?php if ($Page->application_date->Visible) { // application_date ?>
        <td data-name="application_date"<?= $Page->application_date->cellAttributes() ?>>
<span id="el<?= $Page->RowCount ?>_applicants_application_date" class="el_applicants_application_date">
<span<?= $Page->application_date->viewAttributes() ?>>
<?= $Page->application_date->getViewValue() ?></span>
</span>
</td>
    <?php } ?>
<?php
// Render list options (body, right)
$Page->ListOptions->render("body", "right", $Page->RowCount);
?>
    </tr>
<?php
    }
    if (!$Page->isGridAdd()) {
        $Page->Recordset->moveNext();
    }
}
?>
</tbody>
</table><!-- /.ew-table -->
<?php } ?>
</div><!-- /.ew-grid-middle-panel -->
<?php if (!$Page->CurrentAction) { ?>
<input type="hidden" name="action" id="action" value="">
<?php } ?>
</form><!-- /.ew-list-form -->
<?php
// Close recordset
if ($Page->Recordset) {
    $Page->Recordset->close();
}
?>
<?php if (!$Page->isExport()) { ?>
<div class="card-footer ew-grid-lower-panel">
<?php if (!$Page->isGridAdd()) { ?>
<form name="ew-pager-form" class="ew-form ew-pager-form" action="<?= CurrentPageUrl(false) ?>">
<?= $Page->Pager->render() ?>
</form>
<?php } ?>
<div class="ew-list-other-options">
<?php $Page->OtherOptions->render("body", "bottom") ?>
</div>
</div>
<?php } ?>
</div><!-- /.ew-grid -->
<?php } else { ?>
<div class="ew-list-other-options">
<?php $Page->OtherOptions->render("body") ?>
</div>
<?php } ?>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
<?php if (!$Page->isExport()) { ?>
<script>
// Field event handlers
loadjs.ready("head", function() {
    ew.addEventHandlers("applicants");
});
</script>
<script>
loadjs.ready("load", function () {
    // Write your table-specific startup script here, no need to add script tags.
});
</script>
<?php } ?>
