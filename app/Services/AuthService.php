<?php
declare(strict_types=1);

namespace App\Services;
use mysqli;

class AuthService {
    private mysqli $db;
    
    // DB connection
    public function __construct(mysqli $db) { $this->db = $db; }

    // Register new applicant
    public function createApplicant(array $data): int {
        $sql = "
            INSERT INTO applicants
            (firstname, surname, username, email, password_hash, role, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return 0;

        $stmt->bind_param(
            "sssssss",
            $data['firstname'],
            $data['surname'],
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['role'],
            $data['timestamp']
        );
        if (!$stmt->execute()) return 0; 

        // Return the ID
        return $this->db->insert_id;
    }
    
    // Check for duplicate username
    public function usernameExists(string $username): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM applicants 
             WHERE username = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new \RuntimeException('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    // Check for duplicate email
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare(
            "SELECT id FROM applicants 
             WHERE email = ? LIMIT 1"
        );

        if (!$stmt) {
            throw new \RuntimeException('Prepare failed: ' . $this->db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    // Find user from a particular table using a username
    public function findUserById(int $id, string $table): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM $table WHERE id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows ? $result->fetch_assoc() : null;
    }

    // Find user from a particular table using a username
    public function findUserByUsername(string $username, string $table): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM $table WHERE username = ? LIMIT 1"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->num_rows ? $result->fetch_assoc() : null;
    }

    // Verify the password
    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    /**
     * Attempt auto-login via remember-me cookie
     * Returns user array on success, null on failure
     */
    public function loginViaRememberToken(string $cookie): ?array {
        if (!str_contains($cookie, ':')) {
            return null;
        }

        [$selector, $token] = explode(':', $cookie, 2);

        $stmt = $this->db->prepare(
            "SELECT rt.user_id, rt.token_hash, rt.expires_at, u.username,
                u.email,  u.firstname, u.surname, u.role
             FROM remember_tokens rt
             JOIN users u ON u.id = rt.user_id
             WHERE rt.selector = ? LIMIT 1"
        );

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $selector);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        if (!$user) {
            return null;
        }

        // Validate token + expiry
        if (
            !hash_equals($user['token_hash'], hash('sha256', $token)) ||
            strtotime($user['expires_at']) <= time()
        ) {
            return null;
        }

        return $user;
    }

    // Create a remember me token
    public function createRememberToken(int $userId): array {
        $selector = bin2hex(random_bytes(6));
        $token    = bin2hex(random_bytes(32));
        $hash     = hash('sha256', $token);
        $expires  = date('Y-m-d H:i:s', strtotime('+30 days'));

        $stmt = $this->db->prepare(
            "INSERT INTO remember_tokens 
             (user_id, selector, token_hash, expires_at)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("isss", $userId, $selector, $hash, $expires);
        $stmt->execute();

        return [
            'selector' => $selector,
            'token'    => $token,
            'expires'  => $expires,
        ];
    }

    // Create or reuse verification token
    public function createEmailVerification(int $userId): array {
        // Reuse existing record if recently sent
        $stmt = $this->db->prepare(
            "SELECT sent_at FROM email_verifications
             WHERE id = ? AND verified_at IS NULL
             LIMIT 1"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        // Cooldown: 5 minutes
        if ($existing && strtotime($existing['sent_at']) > time() - 300) {
            throw new \RuntimeException(
                'Verification email was sent recently. Please wait a few minutes.'
            );
        }

        // Generate new token
        $token   = bin2hex(random_bytes(32));
        $hash    = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', strtotime('+1 day'));
        $now     = date('Y-m-d H:i:s');

        // Upsert (replace old token safely)
        $stmt = $this->db->prepare(
            "INSERT INTO email_verifications (user_id, token_hash, expires_at, sent_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                token_hash = VALUES(token_hash),
                expires_at = VALUES(expires_at),
                sent_at    = VALUES(sent_at)"
        );
        $stmt->bind_param("isss", $userId, $hash, $expires, $now);
        $stmt->execute();

        return [
            'token'   => $token,
            'expires' => $expires,
        ];
    }

    // Verify email by verification token
    public function verifyEmailToken(string $token): array {
        $hash = hash('sha256', $token);

        $stmt = $this->db->prepare(
            "SELECT user_id, expires_at
             FROM email_verifications
             WHERE token_hash = ?
               AND verified_at IS NULL
             LIMIT 1"
        );
        $stmt->bind_param("s", $hash);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        if (!$row || strtotime($row['expires_at']) < time()) {
            return [];
        }

        // Mark token as used
        $stmt = $this->db->prepare(
            "UPDATE email_verifications
             SET verified_at = NOW()
             WHERE user_id = ?"
        );
        $stmt->bind_param("i", $row['user_id']);
        $stmt->execute();

        // Mark applicant as verified
        $stmt = $this->db->prepare(
            "UPDATE applicants
             SET email_verified_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param("i", $row['user_id']);
        $stmt->execute();
        
        // Fetch applicant
        $stmt = $this->db->prepare(
            "SELECT * FROM applicants WHERE id = ?
             LIMIT 1"
        );
        $stmt->bind_param("i", $row['user_id']);
        $stmt->execute();
        $applicant = $stmt->get_result()->fetch_assoc();

        return $applicant;
    }

    // Delete expired email verification tokens
    public function purgeExpiredEmailVerifications(): int {
        $stmt = $this->db->prepare(
            "DELETE FROM email_verifications
             WHERE expires_at < NOW()
                OR (verified_at IS NOT NULL AND verified_at < NOW() - INTERVAL 1 DAY)"
        );

        if (!$stmt) {
            return 0;
        }

        $stmt->execute();
        return $stmt->affected_rows;
    }

    // Keep the hashed verification code
    public function createLoginVerification(int $userId): string {
        $code = (string) random_int(100000, 999999);
        $hash = password_hash((string)$code, PASSWORD_DEFAULT);
        $created = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $this->db->prepare(
            "INSERT INTO login_verifications (user_id, code_hash, created_at, expires_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                code_hash = VALUES(code_hash),
                created_at = VALUES(created_at),
                expires_at = VALUES(expires_at),
                attempts = 0"
        );
        $stmt->bind_param("isss", $userId, $hash, $created, $expires);
        $stmt->execute();

        return $code; // only for email
    }
  
    // Verify the submitted code
    public function verifyLoginCode(int $userId, string $code): bool {
        $stmt = $this->db->prepare(
            "SELECT code_hash, expires_at, attempts
             FROM login_verifications
             WHERE user_id = ? LIMIT 1"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            return false;
        }

        if (strtotime($row['expires_at']) < time()) {
            return false;
        }

        if ($row['attempts'] >= 5) {
            return false;
        }

        if (!password_verify(trim($code), $row['code_hash'])) {
            $stmt = $this->db->prepare(
                "UPDATE login_verifications
                 SET attempts = attempts + 1
                 WHERE user_id = ?"
            );
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            return false;
        }

        // ✅ Success → cleanup
        $stmt = $this->db->prepare(
            "DELETE FROM login_verifications WHERE user_id = ?"
        );
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        return true;
    } 
    
    // Mark verified applicants as pending_approvals
    public function markPendingApproval(int $applicantId): void {
        $stmt = $this->db->prepare(
            "UPDATE applicants
             SET status = 'pending_approval'
             WHERE id = ?"
        );
        $stmt->bind_param("i", $applicantId);
        $stmt->execute();
    }
    
    // Fetch all applicants on pending approvals
    public function fetchPendingApplicants(): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM applicants WHERE status = 'pending_approval'
                AND email_verified_at IS NOT NULL"
        );
        //$stmt->bind_param("s", 'pending_approval');
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    // Move applicants to users table
    public function approveApplicant(int $applicantId): array {
        $this->db->begin_transaction();

        try {
            // Fetch applicant
            $stmt = $this->db->prepare(
                "SELECT firstname, surname, username, email, 
                 password_hash, role FROM applicants WHERE id = ?
                 AND status = 'pending_approval'
                 AND email_verified_at IS NOT NULL
                 LIMIT 1"
            );
            $stmt->bind_param("i", $applicantId);
            $stmt->execute();
            $applicant = $stmt->get_result()->fetch_assoc();

            if (!$applicant) {
                throw new \RuntimeException('Applicant not eligible for approval');
            }

            // Insert user
            $stmt = $this->db->prepare(
                "INSERT INTO users
                 (firstname, surname, username, email, password_hash, role, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            $stmt->bind_param(
                "ssssss", $applicant['firstname'], $applicant['surname'],
                $applicant['username'], $applicant['email'],
                $applicant['password_hash'], $applicant['role']
            );
            $stmt->execute();

            // Delete applicant
            $stmt = $this->db->prepare(
                "DELETE FROM applicants WHERE id = ?"
            );
            $stmt->bind_param("i", $applicantId);
            $stmt->execute();

            $this->db->commit();

            return $applicant;

        } catch (\Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
