<?php

declare(strict_types=1);

// Force PHP to UTC (global)
date_default_timezone_set('UTC');

// Enable full error reporting
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\MessageController;
use App\Controllers\NotificationController;
use App\Controllers\AnnouncementController;
use App\Controllers\ProfileController;

// Load routes
//$routes = require_once __DIR__ . '/../app/routes.php';
$routes =  [
    
    // Public routes
    ['GET', '/', [PageController::class, 'index']],
    ['GET', '/admissions', [PageController::class, 'showApply']],
    ['POST', '/admissions', [AuthController::class, 'apply']],
    ['GET', '/verify-vmail', [AuthController::class, 'verifyEmail']],
    ['GET', '/resend-vmail', [AuthController::class, 'resendVMail']],
    ['GET', '/approve', [PageController::class, 'showApprove']],
    ['POST', '/approve', [AuthController::class, 'approveApplicant']],
    ['GET', '/campus', [PageController::class, 'showLogin']],
    ['POST', '/campus', [AuthController::class, 'login']],
    ['GET', '/resend-vcode', [AuthController::class, 'resendVCode']],
    ['GET', '/contact', [PageController::class, 'underConstruction']],
    ['GET', '/about', [PageController::class, 'underConstruction']],
    ['GET', '/donate', [AuthController::class, 'donate']],
    
    // Dashboard routes
    ['GET', '/dashboard', [DashboardController::class, 'dashboard']],
    ['GET', '/dashboard/{page}', [DashboardController::class, 'dashboardController']],
    ['POST', '/dashboard/courses/course-registration', [DashboardController::class, 'enrollCourse']],
    ['POST', '/dashboard/courses/heading', [DashboardController::class, 'setHeading']],
    ['POST', '/dashboard/courses/crud/{page}', [DashboardController::class, 'crudData']],
    
    // Messages routes
    ['GET', '/messages', [MessageController::class, 'index']],
    ['GET', '/messages/unread', [MessageController::class, 'unread']],
    ['GET', '/messages/compose', [MessageController::class, 'compose']],
    ['POST', '/messages/compose', [MessageController::class, 'send']],
    ['GET', '/messages/thread/{id}', [MessageController::class, 'thread']],
    ['POST', '/messages/markAsRead/{id}', [MessageController::class, 'markAsRead']],
    ['POST', '/messages/delete/{id}', [MessageController::class, 'deleteMessage']],
    ['POST', '/messages/bulkDelete', [MessageController::class, 'bulkDeleteMessages']],
    
    // Notifications routes
    ['GET', '/notifications', [NotificationController::class, 'showNotifications']],
    ['GET', '/notifications/unread', [NotificationController::class, 'unread']],
    ['GET', '/notifications/create', [NotificationController::class, 'create']],
    ['POST', '/notifications/create', [NotificationController::class, 'send']],
    ['POST', '/notifications/markAsRead/{id}', [NotificationController::class, 'markAsRead']],
    ['POST', '/notifications/delete/{id}', [NotificationController::class, 'delete']],
    ['POST', '/notifications/bulkRead', [NotificationController::class, 'bulkRead']],
    ['POST', '/notifications/bulkDelete', [NotificationController::class, 'bulkDelete']],
    
    // Announcement routes
    ['GET', '/announcements', [AnnouncementController::class, 'showAnnouncements']],
    ['GET', '/announcements/unread', [AnnouncementController::class, 'unread']],
    ['GET', '/announcements/create', [AnnouncementController::class, 'create']],
    ['POST', '/announcements/create', [AnnouncementController::class, 'send']],
    ['POST', '/announcements/markAsRead/{id}', [AnnouncementController::class, 'markAsRead']],
    ['POST', '/announcements/delete/{id}', [AnnouncementController::class, 'delete']],
    ['POST', '/announcements/bulkRead', [AnnouncementController::class, 'bulkRead']],
    ['POST', '/announcements/bulkDelete', [AnnouncementController::class, 'bulkDelete']],
    
    // User menu routes
    ['GET', '/profile', [ProfileController::class, 'index']],
    ['GET', '/profile/changePassword', [ProfileController::class, 'changePassword']],
    ['POST', '/profile/changePassword', [ProfileController::class, 'changePassword']],
    ['POST', '/profile', [ProfileController::class, 'handleAvatarUpload']],
    
    // User logout route
    ['GET', '/logout', [AuthController::class, 'logout']],
];


// Dispatch the request
Router::dispatch($routes);

// echo \App\Core\Csrf::token();
// exit;

