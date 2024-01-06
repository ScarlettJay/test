<?php

namespace PHPMaker2022\project1;

// Menu Language
if ($Language && function_exists(PROJECT_NAMESPACE . "Config") && $Language->LanguageFolder == Config("LANGUAGE_FOLDER")) {
    $MenuRelativePath = "";
    $MenuLanguage = &$Language;
} else { // Compat reports
    $LANGUAGE_FOLDER = "../lang/";
    $MenuRelativePath = "../";
    $MenuLanguage = Container("language");
}

// Navbar menu
$topMenu = new Menu("navbar", true, true);
echo $topMenu->toScript();

// Sidebar menu
$sideMenu = new Menu("menu", true, false);
$sideMenu->addMenuItem(1, "mi_applicants", $MenuLanguage->MenuPhrase("1", "MenuText"), $MenuRelativePath . "ApplicantsList", -1, "", IsLoggedIn() || AllowListMenu('{474D802A-A893-4F63-8D91-A53EF3A4EE28}applicants'), false, false, "", "", false);
$sideMenu->addMenuItem(2, "mi_registration", $MenuLanguage->MenuPhrase("2", "MenuText"), $MenuRelativePath . "RegistrationList", -1, "", IsLoggedIn() || AllowListMenu('{474D802A-A893-4F63-8D91-A53EF3A4EE28}registration'), false, false, "", "", false);
echo $sideMenu->toScript();
