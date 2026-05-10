<?php

namespace App\Services;
use mysqli;

class DashboardService {
    
    
    /*================================================
                DB connection
    ================================================*/
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }


    /*================================================
              Get counts
    ================================================*/
    public function getRoleCounts(): array {
        $sql = "SELECT role, COUNT(*) as total FROM users GROUP BY role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        $counts = [];
        while ($row = $result->fetch_assoc()) {
            $counts[$row['role']] = (int) $row['total'];
        }

        return $counts;
    }


    /*================================================
              Get courses counts
    ================================================*/
    public function getCoursesCounts(int $id, string $role): int {
        $sql = "SELECT COUNT(id) as total ";
        $needsBinding = false;
        switch ($role) {
            case "teacher":
                $sql .= "FROM course_classes WHERE teacher_id = ?";
                $needsBinding = true;
                break;
            case "student":
                $sql .= "FROM course_enrollments WHERE student_id = ?";
                $needsBinding = true;
                break;
            default:
                $sql .= "FROM courses";
        }

        $stmt = $this->db->prepare($sql);
        if ($needsBinding) {$stmt->bind_param("i", $id); }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return (int) $row['total'];
    }


    /*================================================
           Courses to register
    ================================================*/
    public function registerCourse(int $studentId): array {

        $sql = "
            SELECT 
                c.*,
                ce.id AS enrolled
            FROM courses c
            LEFT JOIN course_enrollments ce
                ON ce.course_id = c.id
                AND ce.student_id = ?
            ORDER BY c.title
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $studentId);
        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
    

    /*================================================
           Enroll students
    ================================================*/
    public function enrollStudentCourse(int $studentId, int $courseId): bool {
        $stmt = $this->db->prepare("
            INSERT INTO course_enrollments
            (student_id, course_id, status)
            VALUES (?, ?, 'enrolled')
        ");

        $stmt->bind_param("ii", $studentId, $courseId);
        return $stmt->execute();
    }


    /*================================================
              Get all courses
    ================================================*/
    public function getAllCourses(): array {
        $sql = "SELECT * FROM courses ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /*================================================
              Get all modules
    ================================================*/
    public function getCourseModules(int $courseId): array {
        $sql = "SELECT * FROM course_modules WHERE course_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $courseId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }



    /*================================================
              Get all lessons
    ================================================*/
    public function getAllLessons(int $moduleId): array {
        $sql = "SELECT * FROM lessons WHERE module_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $moduleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }


    /*================================================
       Create entity (course, module, lesson, etc)
    ================================================*/
    public function createEntity(string $type, array $data): bool {
        switch ($type) {
            case 'course':
                $sql = "INSERT INTO courses
                        (code, title, description, prerequisites, duration, registration_status)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "ssssis", $data['code'], $data['title'], $data['description'],
                    $data['prerequisites'], $data['duration'], $data['status']
                );
                break;
            
            case 'module':
                $sql = "INSERT INTO course_modules
                        (course_id, week_number, unlock_after_days, topic, stage, description)
                        VALUES (?, ?, ?, ?, ?, ?)";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "iiisss", $data['course_id'], $data['week'], $data['days'],
                    $data['topic'], $data['stage'], $data['description']
                );
                break;
            
            case 'lesson':
                $sql = "INSERT INTO lessons (module_id, title, overview, introduction, readings, video_url, 
                            discussion_question, discussion_answer, assignment_question, assignment_answer
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "isssssssss", $data['module_id'], $data['title'], $data['overview'], 
                    $data['introduction'], $data['readings'], $data['videos'], $data['discussion_question'], 
                    $data['discussion_answer'], $data['assignment_question'], $data['assignment_answer']
                );
                break;
        }
        return $stmt->execute();
    }


    /*================================================
              Update Entity
    =================================================*/
    public function updateEntity(string $type, array $data): bool {
        switch ($type) {
            case 'course':
                $sql = "UPDATE courses
                    SET code=?, title=?, description=?, prerequisites=?, duration=?, registration_status=?
                    WHERE id=?";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "ssssisi", $data['code'], $data['title'], $data['description'],
                    $data['prerequisites'], $data['duration'], $data['status'], $data['data_id']
                );
                break;
            
            case 'module':
                $sql = "UPDATE course_modules
                        SET course_id=?, week_number=?, unlock_after_days=?, topic=?, stage=?, description=?
                        WHERE id=?";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "iiisssi", $data['course_id'], $data['week'], $data['days'],
                    $data['topic'], $data['stage'], $data['description'], $data['data_id']
                );
                break;
            
            case 'lesson':
                $sql = "UPDATE lessons
                        SET module_id=?, title=?, overview=?, introduction=?, readings=?, video_url=?, discussion_question=?, 
                        discussion_answer=?, assignment_question=?, assignment_answer=? WHERE id=?";

                $stmt = $this->db->prepare($sql);
                $stmt->bind_param( "isssssssssi", $data['module_id'], $data['title'], $data['overview'], $data['introduction'], 
                    $data['readings'], $data['videos'], $data['discussion_question'], $data['discussion_answer'], 
                    $data['assignment_question'], $data['assignment_answer'], $data['data_id']
                );
                break;
        }

        return $stmt->execute();
    }


    /*================================================
              Delete data from table
    ================================================*/
    public function deleteData(string $table, int $data_id): bool {
        $sql = "DELETE FROM $table WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $data_id);
        return $stmt->execute();
    }


    /*================================================
                Klines Database Handler
    ================================================*/
    public function klinesDatabase(string $interval, string $aim, array $klines = []) {
        require __DIR__ . '/../../config/symbols.php';
        //require __DIR__ . '/../../config/klinesDay.php';
        //$symbols = array_keys($klines);
        foreach ($symbols as $symbol) {
            $table = $interval . "_" . $symbol;
            switch ($aim) {
                // Creating the tables
                case 'create':
                    $sql = "CREATE TABLE IF NOT EXISTS $table (
                        id INT(15) AUTO_INCREMENT PRIMARY KEY,
                        open_time INT(15) UNIQUE NOT NULL, 
                        open_price VARCHAR(50) UNIQUE NOT NULL,  
                        high_price VARCHAR(50) UNIQUE NOT NULL,  
                        low_price VARCHAR(50) UNIQUE NOT NULL,  
                        volume VARCHAR(50) UNIQUE NOT NULL,  
                        close_price VARCHAR(50) UNIQUE NOT NULL, 
                        close_time INT(15) UNIQUE NOT NULL, 
                        asset_volume VARCHAR(50) UNIQUE NOT NULL,  
                        trades INT(20) UNIQUE NOT NULL, 
                        buyer_volume VARCHAR(50) UNIQUE NOT NULL, 
                        buyer_quote_volume VARCHAR(50) UNIQUE NOT NULL, 
                        extra VARCHAR(50) UNIQUE NOT NULL, 
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    break;
                    
                case 'drop':
                    $sql = "DROP TABLE IF EXISTS $table";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    break;
                    
                case 'alter':
                    $sql = "ALTER TABLE $table MODIFY open_time BIGINT";
                    $sql2 = "ALTER TABLE $table MODIFY close_time BIGINT";
                    $stmt = $this->db->prepare($sql);
                    $stmt->execute();
                    $stmt2 = $this->db->prepare($sql2);
                    $stmt2->execute();
                    break;
                    
                // Creating the tables
                case 'insert':
                    foreach ($klines[$symbol] as $kline) {
                        $sql = "INSERT INTO $table
                                (open_time, open_price, high_price, low_price, close_price, volume, close_time, 
                                    asset_volume, trades, buyer_volume, buyer_quote_volume, extra)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                        $stmt = $this->db->prepare($sql);
                        $stmt->bind_param( "isssssisssss", $kline[0], $kline[1], $kline[2], $kline[3], $kline[4], $kline[5], 
                                $kline[6], $kline[7], $kline[8], $kline[9], $kline[10], $kline[11]
                        );
                    }
                    break;
            }
        }
    }
}
