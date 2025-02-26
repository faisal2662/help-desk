<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\VersionController;
use App\Http\Controllers\API\FAQController;
use App\Http\Controllers\API\ChartController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthControllerV2;
use App\Http\Controllers\API\DivisionController;
use App\Http\Controllers\API\BranchOfficeController;
use App\Http\Controllers\API\HeadOfficeController;
use App\Http\Controllers\API\RegionalOfficeController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\AuthAgent\ChartController as AgentChartController;
use App\Http\Controllers\API\AuthAgent\ComplaintController as AgentComplaintController;
use App\Http\Controllers\API\AuthCustomer\ChartController as CustomerChartController;
use App\Http\Controllers\API\AuthCustomer\ComplaintController as CustomerComplaintController;
use App\Http\Controllers\API\AuthCustomer\ComplaintControllerV2 as CustomerComplaintControllerV2;
use App\Http\Controllers\API\AuthOfficer\ChartController as OfficerChartController;
use App\Http\Controllers\API\AuthOfficer\ComplaintController as OfficerComplaintController;

Route::get('/send', 'API\FirebaseMessagingController@send');

Route::prefix('v1')->group(function () {
    Route::get('/version', [VersionController::class, 'getVersion']);

    Route::get('/faqs', [FAQController::class, 'getFAQs']);

    Route::prefix('chart')->group(function () {
        Route::get('/complaints/status', [ChartController::class, 'getStatusComplaints']);
        Route::get('/complaints/monthly', [ChartController::class, 'getMonthlyComplaints']);
    });

    Route::prefix('auth')->group(function() {
        Route::post('/sign-in', [AuthController::class, 'signIn']);
        Route::post('/sign-out', [AuthController::class, 'signOut']);
        Route::post('/me', [AuthController::class, 'me']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    });

    Route::prefix('divisions')->group(function () {
        Route::get('/', [DivisionController::class, 'getDivisions']);
        Route::get('/{id}/sections', [DivisionController::class, 'getDivisionSections']);
    });

    Route::prefix('branch-offices')->group(function () {
        Route::get('/', [BranchOfficeController::class, 'getBranchOffices']);
        Route::get('/{id}/sections', [BranchOfficeController::class, 'getBranchOfficeSections']);
    });

    Route::prefix('head-offices')->group(function () {
        Route::get('/', [HeadOfficeController::class, 'getHeadOffices']);
        Route::get('/{id}/sections', [HeadOfficeController::class, 'getHeadOfficeSections']);
    });

    Route::prefix('regional-offices')->group(function () {
        Route::get('/', [RegionalOfficeController::class, 'getRegionalOffices']);
        Route::get('/{id}/sections', [RegionalOfficeController::class, 'getRegionalOfficeSections']);
    });

    Route::middleware('auth.employee')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::post('/', [ProfileController::class, 'update']);
            Route::post('/photo', [ProfileController::class, 'changePhoto']);
            Route::post('/change-password', [ProfileController::class, 'changePassword']);
        });

        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'getNotifications']);
            Route::get('/{id}', [NotificationController::class, 'getNotification']);
            Route::post('/{id}/read', [NotificationController::class, 'markRead']);
        });

        Route::prefix('chats')->group(function () {
            Route::get('/', [ChatController::class, 'getChats']);
            Route::get('/{id}', [ChatController::class, 'getChat']);
            Route::post('/{id}', [ChatController::class, 'sendChat']);
        });

        Route::prefix('agent')->group(function () {
            Route::prefix('chart')->group(function () {
                Route::get('/complaints/status', [AgentChartController::class, 'getStatusComplaints']);
                Route::get('/complaints/monthly', [AgentChartController::class, 'getMonthlyComplaints']);
            });

            Route::prefix('complaints')->group(function () {
                Route::get('/', [AgentComplaintController::class, 'getComplaints']);
                Route::get('/{id}', [AgentComplaintController::class, 'getComplaint']);
                Route::post('/{id}/answers', [AgentComplaintController::class, 'createAnswer']);
                Route::post('/{id}/transfer', [AgentComplaintController::class, 'transferComplaint']);
            });
        });

        Route::prefix('customer')->group(function () {
            Route::prefix('chart')->group(function () {
                Route::get('/complaints/status', [CustomerChartController::class, 'getStatusComplaints']);
                Route::get('/complaints/monthly', [CustomerChartController::class, 'getMonthlyComplaints']);
            });

            Route::prefix('complaints')->group(function () {
                Route::get('/', [CustomerComplaintController::class, 'getComplaints']);
                Route::get('/{id}', [CustomerComplaintController::class, 'getComplaint']);
                Route::patch('/{id}', [CustomerComplaintController::class, 'updateComplaint']);
                Route::delete('/{id}', [CustomerComplaintController::class, 'deleteComplaint']);
                Route::post('/', [CustomerComplaintController::class, 'createComplaint']);
                Route::post('/{id}/done', [CustomerComplaintController::class, 'doneComplaint']);
                Route::post('/{id}/approve', [CustomerComplaintController::class, 'approveComplaint']);
                Route::post('/{id}/answers/{answerId}/response', [CustomerComplaintController::class, 'createResponseAnswer']);
            });
        });

        Route::prefix('officer')->group(function () {
            Route::prefix('chart')->group(function () {
                Route::get('/complaints/status', [OfficerChartController::class, 'getStatusComplaints']);
                Route::get('/complaints/monthly', [OfficerChartController::class, 'getMonthlyComplaints']);
            });

            Route::prefix('complaints')->group(function () {
                Route::get('/', [OfficerComplaintController::class, 'getComplaints']);
                Route::get('/{id}', [OfficerComplaintController::class, 'getComplaint']);
            });
        });
    });
});

Route::prefix('v2')->group(function () {
    Route::prefix('auth')->group(function() {
        Route::post('/sign-in', [AuthControllerV2::class, 'signIn']);
        Route::post('/sign-out', [AuthControllerV2::class, 'signOut']);
        Route::post('/me', [AuthControllerV2::class, 'me']);
        Route::post('/forgot-password', [AuthControllerV2::class, 'forgotPassword']);
    });

    Route::middleware('auth.employee')->group(function () {
        Route::prefix('customer')->group(function () {
            Route::prefix('complaints')->group(function () {
                Route::get('/', [CustomerComplaintControllerV2::class, 'getComplaints']);
                Route::get('/{id}', [CustomerComplaintControllerV2::class, 'getComplaint']);
                Route::patch('/{id}', [CustomerComplaintControllerV2::class, 'updateComplaint']);
                Route::delete('/{id}', [CustomerComplaintControllerV2::class, 'deleteComplaint']);
                Route::post('/', [CustomerComplaintControllerV2::class, 'createComplaint']);
                Route::post('/{id}/done', [CustomerComplaintControllerV2::class, 'doneComplaint']);
                Route::post('/{id}/approve', [CustomerComplaintControllerV2::class, 'approveComplaint']);
                Route::patch('/{id}/classification', [CustomerComplaintControllerV2::class, 'updateClassification']);
                Route::post('/{id}/answers/{answerId}/response', [CustomerComplaintControllerV2::class, 'createResponseAnswer']);
            });
        });
    });
});