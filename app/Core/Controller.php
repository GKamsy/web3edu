<?php

namespace App\Core;
use App\Core\Csrf;

class Controller {
    protected string $layout = 'main';

    // TODO SCHOOL INFO: Change into a logo
    
    protected array $schoolInfo = [
        'name' => 'For A Change Academy',
        'tagline' => 'Empowering Students, Inspiring Minds',
    ];
    
    // Breadcrumbs to show the user where they are
    protected array $breadcrumbs = [ ];
    protected bool $autoBreadcrumbs = false;
    protected function breadcrumbs(array $items): void {
        $this->autoBreadcrumbs = false;
        $this->breadcrumbs = $items;
    }

    protected function generateBreadcrumbs(): void {
        if (!$this->autoBreadcrumbs) {
            return;
        }

        $map = [
            'dashboard' => ['label' => 'Dashboard', 'icon' => '🏠'],
            'student'   => ['label' => 'Student',   'icon' => '🎓'],
            'teacher'   => ['label' => 'Teacher',   'icon' => '👨‍🏫'],
            'principal' => ['label' => 'Admin',     'icon' => '🏛️'],
            'admin'     => ['label' => 'Admin',     'icon' => '🏛️'],

            'courses'   => ['label' => 'Courses',   'icon' => '📚'],
            'students'  => ['label' => 'Students',  'icon' => '🧑‍🎓'],
            'profile'   => ['label' => 'Profile',   'icon' => '👤'],
            'reports'   => ['label' => 'Reports',   'icon' => '📊'],
            'settings'  => ['label' => 'Settings',  'icon' => '⚙️'],
        ];

        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        if ($path === '') {
            return;
        }

        $segments = explode('/', $path);
        $url = '';

        foreach ($segments as $segment) {
            $url .= '/' . $segment;

            $this->breadcrumbs[] = [
                'label' => $map[$segment]['label'] ?? ucfirst($segment),
                'icon'  => $map[$segment]['icon'] ?? '📄',
                'url'   => $url,
            ];
        }
    }
    
    
    
    protected function bootSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            (new PageController())->notAllowed();
            exit;
        }
    }
    
    protected function view(string $view, array $data = []): void {
        $data['_csrf'] = Csrf::token();
        $this->generateBreadcrumbs();
        $data['_breadcrumbs'] = $this->breadcrumbs;
        extract($data, EXTR_SKIP);

        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        $layoutFile = __DIR__ . '/../Views/layouts/' . $this->layout . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        if (!file_exists($layoutFile)) {
            throw new \RuntimeException("Layout not found: {$layoutFile}");
        }

        // Make $content available to the layout
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }

    // Redirect back to a required page.
    protected function redirectBack( 
        string $page, string $image, array $credentials = [] 
    ): void {
        $this->view($page, [
            'school' => $this->schoolInfo,
            'image'       => $image,
            'credentials' => $credentials,
        ]);
        
        return;
    }
    
    protected function renderDashboard(string $view, array $data = []): void {
        $this->bootSession();
        $this->requireAuth();
        $data = array_merge([
            'title'     => 'Campus',
            'firstname' => $_SESSION['firstname'] ?? '',
            'role'      => $_SESSION['role'] ?? '',
            'roleLabel' => $_SESSION['roleLabel'] ?? '',
        ], $data);

        $viewFile = __DIR__ . "/../Views/dashboard/$view.php";
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        // Ajax view
        if ($isAjax) {
            extract($data);
            require $viewFile;
            return;
        }

        // Normal view
        $this->layout = 'dashboard';
        $this->breadcrumbs([['label' => 'Dashboard']]);
        $this->view("dashboard/$view", $data);
    }
}
