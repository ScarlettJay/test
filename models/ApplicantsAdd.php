<?php

namespace PHPMaker2022\project1;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Page class
 */
class ApplicantsAdd extends Applicants
{
    use MessagesTrait;

    // Page ID
    public $PageID = "add";

    // Project ID
    public $ProjectID = PROJECT_ID;

    // Table name
    public $TableName = 'applicants';

    // Page object name
    public $PageObjName = "ApplicantsAdd";

    // View file path
    public $View = null;

    // Title
    public $Title = null; // Title for <title> tag

    // Rendering View
    public $RenderingView = false;

    // Page headings
    public $Heading = "";
    public $Subheading = "";
    public $PageHeader;
    public $PageFooter;

    // Page layout
    public $UseLayout = true;

    // Page terminated
    private $terminated = false;

    // Page heading
    public function pageHeading()
    {
        global $Language;
        if ($this->Heading != "") {
            return $this->Heading;
        }
        if (method_exists($this, "tableCaption")) {
            return $this->tableCaption();
        }
        return "";
    }

    // Page subheading
    public function pageSubheading()
    {
        global $Language;
        if ($this->Subheading != "") {
            return $this->Subheading;
        }
        if ($this->TableName) {
            return $Language->phrase($this->PageID);
        }
        return "";
    }

    // Page name
    public function pageName()
    {
        return CurrentPageName();
    }

    // Page URL
    public function pageUrl($withArgs = true)
    {
        $route = GetRoute();
        $args = $route->getArguments();
        if (!$withArgs) {
            foreach ($args as $key => &$val) {
                $val = "";
            }
            unset($val);
        }
        $url = rtrim(UrlFor($route->getName(), $args), "/") . "?";
        if ($this->UseTokenInUrl) {
            $url .= "t=" . $this->TableVar . "&"; // Add page token
        }
        return $url;
    }

    // Show Page Header
    public function showPageHeader()
    {
        $header = $this->PageHeader;
        $this->pageDataRendering($header);
        if ($header != "") { // Header exists, display
            echo '<p id="ew-page-header">' . $header . '</p>';
        }
    }

    // Show Page Footer
    public function showPageFooter()
    {
        $footer = $this->PageFooter;
        $this->pageDataRendered($footer);
        if ($footer != "") { // Footer exists, display
            echo '<p id="ew-page-footer">' . $footer . '</p>';
        }
    }

    // Validate page request
    protected function isPageRequest()
    {
        global $CurrentForm;
        if ($this->UseTokenInUrl) {
            if ($CurrentForm) {
                return $this->TableVar == $CurrentForm->getValue("t");
            }
            if (Get("t") !== null) {
                return $this->TableVar == Get("t");
            }
        }
        return true;
    }

    // Constructor
    public function __construct()
    {
        global $Language, $DashboardReport, $DebugTimer;
        global $UserTable;

        // Initialize
        $GLOBALS["Page"] = &$this;

        // Language object
        $Language = Container("language");

        // Parent constuctor
        parent::__construct();

        // Table object (applicants)
        if (!isset($GLOBALS["applicants"]) || get_class($GLOBALS["applicants"]) == PROJECT_NAMESPACE . "applicants") {
            $GLOBALS["applicants"] = &$this;
        }

        // Table name (for backward compatibility only)
        if (!defined(PROJECT_NAMESPACE . "TABLE_NAME")) {
            define(PROJECT_NAMESPACE . "TABLE_NAME", 'applicants');
        }

        // Start timer
        $DebugTimer = Container("timer");

        // Debug message
        LoadDebugMessage();

        // Open connection
        $GLOBALS["Conn"] = $GLOBALS["Conn"] ?? $this->getConnection();

        // User table object
        $UserTable = Container("usertable");
    }

    // Get content from stream
    public function getContents($stream = null): string
    {
        global $Response;
        return is_object($Response) ? $Response->getBody() : ob_get_clean();
    }

    // Is lookup
    public function isLookup()
    {
        return SameText(Route(0), Config("API_LOOKUP_ACTION"));
    }

