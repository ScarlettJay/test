<?php

namespace PHPMaker2022\project1;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

// Handle Routes
return function (App $app) {
    // applicants
    $app->map(["GET","POST","OPTIONS"], '/ApplicantsList[/{applicant_id}]', ApplicantsController::class . ':list')->add(PermissionMiddleware::class)->setName('ApplicantsList-applicants-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/ApplicantsAdd[/{applicant_id}]', ApplicantsController::class . ':add')->add(PermissionMiddleware::class)->setName('ApplicantsAdd-applicants-add'); // add
    $app->map(["GET","OPTIONS"], '/ApplicantsView[/{applicant_id}]', ApplicantsController::class . ':view')->add(PermissionMiddleware::class)->setName('ApplicantsView-applicants-view'); // view
    $app->map(["GET","POST","OPTIONS"], '/ApplicantsEdit[/{applicant_id}]', ApplicantsController::class . ':edit')->add(PermissionMiddleware::class)->setName('ApplicantsEdit-applicants-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/ApplicantsDelete[/{applicant_id}]', ApplicantsController::class . ':delete')->add(PermissionMiddleware::class)->setName('ApplicantsDelete-applicants-delete'); // delete
    $app->group(
        '/applicants',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{applicant_id}]', ApplicantsController::class . ':list')->add(PermissionMiddleware::class)->setName('applicants/list-applicants-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{applicant_id}]', ApplicantsController::class . ':add')->add(PermissionMiddleware::class)->setName('applicants/add-applicants-add-2'); // add
            $group->map(["GET","OPTIONS"], '/' . Config("VIEW_ACTION") . '[/{applicant_id}]', ApplicantsController::class . ':view')->add(PermissionMiddleware::class)->setName('applicants/view-applicants-view-2'); // view
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{applicant_id}]', ApplicantsController::class . ':edit')->add(PermissionMiddleware::class)->setName('applicants/edit-applicants-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{applicant_id}]', ApplicantsController::class . ':delete')->add(PermissionMiddleware::class)->setName('applicants/delete-applicants-delete-2'); // delete
        }
    );

    // registration
    $app->map(["GET","POST","OPTIONS"], '/RegistrationList[/{registration_id}]', RegistrationController::class . ':list')->add(PermissionMiddleware::class)->setName('RegistrationList-registration-list'); // list
    $app->map(["GET","POST","OPTIONS"], '/RegistrationAdd[/{registration_id}]', RegistrationController::class . ':add')->add(PermissionMiddleware::class)->setName('RegistrationAdd-registration-add'); // add
    $app->map(["GET","POST","OPTIONS"], '/RegistrationEdit[/{registration_id}]', RegistrationController::class . ':edit')->add(PermissionMiddleware::class)->setName('RegistrationEdit-registration-edit'); // edit
    $app->map(["GET","POST","OPTIONS"], '/RegistrationDelete[/{registration_id}]', RegistrationController::class . ':delete')->add(PermissionMiddleware::class)->setName('RegistrationDelete-registration-delete'); // delete
    $app->group(
        '/registration',
        function (RouteCollectorProxy $group) {
            $group->map(["GET","POST","OPTIONS"], '/' . Config("LIST_ACTION") . '[/{registration_id}]', RegistrationController::class . ':list')->add(PermissionMiddleware::class)->setName('registration/list-registration-list-2'); // list
            $group->map(["GET","POST","OPTIONS"], '/' . Config("ADD_ACTION") . '[/{registration_id}]', RegistrationController::class . ':add')->add(PermissionMiddleware::class)->setName('registration/add-registration-add-2'); // add
            $group->map(["GET","POST","OPTIONS"], '/' . Config("EDIT_ACTION") . '[/{registration_id}]', RegistrationController::class . ':edit')->add(PermissionMiddleware::class)->setName('registration/edit-registration-edit-2'); // edit
            $group->map(["GET","POST","OPTIONS"], '/' . Config("DELETE_ACTION") . '[/{registration_id}]', RegistrationController::class . ':delete')->add(PermissionMiddleware::class)->setName('registration/delete-registration-delete-2'); // delete
        }
    );

    // error
    $app->map(["GET","POST","OPTIONS"], '/error', OthersController::class . ':error')->add(PermissionMiddleware::class)->setName('error');

    // personal_data
    $app->map(["GET","POST","OPTIONS"], '/personaldata', OthersController::class . ':personaldata')->add(PermissionMiddleware::class)->setName('personaldata');

    // login
    $app->map(["GET","POST","OPTIONS"], '/login', OthersController::class . ':login')->add(PermissionMiddleware::class)->setName('login');

    // reset_password
    $app->map(["GET","POST","OPTIONS"], '/resetpassword', OthersController::class . ':resetpassword')->add(PermissionMiddleware::class)->setName('resetpassword');

    // change_password
    $app->map(["GET","POST","OPTIONS"], '/changepassword', OthersController::class . ':changepassword')->add(PermissionMiddleware::class)->setName('changepassword');

    // register
    $app->map(["GET","POST","OPTIONS"], '/register', OthersController::class . ':register')->add(PermissionMiddleware::class)->setName('register');

    // logout
    $app->map(["GET","POST","OPTIONS"], '/logout', OthersController::class . ':logout')->add(PermissionMiddleware::class)->setName('logout');

    // captcha
    $app->map(["GET","OPTIONS"], '/captcha[/{page}]', OthersController::class . ':captcha')->add(PermissionMiddleware::class)->setName('captcha');

    // Swagger
    $app->get('/' . Config("SWAGGER_ACTION"), OthersController::class . ':swagger')->setName(Config("SWAGGER_ACTION")); // Swagger

    // Index
    $app->get('/[index]', OthersController::class . ':index')->add(PermissionMiddleware::class)->setName('index');

    // Route Action event
    if (function_exists(PROJECT_NAMESPACE . "Route_Action")) {
        Route_Action($app);
    }

    /**
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * NOTE: Make sure this route is defined last.
     */
    $app->map(
        ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
        '/{routes:.+}',
        function ($request, $response, $params) {
            $error = [
                "statusCode" => "404",
                "error" => [
                    "class" => "text-warning",
                    "type" => Container("language")->phrase("Error"),
                    "description" => str_replace("%p", $params["routes"], Container("language")->phrase("PageNotFound")),
                ],
            ];
            Container("flash")->addMessage("error", $error);
            return $response->withStatus(302)->withHeader("Location", GetUrl("error")); // Redirect to error page
        }
    );
};
