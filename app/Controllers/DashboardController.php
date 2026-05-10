<?php
declare(strict_types=1);

namespace App\Controllers;
use App\Core\Controller;
use App\Services\Security;
use App\Services\DashboardService;

class DashboardController extends Controller {

    private DashboardService $db;
    public function __construct() {
        require __DIR__ . '/../../config/config.php';
        $this->db = new DashboardService($conn);
    }
    

    /*================================================
                Dashboard landing page
    ================================================*/
    public function dashboard(): void {

        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'];

        $data = [ ];
        $roleCounts = $this->db->getRoleCounts();
        $totalUsers = array_sum($roleCounts);
        $data['users'] = $totalUsers;
        $data['courses'] = $this->db->getCoursesCounts($userId, $userRole);

        // Optional: define expected roles (for consistent UI)
        $defaultRoles = ['admin', 'principal', 'teacher', 'student', 'mentor', 'guardian', 'smc'];

        foreach ($defaultRoles as $role) {
            $data[$role] = $roleCounts[$role] ?? 0;
        }

        // Also pass full dynamic list (important!)
        $data['roleCounts'] = $roleCounts;

        $view = __DIR__ . '/../Views/dashboard/index.php';
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // AJAX request → load partial only
        if ($isAjax) {
            require $view;
            return;
        }

        // Normal request → load full dashboard layout
        $this->layout = 'dashboard';
        $this->breadcrumbs([['label' => 'Dashboard']]);

        $this->view('dashboard/index', $data);
    }
    

    /*================================================
                AJAX dashboard controller
    ================================================*/
    public function dashboardController($page) {
        $this->bootSession();
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        // Data required for the view
        $data = [];
        
        // Add data based on dashboard requests
        switch ($page) {
            case 'course-registration':
                $data['courses'] = $this->db->registerCourse($userId);
                break;
            case 'tasks':
                //$data['tasks'] = $this->db->getTasks($_SESSION['user_id']);
                break;
            case 'courses':
                $data['courses'] = $this->db->getAllCourses();
                break;
            case 'modules':
                $courseId = $_GET['course_id'] ?? $_POST['course_id'] ?? null;
                $courseId = (int) Security::clean($courseId);
                $_SESSION['courseId'] = $courseId;
                $data['courseId'] = $courseId;
                $data['courses'] = $this->db->getAllCourses();
                $data['modules'] = $this->db->getCourseModules($_SESSION['courseId']);
                break;

            case 'lessons':
                $moduleId = $_GET['module_id'] ?? $_POST['module_id'] ?? null;
                $moduleId = (int) Security::clean($moduleId);
                $data['modules'] = $this->db->getCourseModules($_SESSION['courseId']);
                $data['lessons'] = $this->db->getAllLessons($moduleId);
                break;

            case 'binance':
                require __DIR__ . '/../../config/candidates.php';
                $data['candidates'] = $candidates;
                break;
        }
        $this->renderDashboard($page, $data);
    }

    
    /*================================================
                Set module heading controller
    ================================================*/
    public function setHeading(): void {

        $this->bootSession();
        $this->requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['heading'])) {
            $_SESSION['heading'] = $data['heading'];
        }

        echo json_encode(['success' => true]);
        exit;
    }


    /*=================================================================================
                Create or update entity (course, module, lesson, etc)
    ==================================================================================*/
    public function crudData(): void{

        $this->bootSession();
        $this->requireAuth();

        if($_SESSION['role'] !== 'admin'){
            echo json_encode(['success'=>false,'message'=>'Unauthorized']);
            exit;
        }
        
        $type = Security::clean($_POST['type']);
        $goal = Security::clean($_POST['goal']);
        $data_id = Security::clean($_POST['data_id']);
        $data_id = (int)$data_id;
        $skiped = array("delete", "cancel");
        
        if (!in_array($goal, $skiped)) {
            switch ($type) {
                case 'course':
                    $status = $_POST['registration_status'] ?? '';
                    if ($status === '') {
                        $status = 'closed';
                    }
                    
                    $data = [
                        'code' => Security::clean($_POST['code']),
                        'title' => Security::clean($_POST['title']),
                        'description' => Security::clean($_POST['description']),
                        'prerequisites' => Security::clean($_POST['prerequisites']),
                        'duration' => Security::clean($_POST['duration']),
                        'status' => Security::clean($status)
                    ];
                    break;
                
                case 'module':
                    $data = [
                        'course_id' => (int)Security::clean($_POST['course_id']),
                        'topic' => Security::clean($_POST['topic']),
                        'week' => (int)Security::clean($_POST['week']),
                        'stage' => Security::clean($_POST['stage']),
                        'days' => (int)Security::clean($_POST['days']),
                        'description' => Security::clean($_POST['description'])
                    ];
                    break;
                
                case 'lesson':
                    $data = [
                        'module_id' => (int)Security::clean($_POST['module_id']),
                        'title' => Security::clean($_POST['title']),
                        'overview' => Security::clean($_POST['overview']),
                        'introduction' => Security::clean($_POST['introduction']),
                        'readings' => Security::clean($_POST['readings']),
                        'videos' => Security::clean($_POST['videos']),
                        'discussion_question' => Security::clean($_POST['discussion_question']),
                        'discussion_answer' => Security::clean($_POST['discussion_answer']),
                        'assignment_question' => Security::clean($_POST['assignment_question']),
                        'assignment_answer' => Security::clean($_POST['assignment_answer'])
                    ];
                    break;

                case 'klines':
                    $aim = $_POST['aim'] ?? null;
                    $interval = $_POST['interval'] ?? null;
                    /*if($interval == "1d"){
                        require __DIR__ . '/../../config/klinesDay.php';
                    }
                    else{
                        require __DIR__ . '/../../config/klinesHour.php';
                    }*/
                    break;
            }
        }

        $result = false;
        if ($goal === 'alter') {
            //require __DIR__ . '/../../config/symbols.php';
            //require __DIR__ . '/../../config/klinesDay.php';
            $this->db->klinesDatabase("d", "alter");
        }
        elseif ($goal === 'create') {
            $result = $this->db->createEntity($type, $data);
        }
        elseif ($goal === 'update') {
            $data['data_id'] = $data_id;
            $result = $this->db->updateEntity($type, $data);
        }
        elseif ($goal === 'delete') {
            $table = Security::clean($_POST['table']);
            $result = $this->db->deleteData($table, $data_id);
        }
        elseif ($goal === 'cancel') {
            $result = true;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $result, ]);
        exit;
    }

    public function enrollCourse(): void {
        $this->bootSession();
        $this->requireAuth();

        $studentId = $_SESSION['user_id'];
        $courseId = (int) $_POST['course_id'];
        $course = $this->db->getCourse($courseId);
        if ($course['registration_status'] === 'closed') {
            echo json_encode([
                'success' => false,
                'message' => 'Registration closed'
            ]);
            exit;
        }

        if ($course['registration_status'] === 'waitlist') {
            $this->db->addToWaitlist($studentId, $courseId);
            echo json_encode([
                'success'=>true,
                'message'=>'Added to waitlist'
            ]);
            exit;
        }

        $this->db->enrollStudent($studentId, $courseId);
        echo json_encode(['success'=>true]);
    }
}
