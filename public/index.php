<?php
session_start();

require __DIR__ . '/../app/core/model.php';
require __DIR__ . '/../app/core/controller.php';
require __DIR__ . '/../app/core/auth.php';
require __DIR__ . '/../app/core/router.php';

require __DIR__ . '/../app/controllers/authcontroller.php';
require __DIR__ . '/../app/controllers/admincontroller.php';
require __DIR__ . '/../app/controllers/adminajaxcontroller.php';
require __DIR__ . '/../app/controllers/usercontroller.php';
require __DIR__ . '/../app/controllers/userbookingcontroller.php';
require __DIR__ . '/../app/controllers/UserRescheduleController.php';
require __DIR__ . '/../app/controllers/adminbookingcontroller.php';
require __DIR__ . '/../app/controllers/infocontroller.php';
require __DIR__ . '/../app/Controllers/UserFeedbackController.php';

require __DIR__ . '/../app/models/account.php';
require __DIR__ . '/../app/models/AccountSuspend.php';
require __DIR__ . '/../app/models/registrasi.php';
require __DIR__ . '/../app/models/room.php';
require __DIR__ . '/../app/models/bookingbase.php';
require __DIR__ . '/../app/models/BookingReschedule.php';
require __DIR__ . '/../app/models/bookinguser.php';
require __DIR__ . '/../app/models/bookingadmin.php';
require __DIR__ . '/../app/models/laporan.php';
require __DIR__ . '/../app/models/feedback.php';

date_default_timezone_set('Asia/Jakarta');

use App\Core\Router;

$router = new Router();
$router->dispatch();
