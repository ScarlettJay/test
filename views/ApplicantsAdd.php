<?php

namespace PHPMaker2022\project1;

// Page object
$ApplicantsAdd = &$Page;
?>
<script>
var currentTable = <?= JsonEncode($Page->toClientVar()) ?>;
ew.deepAssign(ew.vars, { tables: { applicants: currentTable } });
var currentForm, currentPageID;
var fapplicantsadd;
loadjs.ready(["wrapper", "head"], function () {
    var $ = jQuery;
    // Form object
    fapplicantsadd = new ew.Form("fapplicantsadd", "add");
    currentPageID = ew.PAGE_ID = "add";
    currentForm = fapplicantsadd;

    // Add fields
    var fields = currentTable.fields;
    fapplicantsadd.addFields([
        ["registration_id", [fields.registration_id.visible && fields.registration_id.required ? ew.Validators.required(fields.registration_id.caption) : null, ew.Validators.integer], fields.registration_id.isInvalid],
        ["full_name", [fields.full_name.visible && fields.full_name.required ? ew.Validators.required(fields.full_name.caption) : null], fields.full_name.isInvalid],
        ["photo", [fields.photo.visible && fields.photo.required ? ew.Validators.fileRequired(fields.photo.caption) : null], fields.photo.isInvalid],
        ["ic_number", [fields.ic_number.visible && fields.ic_number.required ? ew.Validators.required(fields.ic_number.caption) : null], fields.ic_number.isInvalid],
        ["_email", [fields._email.visible && fields._email.required ? ew.Validators.required(fields._email.caption) : null], fields._email.isInvalid],
        ["phone_number", [fields.phone_number.visible && fields.phone_number.required ? ew.Validators.required(fields.phone_number.caption) : null], fields.phone_number.isInvalid],
        ["education_level", [fields.education_level.visible && fields.education_level.required ? ew.Validators.required(fields.education_level.caption) : null], fields.education_level.isInvalid],
        ["job_position", [fields.job_position.visible && fields.job_position.required ? ew.Validators.required(fields.job_position.caption) : null], fields.job_position.isInvalid],
        ["certificate", [fields.certificate.visible && fields.certificate.required ? ew.Validators.fileRequired(fields.certificate.caption) : null], fields.certificate.isInvalid],
        ["application_date", [fields.application_date.visible && fields.application_date.required ? ew.Validators.required(fields.application_date.caption) : null, ew.Validators.datetime(fields.application_date.clientFormatPattern)], fields.application_date.isInvalid]
    ]);

    // Form_CustomValidate
    fapplicantsadd.customValidate = function(fobj) { // DO NOT CHANGE THIS LINE!
        // Your custom validation code here, return false if invalid.
        return true;
    }

    // Use JavaScript validation or not
    fapplicantsadd.validateRequired = ew.CLIENT_VALIDATE;

    // Dynamic selection lists
    fapplicantsadd.lists.education_level = <?= $Page->education_level->toClientList($Page) ?>;
    fapplicantsadd.lists.job_position = <?= $Page->job_position->toClientList($Page) ?>;
    loadjs.done("fapplicantsadd");
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
<form name="fapplicantsadd" id="fapplicantsadd" class="<?= $Page->FormClassName ?>" action="<?= CurrentPageUrl(false) ?>" method="post">
<?php if (Config("CHECK_TOKEN")) { ?>
<input type="hidden" name="<?= $TokenNameKey ?>" value="<?= $TokenName ?>"><!-- CSRF token name -->
<input type="hidden" name="<?= $TokenValueKey ?>" value="<?= $TokenValue ?>"><!-- CSRF token value -->
<?php } ?>
<input type="hidden" name="t" value="applicants">
<input type="hidden" name="action" id="action" value="insert">
<input type="hidden" name="modal" value="<?= (int)$Page->IsModal ?>">
<input type="hidden" name="<?= $Page->OldKeyName ?>" value="<?= $Page->OldKey ?>">
<div class="ew-add-div"><!-- page* -->
<?php if ($Page->registration_id->Visible) { // registration_id ?>
    <div id="r_registration_id"<?= $Page->registration_id->rowAttributes() ?>>
        <label id="elh_applicants_registration_id" for="x_registration_id" class="<?= $Page->LeftColumnClass ?>"><?= $Page->registration_id->caption() ?><?= $Page->registration_id->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->registration_id->cellAttributes() ?>>
<span id="el_applicants_registration_id">
<input type="<?= $Page->registration_id->getInputTextType() ?>" name="x_registration_id" id="x_registration_id" data-table="applicants" data-field="x_registration_id" value="<?= $Page->registration_id->EditValue ?>" size="30" placeholder="<?= HtmlEncode($Page->registration_id->getPlaceHolder()) ?>"<?= $Page->registration_id->editAttributes() ?> aria-describedby="x_registration_id_help">
<?= $Page->registration_id->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->registration_id->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->full_name->Visible) { // full_name ?>
    <div id="r_full_name"<?= $Page->full_name->rowAttributes() ?>>
        <label id="elh_applicants_full_name" for="x_full_name" class="<?= $Page->LeftColumnClass ?>"><?= $Page->full_name->caption() ?><?= $Page->full_name->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->full_name->cellAttributes() ?>>
<span id="el_applicants_full_name">
<input type="<?= $Page->full_name->getInputTextType() ?>" name="x_full_name" id="x_full_name" data-table="applicants" data-field="x_full_name" value="<?= $Page->full_name->EditValue ?>" size="30" maxlength="100" placeholder="<?= HtmlEncode($Page->full_name->getPlaceHolder()) ?>"<?= $Page->full_name->editAttributes() ?> aria-describedby="x_full_name_help">
<?= $Page->full_name->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->full_name->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->photo->Visible) { // photo ?>
    <div id="r_photo"<?= $Page->photo->rowAttributes() ?>>
        <label id="elh_applicants_photo" class="<?= $Page->LeftColumnClass ?>"><?= $Page->photo->caption() ?><?= $Page->photo->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->photo->cellAttributes() ?>>
<span id="el_applicants_photo">
<div id="fd_x_photo" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->photo->title() ?>" data-table="applicants" data-field="x_photo" name="x_photo" id="x_photo" lang="<?= CurrentLanguageID() ?>"<?= $Page->photo->editAttributes() ?> aria-describedby="x_photo_help"<?= ($Page->photo->ReadOnly || $Page->photo->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFile") ?></div>
</div>
<?= $Page->photo->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->photo->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_photo" id= "fn_x_photo" value="<?= $Page->photo->Upload->FileName ?>">
<input type="hidden" name="fa_x_photo" id= "fa_x_photo" value="0">
<input type="hidden" name="fs_x_photo" id= "fs_x_photo" value="16777215">
<input type="hidden" name="fx_x_photo" id= "fx_x_photo" value="<?= $Page->photo->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_photo" id= "fm_x_photo" value="<?= $Page->photo->UploadMaxFileSize ?>">
<table id="ft_x_photo" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->ic_number->Visible) { // ic_number ?>
    <div id="r_ic_number"<?= $Page->ic_number->rowAttributes() ?>>
        <label id="elh_applicants_ic_number" for="x_ic_number" class="<?= $Page->LeftColumnClass ?>"><?= $Page->ic_number->caption() ?><?= $Page->ic_number->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->ic_number->cellAttributes() ?>>
<span id="el_applicants_ic_number">
<input type="<?= $Page->ic_number->getInputTextType() ?>" name="x_ic_number" id="x_ic_number" data-table="applicants" data-field="x_ic_number" value="<?= $Page->ic_number->EditValue ?>" size="30" maxlength="20" placeholder="<?= HtmlEncode($Page->ic_number->getPlaceHolder()) ?>"<?= $Page->ic_number->editAttributes() ?> aria-describedby="x_ic_number_help">
<?= $Page->ic_number->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->ic_number->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->_email->Visible) { // email ?>
    <div id="r__email"<?= $Page->_email->rowAttributes() ?>>
        <label id="elh_applicants__email" for="x__email" class="<?= $Page->LeftColumnClass ?>"><?= $Page->_email->caption() ?><?= $Page->_email->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->_email->cellAttributes() ?>>
<span id="el_applicants__email">
<input type="<?= $Page->_email->getInputTextType() ?>" name="x__email" id="x__email" data-table="applicants" data-field="x__email" value="<?= $Page->_email->EditValue ?>" size="30" maxlength="100" placeholder="<?= HtmlEncode($Page->_email->getPlaceHolder()) ?>"<?= $Page->_email->editAttributes() ?> aria-describedby="x__email_help">
<?= $Page->_email->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->_email->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->phone_number->Visible) { // phone_number ?>
    <div id="r_phone_number"<?= $Page->phone_number->rowAttributes() ?>>
        <label id="elh_applicants_phone_number" for="x_phone_number" class="<?= $Page->LeftColumnClass ?>"><?= $Page->phone_number->caption() ?><?= $Page->phone_number->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->phone_number->cellAttributes() ?>>
<span id="el_applicants_phone_number">
<input type="<?= $Page->phone_number->getInputTextType() ?>" name="x_phone_number" id="x_phone_number" data-table="applicants" data-field="x_phone_number" value="<?= $Page->phone_number->EditValue ?>" size="30" maxlength="20" placeholder="<?= HtmlEncode($Page->phone_number->getPlaceHolder()) ?>"<?= $Page->phone_number->editAttributes() ?> aria-describedby="x_phone_number_help">
<?= $Page->phone_number->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->phone_number->getErrorMessage() ?></div>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->education_level->Visible) { // education_level ?>
    <div id="r_education_level"<?= $Page->education_level->rowAttributes() ?>>
        <label id="elh_applicants_education_level" for="x_education_level" class="<?= $Page->LeftColumnClass ?>"><?= $Page->education_level->caption() ?><?= $Page->education_level->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->education_level->cellAttributes() ?>>
<span id="el_applicants_education_level">
    <select
        id="x_education_level"
        name="x_education_level"
        class="form-select ew-select<?= $Page->education_level->isInvalidClass() ?>"
        data-select2-id="fapplicantsadd_x_education_level"
        data-table="applicants"
        data-field="x_education_level"
        data-value-separator="<?= $Page->education_level->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->education_level->getPlaceHolder()) ?>"
        <?= $Page->education_level->editAttributes() ?>>
        <?= $Page->education_level->selectOptionListHtml("x_education_level") ?>
    </select>
    <?= $Page->education_level->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->education_level->getErrorMessage() ?></div>
<script>
loadjs.ready("fapplicantsadd", function() {
    var options = { name: "x_education_level", selectId: "fapplicantsadd_x_education_level" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fapplicantsadd.lists.education_level.lookupOptions.length) {
        options.data = { id: "x_education_level", form: "fapplicantsadd" };
    } else {
        options.ajax = { id: "x_education_level", form: "fapplicantsadd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.applicants.fields.education_level.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->job_position->Visible) { // job_position ?>
    <div id="r_job_position"<?= $Page->job_position->rowAttributes() ?>>
        <label id="elh_applicants_job_position" for="x_job_position" class="<?= $Page->LeftColumnClass ?>"><?= $Page->job_position->caption() ?><?= $Page->job_position->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->job_position->cellAttributes() ?>>
<span id="el_applicants_job_position">
    <select
        id="x_job_position"
        name="x_job_position"
        class="form-select ew-select<?= $Page->job_position->isInvalidClass() ?>"
        data-select2-id="fapplicantsadd_x_job_position"
        data-table="applicants"
        data-field="x_job_position"
        data-value-separator="<?= $Page->job_position->displayValueSeparatorAttribute() ?>"
        data-placeholder="<?= HtmlEncode($Page->job_position->getPlaceHolder()) ?>"
        <?= $Page->job_position->editAttributes() ?>>
        <?= $Page->job_position->selectOptionListHtml("x_job_position") ?>
    </select>
    <?= $Page->job_position->getCustomMessage() ?>
    <div class="invalid-feedback"><?= $Page->job_position->getErrorMessage() ?></div>
<script>
loadjs.ready("fapplicantsadd", function() {
    var options = { name: "x_job_position", selectId: "fapplicantsadd_x_job_position" },
        el = document.querySelector("select[data-select2-id='" + options.selectId + "']");
    options.dropdownParent = el.closest("#ew-modal-dialog, #ew-add-opt-dialog");
    if (fapplicantsadd.lists.job_position.lookupOptions.length) {
        options.data = { id: "x_job_position", form: "fapplicantsadd" };
    } else {
        options.ajax = { id: "x_job_position", form: "fapplicantsadd", limit: ew.LOOKUP_PAGE_SIZE };
    }
    options.minimumResultsForSearch = Infinity;
    options = Object.assign({}, ew.selectOptions, options, ew.vars.tables.applicants.fields.job_position.selectOptions);
    ew.createSelect(options);
});
</script>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->certificate->Visible) { // certificate ?>
    <div id="r_certificate"<?= $Page->certificate->rowAttributes() ?>>
        <label id="elh_applicants_certificate" class="<?= $Page->LeftColumnClass ?>"><?= $Page->certificate->caption() ?><?= $Page->certificate->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->certificate->cellAttributes() ?>>
<span id="el_applicants_certificate">
<div id="fd_x_certificate" class="fileinput-button ew-file-drop-zone">
    <input type="file" class="form-control ew-file-input" title="<?= $Page->certificate->title() ?>" data-table="applicants" data-field="x_certificate" name="x_certificate" id="x_certificate" lang="<?= CurrentLanguageID() ?>" multiple<?= $Page->certificate->editAttributes() ?> aria-describedby="x_certificate_help"<?= ($Page->certificate->ReadOnly || $Page->certificate->Disabled) ? " disabled" : "" ?>>
    <div class="text-muted ew-file-text"><?= $Language->phrase("ChooseFiles") ?></div>
</div>
<?= $Page->certificate->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->certificate->getErrorMessage() ?></div>
<input type="hidden" name="fn_x_certificate" id= "fn_x_certificate" value="<?= $Page->certificate->Upload->FileName ?>">
<input type="hidden" name="fa_x_certificate" id= "fa_x_certificate" value="0">
<input type="hidden" name="fs_x_certificate" id= "fs_x_certificate" value="-1">
<input type="hidden" name="fx_x_certificate" id= "fx_x_certificate" value="<?= $Page->certificate->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_certificate" id= "fm_x_certificate" value="<?= $Page->certificate->UploadMaxFileSize ?>">
<input type="hidden" name="fc_x_certificate" id= "fc_x_certificate" value="<?= $Page->certificate->UploadMaxFileCount ?>">
<table id="ft_x_certificate" class="table table-sm float-start ew-upload-table"><tbody class="files"></tbody></table>
</span>
</div></div>
    </div>
<?php } ?>
<?php if ($Page->application_date->Visible) { // application_date ?>
    <div id="r_application_date"<?= $Page->application_date->rowAttributes() ?>>
        <label id="elh_applicants_application_date" for="x_application_date" class="<?= $Page->LeftColumnClass ?>"><?= $Page->application_date->caption() ?><?= $Page->application_date->Required ? $Language->phrase("FieldRequiredIndicator") : "" ?></label>
        <div class="<?= $Page->RightColumnClass ?>"><div<?= $Page->application_date->cellAttributes() ?>>
<span id="el_applicants_application_date">
<input type="<?= $Page->application_date->getInputTextType() ?>" name="x_application_date" id="x_application_date" data-table="applicants" data-field="x_application_date" value="<?= $Page->application_date->EditValue ?>" placeholder="<?= HtmlEncode($Page->application_date->getPlaceHolder()) ?>"<?= $Page->application_date->editAttributes() ?> aria-describedby="x_application_date_help">
<?= $Page->application_date->getCustomMessage() ?>
<div class="invalid-feedback"><?= $Page->application_date->getErrorMessage() ?></div>
<?php if (!$Page->application_date->ReadOnly && !$Page->application_date->Disabled && !isset($Page->application_date->EditAttrs["readonly"]) && !isset($Page->application_date->EditAttrs["disabled"])) { ?>
<script>
loadjs.ready(["fapplicantsadd", "datetimepicker"], function () {
    let format = "<?= DateFormat(0) ?>",
        options = {
        localization: {
            locale: ew.LANGUAGE_ID,
            numberingSystem: ew.getNumberingSystem()
        },
        display: {
            format,
            components: {
                hours: !!format.match(/h/i),
                minutes: !!format.match(/m/),
                seconds: !!format.match(/s/i)
            },
            icons: {
                previous: ew.IS_RTL ? "fas fa-chevron-right" : "fas fa-chevron-left",
                next: ew.IS_RTL ? "fas fa-chevron-left" : "fas fa-chevron-right"
            }
        }
    };
    ew.createDateTimePicker("fapplicantsadd", "x_application_date", jQuery.extend(true, {"useCurrent":false}, options));
});
</script>
<?php } ?>
</span>
</div></div>
    </div>
<?php } ?>
</div><!-- /page* -->
<?php if (!$Page->IsModal) { ?>
<div class="row"><!-- buttons .row -->
    <div class="<?= $Page->OffsetColumnClass ?>"><!-- buttons offset -->
<button class="btn btn-primary ew-btn" name="btn-action" id="btn-action" type="submit"><?= $Language->phrase("AddBtn") ?></button>
<button class="btn btn-default ew-btn" name="btn-cancel" id="btn-cancel" type="button" data-href="<?= HtmlEncode(GetUrl($Page->getReturnUrl())) ?>"><?= $Language->phrase("CancelBtn") ?></button>
    </div><!-- /buttons offset -->
</div><!-- /buttons .row -->
<?php } ?>
</form>
<?php
$Page->showPageFooter();
echo GetDebugMessage();
?>
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