    // Is AutoFill
    public function isAutoFill()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autofill");
    }

    // Is AutoSuggest
    public function isAutoSuggest()
    {
        return $this->isLookup() && SameText(Post("ajax"), "autosuggest");
    }

    // Is modal lookup
    public function isModalLookup()
    {
        return $this->isLookup() && SameText(Post("ajax"), "modal");
    }

    // Is terminated
    public function isTerminated()
    {
        return $this->terminated;
    }

    /**
     * Terminate page
     *
     * @param string $url URL for direction
     * @return void
     */
    public function terminate($url = "")
    {
        if ($this->terminated) {
            return;
        }
        global $ExportFileName, $TempImages, $DashboardReport, $Response;

        // Page is terminated
        $this->terminated = true;

         // Page Unload event
        if (method_exists($this, "pageUnload")) {
            $this->pageUnload();
        }

        // Global Page Unloaded event (in userfn*.php)
        Page_Unloaded();

        // Export
        if ($this->CustomExport && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, Config("EXPORT_CLASSES"))) {
            $content = $this->getContents();
            if ($ExportFileName == "") {
                $ExportFileName = $this->TableVar;
            }
            $class = PROJECT_NAMESPACE . Config("EXPORT_CLASSES." . $this->CustomExport);
            if (class_exists($class)) {
                $tbl = Container("applicants");
                $doc = new $class($tbl);
                $doc->Text = @$content;
                if ($this->isExport("email")) {
                    echo $this->exportEmail($doc->Text);
                } else {
                    $doc->export();
                }
                DeleteTempImages(); // Delete temp images
                return;
            }
        }
        if (!IsApi() && method_exists($this, "pageRedirecting")) {
            $this->pageRedirecting($url);
        }

        // Close connection
        CloseConnections();

        // Return for API
        if (IsApi()) {
            $res = $url === true;
            if (!$res) { // Show error
                WriteJson(array_merge(["success" => false], $this->getMessages()));
            }
            return;
        } else { // Check if response is JSON
            if (StartsString("application/json", $Response->getHeaderLine("Content-type")) && $Response->getBody()->getSize()) { // With JSON response
                $this->clearMessages();
                return;
            }
        }

        // Go to URL if specified
        if ($url != "") {
            if (!Config("DEBUG") && ob_get_length()) {
                ob_end_clean();
            }

            // Handle modal response
            if ($this->IsModal) { // Show as modal
                $row = ["url" => GetUrl($url), "modal" => "1"];
                $pageName = GetPageName($url);
                if ($pageName != $this->getListUrl()) { // Not List page
                    $row["caption"] = $this->getModalCaption($pageName);
                    if ($pageName == "ApplicantsView") {
                        $row["view"] = "1";
                    }
                } else { // List page should not be shown as modal => error
                    $row["error"] = $this->getFailureMessage();
                    $this->clearFailureMessage();
                }
                WriteJson($row);
            } else {
                SaveDebugMessage();
                Redirect(GetUrl($url));
            }
        }
        return; // Return to controller
    }

    // Get records from recordset
    protected function getRecordsFromRecordset($rs, $current = false)
    {
        $rows = [];
        if (is_object($rs)) { // Recordset
            while ($rs && !$rs->EOF) {
                $this->loadRowValues($rs); // Set up DbValue/CurrentValue
                $row = $this->getRecordFromArray($rs->fields);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
                $rs->moveNext();
            }
        } elseif (is_array($rs)) {
            foreach ($rs as $ar) {
                $row = $this->getRecordFromArray($ar);
                if ($current) {
                    return $row;
                } else {
                    $rows[] = $row;
                }
            }
        }
        return $rows;
    }

    // Get record from array
    protected function getRecordFromArray($ar)
    {
        $row = [];
        if (is_array($ar)) {
            foreach ($ar as $fldname => $val) {
                if (array_key_exists($fldname, $this->Fields) && ($this->Fields[$fldname]->Visible || $this->Fields[$fldname]->IsPrimaryKey)) { // Primary key or Visible
                    $fld = &$this->Fields[$fldname];
                    if ($fld->HtmlTag == "FILE") { // Upload field
                        if (EmptyValue($val)) {
                            $row[$fldname] = null;
                        } else {
                            if ($fld->DataType == DATATYPE_BLOB) {
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . $fld->Param . "/" . rawurlencode($this->getRecordKeyValue($ar))));
                                $row[$fldname] = ["type" => ContentType($val), "url" => $url, "name" => $fld->Param . ContentExtension($val)];
                            } elseif (!$fld->UploadMultiple || !ContainsString($val, Config("MULTIPLE_UPLOAD_SEPARATOR"))) { // Single file
                                $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                    "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $val)));
                                $row[$fldname] = ["type" => MimeContentType($val), "url" => $url, "name" => $val];
                            } else { // Multiple files
                                $files = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $val);
                                $ar = [];
                                foreach ($files as $file) {
                                    $url = FullUrl(GetApiUrl(Config("API_FILE_ACTION") .
                                        "/" . $fld->TableVar . "/" . Encrypt($fld->physicalUploadPath() . $file)));
                                    if (!EmptyValue($file)) {
                                        $ar[] = ["type" => MimeContentType($file), "url" => $url, "name" => $file];
                                    }
                                }
                                $row[$fldname] = $ar;
                            }
                        }
                    } else {
                        $row[$fldname] = $val;
                    }
                }
            }
        }
        return $row;
    }

    // Get record key value from array
    protected function getRecordKeyValue($ar)
    {
        $key = "";
        if (is_array($ar)) {
            $key .= @$ar['applicant_id'];
        }
        return $key;
    }

    /**
     * Hide fields for add/edit
     *
     * @return void
     */
    protected function hideFieldsForAddEdit()
    {
        if ($this->isAdd() || $this->isCopy() || $this->isGridAdd()) {
            $this->applicant_id->Visible = false;
        }
    }

    // Lookup data
    public function lookup($ar = null)
    {
        global $Language, $Security;

        // Get lookup object
        $fieldName = $ar["field"] ?? Post("field");
        $lookup = $this->Fields[$fieldName]->Lookup;

        // Get lookup parameters
        $lookupType = $ar["ajax"] ?? Post("ajax", "unknown");
        $pageSize = -1;
        $offset = -1;
        $searchValue = "";
        if (SameText($lookupType, "modal") || SameText($lookupType, "filter")) {
            $searchValue = $ar["q"] ?? Param("q") ?? $ar["sv"] ?? Post("sv", "");
            $pageSize = $ar["n"] ?? Param("n") ?? $ar["recperpage"] ?? Post("recperpage", 10);
        } elseif (SameText($lookupType, "autosuggest")) {
            $searchValue = $ar["q"] ?? Param("q", "");
            $pageSize = $ar["n"] ?? Param("n", -1);
            $pageSize = is_numeric($pageSize) ? (int)$pageSize : -1;
            if ($pageSize <= 0) {
                $pageSize = Config("AUTO_SUGGEST_MAX_ENTRIES");
            }
        }
        $start = $ar["start"] ?? Param("start", -1);
        $start = is_numeric($start) ? (int)$start : -1;
        $page = $ar["page"] ?? Param("page", -1);
        $page = is_numeric($page) ? (int)$page : -1;
        $offset = $start >= 0 ? $start : ($page > 0 && $pageSize > 0 ? ($page - 1) * $pageSize : 0);
        $userSelect = Decrypt($ar["s"] ?? Post("s", ""));
        $userFilter = Decrypt($ar["f"] ?? Post("f", ""));
        $userOrderBy = Decrypt($ar["o"] ?? Post("o", ""));
        $keys = $ar["keys"] ?? Post("keys");
        $lookup->LookupType = $lookupType; // Lookup type
        $lookup->FilterValues = []; // Clear filter values first
        if ($keys !== null) { // Selected records from modal
            if (is_array($keys)) {
                $keys = implode(Config("MULTIPLE_OPTION_SEPARATOR"), $keys);
            }
            $lookup->FilterFields = []; // Skip parent fields if any
            $lookup->FilterValues[] = $keys; // Lookup values
            $pageSize = -1; // Show all records
        } else { // Lookup values
            $lookup->FilterValues[] = $ar["v0"] ?? $ar["lookupValue"] ?? Post("v0", Post("lookupValue", ""));
        }
        $cnt = is_array($lookup->FilterFields) ? count($lookup->FilterFields) : 0;
        for ($i = 1; $i <= $cnt; $i++) {
            $lookup->FilterValues[] = $ar["v" . $i] ?? Post("v" . $i, "");
        }
        $lookup->SearchValue = $searchValue;
        $lookup->PageSize = $pageSize;
        $lookup->Offset = $offset;
        if ($userSelect != "") {
            $lookup->UserSelect = $userSelect;
        }
        if ($userFilter != "") {
            $lookup->UserFilter = $userFilter;
        }
        if ($userOrderBy != "") {
            $lookup->UserOrderBy = $userOrderBy;
        }
        return $lookup->toJson($this, !is_array($ar)); // Use settings from current page
    }
    public $FormClassName = "ew-form ew-add-form";
    public $IsModal = false;
    public $IsMobileOrModal = false;
    public $DbMasterFilter = "";
    public $DbDetailFilter = "";
    public $StartRecord;
    public $Priv = 0;
    public $OldRecordset;
    public $CopyRecord;

    /**
     * Page run
     *
     * @return void
     */
    public function run()
    {
        global $ExportType, $CustomExportType, $ExportFileName, $UserProfile, $Language, $Security, $CurrentForm,
            $SkipHeaderFooter;

        // Is modal
        $this->IsModal = Param("modal") == "1";
        $this->UseLayout = $this->UseLayout && !$this->IsModal;

        // Use layout
        $this->UseLayout = $this->UseLayout && ConvertToBool(Param("layout", true));

        // Create form object
        $CurrentForm = new HttpForm();
        $this->CurrentAction = Param("action"); // Set up current action
        $this->applicant_id->Visible = false;
        $this->registration_id->setVisibility();
        $this->full_name->setVisibility();
        $this->photo->setVisibility();
        $this->ic_number->setVisibility();
        $this->_email->setVisibility();
        $this->phone_number->setVisibility();
        $this->education_level->setVisibility();
        $this->job_position->setVisibility();
        $this->certificate->setVisibility();
        $this->application_date->setVisibility();
        $this->hideFieldsForAddEdit();

        // Set lookup cache
        if (!in_array($this->PageID, Config("LOOKUP_CACHE_PAGE_IDS"))) {
            $this->setUseLookupCache(false);
        }

        // Global Page Loading event (in userfn*.php)
        Page_Loading();

        // Page Load event
        if (method_exists($this, "pageLoad")) {
            $this->pageLoad();
        }

        // Set up lookup cache
        $this->setupLookupOptions($this->education_level);
        $this->setupLookupOptions($this->job_position);

        // Check modal
        if ($this->IsModal) {
            $SkipHeaderFooter = true;
        }
        $this->IsMobileOrModal = IsMobile() || $this->IsModal;
        $this->FormClassName = "ew-form ew-add-form";
        $postBack = false;

        // Set up current action
        if (IsApi()) {
            $this->CurrentAction = "insert"; // Add record directly
            $postBack = true;
        } elseif (Post("action") !== null) {
            $this->CurrentAction = Post("action"); // Get form action
            $this->setKey(Post($this->OldKeyName));
            $postBack = true;
        } else {
            // Load key values from QueryString
            if (($keyValue = Get("applicant_id") ?? Route("applicant_id")) !== null) {
                $this->applicant_id->setQueryStringValue($keyValue);
            }
            $this->OldKey = $this->getKey(true); // Get from CurrentValue
            $this->CopyRecord = !EmptyValue($this->OldKey);
            if ($this->CopyRecord) {
                $this->CurrentAction = "copy"; // Copy record
            } else {
                $this->CurrentAction = "show"; // Display blank record
            }
        }

        // Load old record / default values
        $loaded = $this->loadOldRecord();

        // Load form values
        if ($postBack) {
            $this->loadFormValues(); // Load form values
        }

        // Validate form if post back
        if ($postBack) {
            if (!$this->validateForm()) {
                $this->EventCancelled = true; // Event cancelled
                $this->restoreFormValues(); // Restore form values
                if (IsApi()) {
                    $this->terminate();
                    return;
                } else {
                    $this->CurrentAction = "show"; // Form error, reset action
                }
            }
        }

        // Perform current action
        switch ($this->CurrentAction) {
            case "copy": // Copy an existing record
                if (!$loaded) { // Record not loaded
                    if ($this->getFailureMessage() == "") {
                        $this->setFailureMessage($Language->phrase("NoRecord")); // No record found
                    }
                    $this->terminate("ApplicantsList"); // No matching record, return to list
                    return;
                }
                break;
            case "insert": // Add new record
                $this->SendEmail = true; // Send email on add success
                if ($this->addRow($this->OldRecordset)) { // Add successful
                    if ($this->getSuccessMessage() == "" && Post("addopt") != "1") { // Skip success message for addopt (done in JavaScript)
                        $this->setSuccessMessage($Language->phrase("AddSuccess")); // Set up success message
                    }
                    $returnUrl = $this->getReturnUrl();
                    if (GetPageName($returnUrl) == "ApplicantsList") {
                        $returnUrl = $this->addMasterUrl($returnUrl); // List page, return to List page with correct master key if necessary
                    } elseif (GetPageName($returnUrl) == "ApplicantsView") {
                        $returnUrl = $this->getViewUrl(); // View page, return to View page with keyurl directly
                    }
                    if (IsApi()) { // Return to caller
                        $this->terminate(true);
                        return;
                    } else {
                        $this->terminate($returnUrl);
                        return;
                    }
                } elseif (IsApi()) { // API request, return
                    $this->terminate();
                    return;
                } else {
                    $this->EventCancelled = true; // Event cancelled
                    $this->restoreFormValues(); // Add failed, restore form values
                }
        }

        // Set up Breadcrumb
        $this->setupBreadcrumb();

        // Render row based on row type
        $this->RowType = ROWTYPE_ADD; // Render add type

        // Render row
        $this->resetAttributes();
        $this->renderRow();

        // Set LoginStatus / Page_Rendering / Page_Render
        if (!IsApi() && !$this->isTerminated()) {
            // Setup login status
            SetupLoginStatus();

            // Pass login status to client side
            SetClientVar("login", LoginStatus());

            // Global Page Rendering event (in userfn*.php)
            Page_Rendering();

            // Page Render event
            if (method_exists($this, "pageRender")) {
                $this->pageRender();
            }

            // Render search option
            if (method_exists($this, "renderSearchOptions")) {
                $this->renderSearchOptions();
            }
        }
    }

    // Get upload files
    protected function getUploadFiles()
    {
        global $CurrentForm, $Language;
        $this->photo->Upload->Index = $CurrentForm->Index;
        $this->photo->Upload->uploadFile();
        $this->photo->CurrentValue = $this->photo->Upload->FileName;
        $this->certificate->Upload->Index = $CurrentForm->Index;
        $this->certificate->Upload->uploadFile();
        $this->certificate->CurrentValue = $this->certificate->Upload->FileName;
    }

    // Load default values
    protected function loadDefaultValues()
    {
        $this->applicant_id->CurrentValue = null;
        $this->applicant_id->OldValue = $this->applicant_id->CurrentValue;
        $this->registration_id->CurrentValue = null;
        $this->registration_id->OldValue = $this->registration_id->CurrentValue;
        $this->full_name->CurrentValue = null;
        $this->full_name->OldValue = $this->full_name->CurrentValue;
        $this->photo->Upload->DbValue = null;
        $this->photo->OldValue = $this->photo->Upload->DbValue;
        $this->photo->CurrentValue = null; // Clear file related field
        $this->ic_number->CurrentValue = null;
        $this->ic_number->OldValue = $this->ic_number->CurrentValue;
        $this->_email->CurrentValue = null;
        $this->_email->OldValue = $this->_email->CurrentValue;
        $this->phone_number->CurrentValue = null;
        $this->phone_number->OldValue = $this->phone_number->CurrentValue;
        $this->education_level->CurrentValue = null;
        $this->education_level->OldValue = $this->education_level->CurrentValue;
        $this->job_position->CurrentValue = null;
        $this->job_position->OldValue = $this->job_position->CurrentValue;
        $this->certificate->Upload->DbValue = null;
        $this->certificate->OldValue = $this->certificate->Upload->DbValue;
        $this->certificate->CurrentValue = null; // Clear file related field
        $this->application_date->CurrentValue = null;
        $this->application_date->OldValue = $this->application_date->CurrentValue;
    }

    // Load form values
    protected function loadFormValues()
    {
        // Load from form
        global $CurrentForm;
        $validate = !Config("SERVER_VALIDATE");

        // Check field name 'registration_id' first before field var 'x_registration_id'
        $val = $CurrentForm->hasValue("registration_id") ? $CurrentForm->getValue("registration_id") : $CurrentForm->getValue("x_registration_id");
        if (!$this->registration_id->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->registration_id->Visible = false; // Disable update for API request
            } else {
                $this->registration_id->setFormValue($val, true, $validate);
            }
        }

        // Check field name 'full_name' first before field var 'x_full_name'
        $val = $CurrentForm->hasValue("full_name") ? $CurrentForm->getValue("full_name") : $CurrentForm->getValue("x_full_name");
        if (!$this->full_name->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->full_name->Visible = false; // Disable update for API request
            } else {
                $this->full_name->setFormValue($val);
            }
        }

        // Check field name 'ic_number' first before field var 'x_ic_number'
        $val = $CurrentForm->hasValue("ic_number") ? $CurrentForm->getValue("ic_number") : $CurrentForm->getValue("x_ic_number");
        if (!$this->ic_number->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->ic_number->Visible = false; // Disable update for API request
            } else {
                $this->ic_number->setFormValue($val);
            }
        }

        // Check field name 'email' first before field var 'x__email'
        $val = $CurrentForm->hasValue("email") ? $CurrentForm->getValue("email") : $CurrentForm->getValue("x__email");
        if (!$this->_email->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->_email->Visible = false; // Disable update for API request
            } else {
                $this->_email->setFormValue($val);
            }
        }

        // Check field name 'phone_number' first before field var 'x_phone_number'
        $val = $CurrentForm->hasValue("phone_number") ? $CurrentForm->getValue("phone_number") : $CurrentForm->getValue("x_phone_number");
        if (!$this->phone_number->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->phone_number->Visible = false; // Disable update for API request
            } else {
                $this->phone_number->setFormValue($val);
            }
        }

        // Check field name 'education_level' first before field var 'x_education_level'
        $val = $CurrentForm->hasValue("education_level") ? $CurrentForm->getValue("education_level") : $CurrentForm->getValue("x_education_level");
        if (!$this->education_level->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->education_level->Visible = false; // Disable update for API request
            } else {
                $this->education_level->setFormValue($val);
            }
        }

        // Check field name 'job_position' first before field var 'x_job_position'
        $val = $CurrentForm->hasValue("job_position") ? $CurrentForm->getValue("job_position") : $CurrentForm->getValue("x_job_position");
        if (!$this->job_position->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->job_position->Visible = false; // Disable update for API request
            } else {
                $this->job_position->setFormValue($val);
            }
        }

        // Check field name 'application_date' first before field var 'x_application_date'
        $val = $CurrentForm->hasValue("application_date") ? $CurrentForm->getValue("application_date") : $CurrentForm->getValue("x_application_date");
        if (!$this->application_date->IsDetailKey) {
            if (IsApi() && $val === null) {
                $this->application_date->Visible = false; // Disable update for API request
            } else {
                $this->application_date->setFormValue($val, true, $validate);
            }
            $this->application_date->CurrentValue = UnFormatDateTime($this->application_date->CurrentValue, $this->application_date->formatPattern());
        }

        // Check field name 'applicant_id' first before field var 'x_applicant_id'
        $val = $CurrentForm->hasValue("applicant_id") ? $CurrentForm->getValue("applicant_id") : $CurrentForm->getValue("x_applicant_id");
        $this->getUploadFiles(); // Get upload files
    }

    // Restore form values
    public function restoreFormValues()
    {
        global $CurrentForm;
        $this->registration_id->CurrentValue = $this->registration_id->FormValue;
        $this->full_name->CurrentValue = $this->full_name->FormValue;
        $this->ic_number->CurrentValue = $this->ic_number->FormValue;
        $this->_email->CurrentValue = $this->_email->FormValue;
        $this->phone_number->CurrentValue = $this->phone_number->FormValue;
        $this->education_level->CurrentValue = $this->education_level->FormValue;
        $this->job_position->CurrentValue = $this->job_position->FormValue;
        $this->application_date->CurrentValue = $this->application_date->FormValue;
        $this->application_date->CurrentValue = UnFormatDateTime($this->application_date->CurrentValue, $this->application_date->formatPattern());
    }

    /**
     * Load row based on key values
     *
     * @return void
     */
    public function loadRow()
    {
        global $Security, $Language;
        $filter = $this->getRecordFilter();

        // Call Row Selecting event
        $this->rowSelecting($filter);

        // Load SQL based on filter
        $this->CurrentFilter = $filter;
        $sql = $this->getCurrentSql();
        $conn = $this->getConnection();
        $res = false;
        $row = $conn->fetchAssociative($sql);
        if ($row) {
            $res = true;
            $this->loadRowValues($row); // Load row values
        }
        return $res;
    }

    /**
     * Load row values from recordset or record
     *
     * @param Recordset|array $rs Record
     * @return void
     */
    public function loadRowValues($rs = null)
    {
        if (is_array($rs)) {
            $row = $rs;
        } elseif ($rs && property_exists($rs, "fields")) { // Recordset
            $row = $rs->fields;
        } else {
            $row = $this->newRow();
        }
        if (!$row) {
            return;
        }

        // Call Row Selected event
        $this->rowSelected($row);
        $this->applicant_id->setDbValue($row['applicant_id']);
        $this->registration_id->setDbValue($row['registration_id']);
        $this->full_name->setDbValue($row['full_name']);
        $this->photo->Upload->DbValue = $row['photo'];
        $this->photo->setDbValue($this->photo->Upload->DbValue);
        $this->ic_number->setDbValue($row['ic_number']);
        $this->_email->setDbValue($row['email']);
        $this->phone_number->setDbValue($row['phone_number']);
        $this->education_level->setDbValue($row['education_level']);
        $this->job_position->setDbValue($row['job_position']);
        $this->certificate->Upload->DbValue = $row['certificate'];
        $this->certificate->setDbValue($this->certificate->Upload->DbValue);
        $this->application_date->setDbValue($row['application_date']);
    }

    // Return a row with default values
    protected function newRow()
    {
        $this->loadDefaultValues();
        $row = [];
        $row['applicant_id'] = $this->applicant_id->CurrentValue;
        $row['registration_id'] = $this->registration_id->CurrentValue;
        $row['full_name'] = $this->full_name->CurrentValue;
        $row['photo'] = $this->photo->Upload->DbValue;
        $row['ic_number'] = $this->ic_number->CurrentValue;
        $row['email'] = $this->_email->CurrentValue;
        $row['phone_number'] = $this->phone_number->CurrentValue;
        $row['education_level'] = $this->education_level->CurrentValue;
        $row['job_position'] = $this->job_position->CurrentValue;
        $row['certificate'] = $this->certificate->Upload->DbValue;
        $row['application_date'] = $this->application_date->CurrentValue;
        return $row;
    }

    // Load old record
    protected function loadOldRecord()
    {
        // Load old record
        $this->OldRecordset = null;
        $validKey = $this->OldKey != "";
        if ($validKey) {
            $this->CurrentFilter = $this->getRecordFilter();
            $sql = $this->getCurrentSql();
            $conn = $this->getConnection();
            $this->OldRecordset = LoadRecordset($sql, $conn);
        }
        $this->loadRowValues($this->OldRecordset); // Load row values
        return $validKey;
    }

    // Render row values based on field settings
    public function renderRow()
    {
        global $Security, $Language, $CurrentLanguage;

        // Initialize URLs

        // Call Row_Rendering event
        $this->rowRendering();

        // Common render codes for all row types

        // applicant_id
        $this->applicant_id->RowCssClass = "row";

        // registration_id
        $this->registration_id->RowCssClass = "row";

        // full_name
        $this->full_name->RowCssClass = "row";

        // photo
        $this->photo->RowCssClass = "row";

        // ic_number
        $this->ic_number->RowCssClass = "row";

        // email
        $this->_email->RowCssClass = "row";

        // phone_number
        $this->phone_number->RowCssClass = "row";

        // education_level
        $this->education_level->RowCssClass = "row";

        // job_position
        $this->job_position->RowCssClass = "row";

        // certificate
        $this->certificate->RowCssClass = "row";

        // application_date
        $this->application_date->RowCssClass = "row";

        // View row
        if ($this->RowType == ROWTYPE_VIEW) {
            // applicant_id
            $this->applicant_id->ViewValue = $this->applicant_id->CurrentValue;
            $this->applicant_id->ViewCustomAttributes = "";

            // registration_id
            $this->registration_id->ViewValue = $this->registration_id->CurrentValue;
            $this->registration_id->ViewValue = FormatNumber($this->registration_id->ViewValue, $this->registration_id->formatPattern());
            $this->registration_id->ViewCustomAttributes = "";

            // full_name
            $this->full_name->ViewValue = $this->full_name->CurrentValue;
            $this->full_name->ViewCustomAttributes = "";

            // photo
            if (!EmptyValue($this->photo->Upload->DbValue)) {
                $this->photo->ImageWidth = 300;
                $this->photo->ImageHeight = 300;
                $this->photo->ImageAlt = $this->photo->alt();
                $this->photo->ImageCssClass = "ew-image";
                $this->photo->ViewValue = $this->photo->Upload->DbValue;
            } else {
                $this->photo->ViewValue = "";
            }
            $this->photo->ViewCustomAttributes = "";

            // ic_number
            $this->ic_number->ViewValue = $this->ic_number->CurrentValue;
            $this->ic_number->ViewCustomAttributes = "";

            // email
            $this->_email->ViewValue = $this->_email->CurrentValue;
            $this->_email->ViewCustomAttributes = "";

            // phone_number
            $this->phone_number->ViewValue = $this->phone_number->CurrentValue;
            $this->phone_number->ViewCustomAttributes = "";

            // education_level
            if (strval($this->education_level->CurrentValue) != "") {
                $this->education_level->ViewValue = $this->education_level->optionCaption($this->education_level->CurrentValue);
            } else {
                $this->education_level->ViewValue = null;
            }
            $this->education_level->ViewCustomAttributes = "";

            // job_position
            if (strval($this->job_position->CurrentValue) != "") {
                $this->job_position->ViewValue = $this->job_position->optionCaption($this->job_position->CurrentValue);
            } else {
                $this->job_position->ViewValue = null;
            }
            $this->job_position->ViewCustomAttributes = "";

            // certificate
            if (!EmptyValue($this->certificate->Upload->DbValue)) {
                $this->certificate->ImageWidth = 150;
                $this->certificate->ImageHeight = 150;
                $this->certificate->ImageAlt = $this->certificate->alt();
                $this->certificate->ImageCssClass = "ew-image";
                $this->certificate->ViewValue = $this->certificate->Upload->DbValue;
            } else {
                $this->certificate->ViewValue = "";
            }
            $this->certificate->ViewCustomAttributes = "";

            // application_date
            $this->application_date->ViewValue = $this->application_date->CurrentValue;
            $this->application_date->ViewValue = FormatDateTime($this->application_date->ViewValue, $this->application_date->formatPattern());
            $this->application_date->ViewCustomAttributes = "";

            // registration_id
            $this->registration_id->LinkCustomAttributes = "";
            $this->registration_id->HrefValue = "";

            // full_name
            $this->full_name->LinkCustomAttributes = "";
            $this->full_name->HrefValue = "";

            // photo
            $this->photo->LinkCustomAttributes = "";
            if (!EmptyValue($this->photo->Upload->DbValue)) {
                $this->photo->HrefValue = GetFileUploadUrl($this->photo, $this->photo->htmlDecode($this->photo->Upload->DbValue)); // Add prefix/suffix
                $this->photo->LinkAttrs["target"] = "_blank"; // Add target
                if ($this->isExport()) {
                    $this->photo->HrefValue = FullUrl($this->photo->HrefValue, "href");
                }
            } else {
                $this->photo->HrefValue = "";
            }
            $this->photo->ExportHrefValue = $this->photo->UploadPath . $this->photo->Upload->DbValue;

            // ic_number
            $this->ic_number->LinkCustomAttributes = "";
            $this->ic_number->HrefValue = "";

            // email
            $this->_email->LinkCustomAttributes = "";
            $this->_email->HrefValue = "";

            // phone_number
            $this->phone_number->LinkCustomAttributes = "";
            $this->phone_number->HrefValue = "";

            // education_level
            $this->education_level->LinkCustomAttributes = "";
            $this->education_level->HrefValue = "";

            // job_position
            $this->job_position->LinkCustomAttributes = "";
            $this->job_position->HrefValue = "";

            // certificate
            $this->certificate->LinkCustomAttributes = "";
            if (!EmptyValue($this->certificate->Upload->DbValue)) {
                $this->certificate->HrefValue = "%u"; // Add prefix/suffix
                $this->certificate->LinkAttrs["target"] = "_blank"; // Add target
                if ($this->isExport()) {
                    $this->certificate->HrefValue = FullUrl($this->certificate->HrefValue, "href");
                }
            } else {
                $this->certificate->HrefValue = "";
            }
            $this->certificate->ExportHrefValue = $this->certificate->UploadPath . $this->certificate->Upload->DbValue;

            // application_date
            $this->application_date->LinkCustomAttributes = "";
            $this->application_date->HrefValue = "";
        } elseif ($this->RowType == ROWTYPE_ADD) {
            // registration_id
            $this->registration_id->setupEditAttributes();
            $this->registration_id->EditCustomAttributes = "";
            $this->registration_id->EditValue = HtmlEncode($this->registration_id->CurrentValue);
            $this->registration_id->PlaceHolder = RemoveHtml($this->registration_id->caption());
            if (strval($this->registration_id->EditValue) != "" && is_numeric($this->registration_id->EditValue)) {
                $this->registration_id->EditValue = FormatNumber($this->registration_id->EditValue, null);
            }

            // full_name
            $this->full_name->setupEditAttributes();
            $this->full_name->EditCustomAttributes = "";
            if (!$this->full_name->Raw) {
                $this->full_name->CurrentValue = HtmlDecode($this->full_name->CurrentValue);
            }
            $this->full_name->EditValue = HtmlEncode($this->full_name->CurrentValue);
            $this->full_name->PlaceHolder = RemoveHtml($this->full_name->caption());

            // photo
            $this->photo->setupEditAttributes();
            $this->photo->EditCustomAttributes = "";
            if (!EmptyValue($this->photo->Upload->DbValue)) {
                $this->photo->ImageWidth = 300;
                $this->photo->ImageHeight = 300;
                $this->photo->ImageAlt = $this->photo->alt();
                $this->photo->ImageCssClass = "ew-image";
                $this->photo->EditValue = $this->photo->Upload->DbValue;
            } else {
                $this->photo->EditValue = "";
            }
            if (!EmptyValue($this->photo->CurrentValue)) {
                $this->photo->Upload->FileName = $this->photo->CurrentValue;
            }
            if ($this->isShow() || $this->isCopy()) {
                RenderUploadField($this->photo);
            }

            // ic_number
            $this->ic_number->setupEditAttributes();
            $this->ic_number->EditCustomAttributes = "";
            if (!$this->ic_number->Raw) {
                $this->ic_number->CurrentValue = HtmlDecode($this->ic_number->CurrentValue);
            }
            $this->ic_number->EditValue = HtmlEncode($this->ic_number->CurrentValue);
            $this->ic_number->PlaceHolder = RemoveHtml($this->ic_number->caption());

            // email
            $this->_email->setupEditAttributes();
            $this->_email->EditCustomAttributes = "";
            if (!$this->_email->Raw) {
                $this->_email->CurrentValue = HtmlDecode($this->_email->CurrentValue);
            }
            $this->_email->EditValue = HtmlEncode($this->_email->CurrentValue);
            $this->_email->PlaceHolder = RemoveHtml($this->_email->caption());

            // phone_number
            $this->phone_number->setupEditAttributes();
            $this->phone_number->EditCustomAttributes = "";
            if (!$this->phone_number->Raw) {
                $this->phone_number->CurrentValue = HtmlDecode($this->phone_number->CurrentValue);
            }
            $this->phone_number->EditValue = HtmlEncode($this->phone_number->CurrentValue);
            $this->phone_number->PlaceHolder = RemoveHtml($this->phone_number->caption());

            // education_level
            $this->education_level->setupEditAttributes();
            $this->education_level->EditCustomAttributes = "";
            $this->education_level->EditValue = $this->education_level->options(true);
            $this->education_level->PlaceHolder = RemoveHtml($this->education_level->caption());

            // job_position
            $this->job_position->setupEditAttributes();
            $this->job_position->EditCustomAttributes = "";
            $this->job_position->EditValue = $this->job_position->options(true);
            $this->job_position->PlaceHolder = RemoveHtml($this->job_position->caption());

            // certificate
            $this->certificate->setupEditAttributes();
            $this->certificate->EditCustomAttributes = "";
            if (!EmptyValue($this->certificate->Upload->DbValue)) {
                $this->certificate->ImageWidth = 150;
                $this->certificate->ImageHeight = 150;
                $this->certificate->ImageAlt = $this->certificate->alt();
                $this->certificate->ImageCssClass = "ew-image";
                $this->certificate->EditValue = $this->certificate->Upload->DbValue;
            } else {
                $this->certificate->EditValue = "";
            }
            if (!EmptyValue($this->certificate->CurrentValue)) {
                $this->certificate->Upload->FileName = $this->certificate->CurrentValue;
            }
            if ($this->isShow() || $this->isCopy()) {
                RenderUploadField($this->certificate);
            }

            // application_date
            $this->application_date->setupEditAttributes();
            $this->application_date->EditCustomAttributes = "";
            $this->application_date->EditValue = HtmlEncode(FormatDateTime($this->application_date->CurrentValue, $this->application_date->formatPattern()));
            $this->application_date->PlaceHolder = RemoveHtml($this->application_date->caption());

            // Add refer script

            // registration_id
            $this->registration_id->LinkCustomAttributes = "";
            $this->registration_id->HrefValue = "";

            // full_name
            $this->full_name->LinkCustomAttributes = "";
            $this->full_name->HrefValue = "";

            // photo
            $this->photo->LinkCustomAttributes = "";
            if (!EmptyValue($this->photo->Upload->DbValue)) {
                $this->photo->HrefValue = GetFileUploadUrl($this->photo, $this->photo->htmlDecode($this->photo->Upload->DbValue)); // Add prefix/suffix
                $this->photo->LinkAttrs["target"] = "_blank"; // Add target
                if ($this->isExport()) {
                    $this->photo->HrefValue = FullUrl($this->photo->HrefValue, "href");
                }
            } else {
                $this->photo->HrefValue = "";
            }
            $this->photo->ExportHrefValue = $this->photo->UploadPath . $this->photo->Upload->DbValue;

            // ic_number
            $this->ic_number->LinkCustomAttributes = "";
            $this->ic_number->HrefValue = "";

            // email
            $this->_email->LinkCustomAttributes = "";
            $this->_email->HrefValue = "";

            // phone_number
            $this->phone_number->LinkCustomAttributes = "";
            $this->phone_number->HrefValue = "";

            // education_level
            $this->education_level->LinkCustomAttributes = "";
            $this->education_level->HrefValue = "";

            // job_position
            $this->job_position->LinkCustomAttributes = "";
            $this->job_position->HrefValue = "";

            // certificate
            $this->certificate->LinkCustomAttributes = "";
            if (!EmptyValue($this->certificate->Upload->DbValue)) {
                $this->certificate->HrefValue = "%u"; // Add prefix/suffix
                $this->certificate->LinkAttrs["target"] = "_blank"; // Add target
                if ($this->isExport()) {
                    $this->certificate->HrefValue = FullUrl($this->certificate->HrefValue, "href");
                }
            } else {
                $this->certificate->HrefValue = "";
            }
            $this->certificate->ExportHrefValue = $this->certificate->UploadPath . $this->certificate->Upload->DbValue;

            // application_date
            $this->application_date->LinkCustomAttributes = "";
            $this->application_date->HrefValue = "";
        }
        if ($this->RowType == ROWTYPE_ADD || $this->RowType == ROWTYPE_EDIT || $this->RowType == ROWTYPE_SEARCH) { // Add/Edit/Search row
            $this->setupFieldTitles();
        }

        // Call Row Rendered event
        if ($this->RowType != ROWTYPE_AGGREGATEINIT) {
            $this->rowRendered();
        }
    }

    // Validate form
    protected function validateForm()
    {
        global $Language;

        // Check if validation required
        if (!Config("SERVER_VALIDATE")) {
            return true;
        }
        $validateForm = true;
        if ($this->registration_id->Required) {
            if (!$this->registration_id->IsDetailKey && EmptyValue($this->registration_id->FormValue)) {
                $this->registration_id->addErrorMessage(str_replace("%s", $this->registration_id->caption(), $this->registration_id->RequiredErrorMessage));
            }
        }
        if (!CheckInteger($this->registration_id->FormValue)) {
            $this->registration_id->addErrorMessage($this->registration_id->getErrorMessage(false));
        }
        if ($this->full_name->Required) {
            if (!$this->full_name->IsDetailKey && EmptyValue($this->full_name->FormValue)) {
                $this->full_name->addErrorMessage(str_replace("%s", $this->full_name->caption(), $this->full_name->RequiredErrorMessage));
            }
        }
        if ($this->photo->Required) {
            if ($this->photo->Upload->FileName == "" && !$this->photo->Upload->KeepFile) {
                $this->photo->addErrorMessage(str_replace("%s", $this->photo->caption(), $this->photo->RequiredErrorMessage));
            }
        }
        if ($this->ic_number->Required) {
            if (!$this->ic_number->IsDetailKey && EmptyValue($this->ic_number->FormValue)) {
                $this->ic_number->addErrorMessage(str_replace("%s", $this->ic_number->caption(), $this->ic_number->RequiredErrorMessage));
            }
        }
        if ($this->_email->Required) {
            if (!$this->_email->IsDetailKey && EmptyValue($this->_email->FormValue)) {
                $this->_email->addErrorMessage(str_replace("%s", $this->_email->caption(), $this->_email->RequiredErrorMessage));
            }
        }
        if ($this->phone_number->Required) {
            if (!$this->phone_number->IsDetailKey && EmptyValue($this->phone_number->FormValue)) {
                $this->phone_number->addErrorMessage(str_replace("%s", $this->phone_number->caption(), $this->phone_number->RequiredErrorMessage));
            }
        }
        if ($this->education_level->Required) {
            if (!$this->education_level->IsDetailKey && EmptyValue($this->education_level->FormValue)) {
                $this->education_level->addErrorMessage(str_replace("%s", $this->education_level->caption(), $this->education_level->RequiredErrorMessage));
            }
        }
        if ($this->job_position->Required) {
            if (!$this->job_position->IsDetailKey && EmptyValue($this->job_position->FormValue)) {
                $this->job_position->addErrorMessage(str_replace("%s", $this->job_position->caption(), $this->job_position->RequiredErrorMessage));
            }
        }
        if ($this->certificate->Required) {
            if ($this->certificate->Upload->FileName == "" && !$this->certificate->Upload->KeepFile) {
                $this->certificate->addErrorMessage(str_replace("%s", $this->certificate->caption(), $this->certificate->RequiredErrorMessage));
            }
        }
        if ($this->application_date->Required) {
            if (!$this->application_date->IsDetailKey && EmptyValue($this->application_date->FormValue)) {
                $this->application_date->addErrorMessage(str_replace("%s", $this->application_date->caption(), $this->application_date->RequiredErrorMessage));
            }
        }
        if (!CheckDate($this->application_date->FormValue, $this->application_date->formatPattern())) {
            $this->application_date->addErrorMessage($this->application_date->getErrorMessage(false));
        }

        // Return validate result
        $validateForm = $validateForm && !$this->hasInvalidFields();

        // Call Form_CustomValidate event
        $formCustomError = "";
        $validateForm = $validateForm && $this->formCustomValidate($formCustomError);
        if ($formCustomError != "") {
            $this->setFailureMessage($formCustomError);
        }
        return $validateForm;
    }

    // Add record
    protected function addRow($rsold = null)
    {
        global $Language, $Security;
        if ($this->registration_id->CurrentValue != "") { // Check field with unique index
            $filter = "(`registration_id` = " . AdjustSql($this->registration_id->CurrentValue, $this->Dbid) . ")";
            $rsChk = $this->loadRs($filter)->fetch();
            if ($rsChk !== false) {
                $idxErrMsg = str_replace("%f", $this->registration_id->caption(), $Language->phrase("DupIndex"));
                $idxErrMsg = str_replace("%v", $this->registration_id->CurrentValue, $idxErrMsg);
                $this->setFailureMessage($idxErrMsg);
                return false;
            }
        }
        $conn = $this->getConnection();

        // Load db values from rsold
        $this->loadDbValues($rsold);
        if ($rsold) {
        }
        $rsnew = [];

        // registration_id
        $this->registration_id->setDbValueDef($rsnew, $this->registration_id->CurrentValue, null, false);

        // full_name
        $this->full_name->setDbValueDef($rsnew, $this->full_name->CurrentValue, null, false);

        // photo
        if ($this->photo->Visible && !$this->photo->Upload->KeepFile) {
            $this->photo->Upload->DbValue = ""; // No need to delete old file
            if ($this->photo->Upload->FileName == "") {
                $rsnew['photo'] = null;
            } else {
                $rsnew['photo'] = $this->photo->Upload->FileName;
            }
        }

        // ic_number
        $this->ic_number->setDbValueDef($rsnew, $this->ic_number->CurrentValue, null, false);

        // email
        $this->_email->setDbValueDef($rsnew, $this->_email->CurrentValue, null, false);

        // phone_number
        $this->phone_number->setDbValueDef($rsnew, $this->phone_number->CurrentValue, null, false);

        // education_level
        $this->education_level->setDbValueDef($rsnew, $this->education_level->CurrentValue, null, false);

        // job_position
        $this->job_position->setDbValueDef($rsnew, $this->job_position->CurrentValue, null, false);

        // certificate
        if ($this->certificate->Visible && !$this->certificate->Upload->KeepFile) {
            $this->certificate->Upload->DbValue = ""; // No need to delete old file
            if ($this->certificate->Upload->FileName == "") {
                $rsnew['certificate'] = null;
            } else {
                $rsnew['certificate'] = $this->certificate->Upload->FileName;
            }
        }

        // application_date
        $this->application_date->setDbValueDef($rsnew, UnFormatDateTime($this->application_date->CurrentValue, $this->application_date->formatPattern()), null, false);
        if ($this->photo->Visible && !$this->photo->Upload->KeepFile) {
            $oldFiles = EmptyValue($this->photo->Upload->DbValue) ? [] : [$this->photo->htmlDecode($this->photo->Upload->DbValue)];
            if (!EmptyValue($this->photo->Upload->FileName)) {
                $newFiles = [$this->photo->Upload->FileName];
                $NewFileCount = count($newFiles);
                for ($i = 0; $i < $NewFileCount; $i++) {
                    if ($newFiles[$i] != "") {
                        $file = $newFiles[$i];
                        $tempPath = UploadTempPath($this->photo, $this->photo->Upload->Index);
                        if (file_exists($tempPath . $file)) {
                            if (Config("DELETE_UPLOADED_FILES")) {
                                $oldFileFound = false;
                                $oldFileCount = count($oldFiles);
                                for ($j = 0; $j < $oldFileCount; $j++) {
                                    $oldFile = $oldFiles[$j];
                                    if ($oldFile == $file) { // Old file found, no need to delete anymore
                                        array_splice($oldFiles, $j, 1);
                                        $oldFileFound = true;
                                        break;
                                    }
                                }
                                if ($oldFileFound) { // No need to check if file exists further
                                    continue;
                                }
                            }
                            $file1 = UniqueFilename($this->photo->physicalUploadPath(), $file); // Get new file name
                            if ($file1 != $file) { // Rename temp file
                                while (file_exists($tempPath . $file1) || file_exists($this->photo->physicalUploadPath() . $file1)) { // Make sure no file name clash
                                    $file1 = UniqueFilename([$this->photo->physicalUploadPath(), $tempPath], $file1, true); // Use indexed name
                                }
                                rename($tempPath . $file, $tempPath . $file1);
                                $newFiles[$i] = $file1;
                            }
                        }
                    }
                }
                $this->photo->Upload->DbValue = empty($oldFiles) ? "" : implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $oldFiles);
                $this->photo->Upload->FileName = implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $newFiles);
                $this->photo->setDbValueDef($rsnew, $this->photo->Upload->FileName, null, false);
            }
        }
        if ($this->certificate->Visible && !$this->certificate->Upload->KeepFile) {
            $oldFiles = EmptyValue($this->certificate->Upload->DbValue) ? [] : explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $this->certificate->htmlDecode(strval($this->certificate->Upload->DbValue)));
            if (!EmptyValue($this->certificate->Upload->FileName)) {
                $newFiles = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), strval($this->certificate->Upload->FileName));
                $NewFileCount = count($newFiles);
                for ($i = 0; $i < $NewFileCount; $i++) {
                    if ($newFiles[$i] != "") {
                        $file = $newFiles[$i];
                        $tempPath = UploadTempPath($this->certificate, $this->certificate->Upload->Index);
                        if (file_exists($tempPath . $file)) {
                            if (Config("DELETE_UPLOADED_FILES")) {
                                $oldFileFound = false;
                                $oldFileCount = count($oldFiles);
                                for ($j = 0; $j < $oldFileCount; $j++) {
                                    $oldFile = $oldFiles[$j];
                                    if ($oldFile == $file) { // Old file found, no need to delete anymore
                                        array_splice($oldFiles, $j, 1);
                                        $oldFileFound = true;
                                        break;
                                    }
                                }
                                if ($oldFileFound) { // No need to check if file exists further
                                    continue;
                                }
                            }
                            $file1 = UniqueFilename($this->certificate->physicalUploadPath(), $file); // Get new file name
                            if ($file1 != $file) { // Rename temp file
                                while (file_exists($tempPath . $file1) || file_exists($this->certificate->physicalUploadPath() . $file1)) { // Make sure no file name clash
                                    $file1 = UniqueFilename([$this->certificate->physicalUploadPath(), $tempPath], $file1, true); // Use indexed name
                                }
                                rename($tempPath . $file, $tempPath . $file1);
                                $newFiles[$i] = $file1;
                            }
                        }
                    }
                }
                $this->certificate->Upload->DbValue = empty($oldFiles) ? "" : implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $oldFiles);
                $this->certificate->Upload->FileName = implode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $newFiles);
                $this->certificate->setDbValueDef($rsnew, $this->certificate->Upload->FileName, null, false);
            }
        }

        // Call Row Inserting event
        $insertRow = $this->rowInserting($rsold, $rsnew);
        if ($insertRow) {
            $addRow = $this->insert($rsnew);
            if ($addRow) {
                if ($this->photo->Visible && !$this->photo->Upload->KeepFile) {
                    $oldFiles = EmptyValue($this->photo->Upload->DbValue) ? [] : [$this->photo->htmlDecode($this->photo->Upload->DbValue)];
                    if (!EmptyValue($this->photo->Upload->FileName)) {
                        $newFiles = [$this->photo->Upload->FileName];
                        $newFiles2 = [$this->photo->htmlDecode($rsnew['photo'])];
                        $newFileCount = count($newFiles);
                        for ($i = 0; $i < $newFileCount; $i++) {
                            if ($newFiles[$i] != "") {
                                $file = UploadTempPath($this->photo, $this->photo->Upload->Index) . $newFiles[$i];
                                if (file_exists($file)) {
                                    if (@$newFiles2[$i] != "") { // Use correct file name
                                        $newFiles[$i] = $newFiles2[$i];
                                    }
                                    if (!$this->photo->Upload->SaveToFile($newFiles[$i], true, $i)) { // Just replace
                                        $this->setFailureMessage($Language->phrase("UploadErrMsg7"));
                                        return false;
                                    }
                                }
                            }
                        }
                    } else {
                        $newFiles = [];
                    }
                    if (Config("DELETE_UPLOADED_FILES")) {
                        foreach ($oldFiles as $oldFile) {
                            if ($oldFile != "" && !in_array($oldFile, $newFiles)) {
                                @unlink($this->photo->oldPhysicalUploadPath() . $oldFile);
                            }
                        }
                    }
                }
                if ($this->certificate->Visible && !$this->certificate->Upload->KeepFile) {
                    $oldFiles = EmptyValue($this->certificate->Upload->DbValue) ? [] : explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $this->certificate->htmlDecode(strval($this->certificate->Upload->DbValue)));
                    if (!EmptyValue($this->certificate->Upload->FileName)) {
                        $newFiles = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $this->certificate->Upload->FileName);
                        $newFiles2 = explode(Config("MULTIPLE_UPLOAD_SEPARATOR"), $this->certificate->htmlDecode($rsnew['certificate']));
                        $newFileCount = count($newFiles);
                        for ($i = 0; $i < $newFileCount; $i++) {
                            if ($newFiles[$i] != "") {
                                $file = UploadTempPath($this->certificate, $this->certificate->Upload->Index) . $newFiles[$i];
                                if (file_exists($file)) {
                                    if (@$newFiles2[$i] != "") { // Use correct file name
                                        $newFiles[$i] = $newFiles2[$i];
                                    }
                                    if (!$this->certificate->Upload->SaveToFile($newFiles[$i], true, $i)) { // Just replace
                                        $this->setFailureMessage($Language->phrase("UploadErrMsg7"));
                                        return false;
                                    }
                                }
                            }
                        }
                    } else {
                        $newFiles = [];
                    }
                    if (Config("DELETE_UPLOADED_FILES")) {
                        foreach ($oldFiles as $oldFile) {
                            if ($oldFile != "" && !in_array($oldFile, $newFiles)) {
                                @unlink($this->certificate->oldPhysicalUploadPath() . $oldFile);
                            }
                        }
                    }
                }
            }
        } else {
            if ($this->getSuccessMessage() != "" || $this->getFailureMessage() != "") {
                // Use the message, do nothing
            } elseif ($this->CancelMessage != "") {
                $this->setFailureMessage($this->CancelMessage);
                $this->CancelMessage = "";
            } else {
                $this->setFailureMessage($Language->phrase("InsertCancelled"));
            }
            $addRow = false;
        }
        if ($addRow) {
            // Call Row Inserted event
            $this->rowInserted($rsold, $rsnew);
        }

        // Clean upload path if any
        if ($addRow) {
            // photo
            CleanUploadTempPath($this->photo, $this->photo->Upload->Index);

            // certificate
            CleanUploadTempPath($this->certificate, $this->certificate->Upload->Index);
        }

        // Write JSON for API request
        if (IsApi() && $addRow) {
            $row = $this->getRecordsFromRecordset([$rsnew], true);
            WriteJson(["success" => true, $this->TableVar => $row]);
        }
        return $addRow;
    }

    // Set up Breadcrumb
    protected function setupBreadcrumb()
    {
        global $Breadcrumb, $Language;
        $Breadcrumb = new Breadcrumb("index");
        $url = CurrentUrl();
        $Breadcrumb->add("list", $this->TableVar, $this->addMasterUrl("ApplicantsList"), "", $this->TableVar, true);
        $pageId = ($this->isCopy()) ? "Copy" : "Add";
        $Breadcrumb->add("add", $pageId, $url);
    }

    // Setup lookup options
    public function setupLookupOptions($fld)
    {
        if ($fld->Lookup !== null && $fld->Lookup->Options === null) {
            // Get default connection and filter
            $conn = $this->getConnection();
            $lookupFilter = "";

            // No need to check any more
            $fld->Lookup->Options = [];

            // Set up lookup SQL and connection
            switch ($fld->FieldVar) {
                case "x_education_level":
                    break;
                case "x_job_position":
                    break;
                default:
                    $lookupFilter = "";
                    break;
            }

            // Always call to Lookup->getSql so that user can setup Lookup->Options in Lookup_Selecting server event
            $sql = $fld->Lookup->getSql(false, "", $lookupFilter, $this);

            // Set up lookup cache
            if (!$fld->hasLookupOptions() && $fld->UseLookupCache && $sql != "" && count($fld->Lookup->Options) == 0) {
                $totalCnt = $this->getRecordCount($sql, $conn);
                if ($totalCnt > $fld->LookupCacheCount) { // Total count > cache count, do not cache
                    return;
                }
                $rows = $conn->executeQuery($sql)->fetchAll();
                $ar = [];
                foreach ($rows as $row) {
                    $row = $fld->Lookup->renderViewRow($row, Container($fld->Lookup->LinkTable));
                    $ar[strval($row["lf"])] = $row;
                }
                $fld->Lookup->Options = $ar;
            }
        }
    }

    // Page Load event
    public function pageLoad()
    {
        //Log("Page Load");
    }

    // Page Unload event
    public function pageUnload()
    {
        //Log("Page Unload");
    }

    // Page Redirecting event
    public function pageRedirecting(&$url)
    {
        // Example:
        //$url = "your URL";
    }

    // Message Showing event
    // $type = ''|'success'|'failure'|'warning'
    public function messageShowing(&$msg, $type)
    {
        if ($type == 'success') {
            //$msg = "your success message";
        } elseif ($type == 'failure') {
            //$msg = "your failure message";
        } elseif ($type == 'warning') {
            //$msg = "your warning message";
        } else {
            //$msg = "your message";
        }
    }

    // Page Render event
    public function pageRender()
    {
        //Log("Page Render");
    }

    // Page Data Rendering event
    public function pageDataRendering(&$header)
    {
        // Example:
        //$header = "your header";
    }

    // Page Data Rendered event
    public function pageDataRendered(&$footer)
    {
        // Example:
        //$footer = "your footer";
    }

    // Form Custom Validate event
    public function formCustomValidate(&$customError)
    {
        // Return error message in $customError
        return true;
    }
}
