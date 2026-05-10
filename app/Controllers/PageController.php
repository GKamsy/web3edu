<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Core\Csrf;

class PageController extends Controller {

    // Page under construction
    public function underConstruction(): void {
        $this->view('shared/under-construction', [
            'school' => $this->schoolInfo,
            'image'  => 'construction.png',
        ]);
    }

    // Page not found
    public function notFound(): void {
        $this->view('shared/not-found', [
            'school' => $this->schoolInfo,
            'image'  => 'oops.png',
        ]);
    }

    // Method not allowed
    public function notAllowed(): void {
        $this->view('shared/not-allowed', [
            'school' => $this->schoolInfo,
            'image'  => 'oops.png',
        ]);
    }

    public function showMessages(): void {
        $userId = $_SESSION['user_id'];
        $inbox = $this->messages->inbox($userId);
        echo "Hello";
        exit;
        $this->layout = 'dashboard';
        $this->view('messages/index', [
            'title' => 'Inbox',
            'messages' => $inbox
        ]);
    }

    // Show login page
    public function showLogin(): void {
        
        // Start the session
        $this->bootSession();
        
        // Automatic login by user_id
        if (isset($_SESSION['user_id'])) {
            // Redirect to the dashboard
            header("Location: /dashboard");
            exit;
        }
        
        // Automatic login via remember-me
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember'])) {
            $user = $this->auth->loginViaRememberToken($_COOKIE['remember']);
            if ($user) {
                // Set session
                $_SESSION['user_id']    = (int) $user['user_id'];
                $_SESSION['firstname']  = $user['firstname'];
                $_SESSION['surname']    = $user['surname'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['email']      = $user['email'];
                $_SESSION['role']       = $user['role'];
                $_SESSION['roleLabel']  = ucwords($user['role']);

                // Rotate CSRF token
                Csrf::regenerate();
                
                // Redirect to the dashboard
                header("Location: /dashboard");
                exit;
            }
        }

        $this->view('home/login', [
            'school' => $this->schoolInfo,
            'image'  => 'lock.png',
            'csrf'   => Csrf::token(),
        ]);
    }

    // Show applying page
    public function showApply(): void {
        $this->view('home/apply', [
            'school' => $this->schoolInfo,
            'image'  => 'admission.png',
        ]);
    }

    // Show aprove demo
    public function showApprove(): void {
        $this->bootSession();
        
        // Restict access
        if (($_SESSION['role'] ?? '') !== 'principal') {
            http_response_code(403);
            (new PageController())->notAllowed();
            return;
        }
        
        // Show all pending approvals
        $pendings = (new AuthController())->getPendingApprovals();
        //var_dump($pendings);
        $this->view('home/approve', ['pendings' => $pendings]);
    }
    
    // Homepage
    public function index(): void {

        $this->view('home/index', [
            'school' => $this->schoolInfo,

            'heroSlides' => [
                [
                    'title' => 'A Great Change For All',
                    'subtitle' => 'Where excellence begins',
                    'image' => 'hero1.png',
                    'cta_text' => 'Learn More',
                    'cta_link' => '/about'
                ],
                [
                    'title' => 'A Modern Learning Environment',
                    'subtitle' => 'With a wonderful innovative learning space',
                    'image' => 'hero2.jpg',
                    'cta_text' => 'Learn More',
                    'cta_link' => '/admissions'
                ],
                [
                    'title' => 'Marvelous Inclussive Education',
                    'subtitle' => 'Tolerant and free from discrimination',
                    'image' => 'hero3.png',
                    'cta_text' => 'Learn More',
                    'cta_link' => '/admissions'
                ],
                [
                    'title' => 'Special Feeding Programme',
                    'subtitle' => 'Always caring for better results',
                    'image' => 'hero4.jpg',
                    'cta_text' => 'Learn More',
                    'cta_link' => '/admissions'
                ],
            ],

            'features' => [
                [
                    'icon' => '🎓',
                    'title' => 'Qualified Teachers',
                    'description' => 'Experienced and passionate educators.'
                ],
                [
                    'icon' => '🏫',
                    'title' => 'Modern Facilities',
                    'description' => 'Cutting-edge learning environments.'
                ],
                [
                    'icon' => '🌱',
                    'title' => 'Holistic Growth',
                    'description' => 'Academic and personal development.'
                ],
            ],

            'news' => [
                [
                    'title' => 'Spring Admission Is Open',
                    'summary' => 'Applications are now open for the next academic year.',
                    'image' => 'edu.png',
                    'slug' => 'spring-admissions-open'
                ],
                [
                    'title' => 'New Science Lab Launched',
                    'summary' => 'Our new advanced science lab is now operational.',
                    'image' => 'online.png',
                    'slug' => 'new-science-lab'
                ],
                [
                    'title' => 'Vistors From USA',
                    'summary' => 'Students from USA have finally landed.',
                    'image' => 'vistors.jpg',
                    'slug' => 'new-science-lab'
                ],
                [
                    'title' => 'Education Contest',
                    'summary' => 'The contest for our students will be launched next week.',
                    'image' => 'news1.jpg',
                    'slug' => 'new-science-lab'
                ],
                [
                    'title' => 'Annual Sports Day',
                    'summary' => 'Join us for our annual sports celebration next month.',
                    'image' => 'youth.jpeg',
                    'slug' => 'annual-sports-day'
                ],
            ]
        ]);
    }
}
