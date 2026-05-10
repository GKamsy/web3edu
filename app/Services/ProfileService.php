<?php

namespace App\Services;
use mysqli;

class ProfileService {
    
    /*================================================
                DB connection
    ================================================*/
    private mysqli $db;
    public function __construct(mysqli $db) { $this->db = $db; }

    public function findUser(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM users WHERE id = ? LIMIT 1
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();

        return $result;
    }

    public function updateProfile(int $userId, string $firstname, string $surname, string $email): void {
        $stmt = $this->db->prepare("
            UPDATE users
            SET firstname = ?, surname = ?, email = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sssi", $firstname, $surname, $email, $userId);
        $stmt->execute();
        $stmt->close();
    }

    public function updatePassword(int $userId, string $hash): void {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password_hash = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $hash, $userId);
        $stmt->execute();
        $stmt->close();
    }
    
    public function handleAvatarUpload(int $userId, array $file): string {
        $uploadDir = dirname(__DIR__, 2) . '/public/images/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Upload error: " . $file['error']);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new \Exception("Invalid file type.");
        }

        // ==============================
        // 1️⃣ Store Original Temporarily
        // ==============================
        $tempName = 'temp_' . $userId . '_' . time() . '.' . $ext;
        $tempPath = $uploadDir . $tempName;

        if (!move_uploaded_file($file['tmp_name'], $tempPath)) {
            throw new \Exception("File move failed.");
        }

        // ==============================
        // 2️⃣ Create Image Resource
        // ==============================
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $sourceImage = imagecreatefromjpeg($tempPath);
                break;
            case 'png':
                $sourceImage = imagecreatefrompng($tempPath);
                break;
            case 'webp':
                $sourceImage = imagecreatefromwebp($tempPath);
                break;
            default:
                unlink($tempPath);
                throw new \Exception("Unsupported format.");
        }

        if (!$sourceImage) {
            unlink($tempPath);
            throw new \Exception("Image processing failed.");
        }

        // ==============================
        // 3️⃣ Resize to 300x300 (Square Crop)
        // ==============================
        $width  = imagesx($sourceImage);
        $height = imagesy($sourceImage);
        $size   = min($width, $height);

        $cropped = imagecrop($sourceImage, [
            'x' => ($width - $size) / 2,
            'y' => ($height - $size) / 2,
            'width' => $size,
            'height' => $size
        ]);

        $finalImage = imagecreatetruecolor(300, 300);

        imagecopyresampled(
            $finalImage,
            $cropped,
            0, 0, 0, 0,
            300, 300,
            $size, $size
        );

        // ==============================
        // 4️⃣ Convert to WebP (80% Quality)
        // ==============================
        $newFileName = 'avatar_' . $userId . '_' . time() . '.webp';
        $finalPath   = $uploadDir . $newFileName;

        imagewebp($finalImage, $finalPath, 80);

        // Free memory
        imagedestroy($sourceImage);
        imagedestroy($cropped);
        imagedestroy($finalImage);

        // ==============================
        // 5️⃣ Delete Temporary Original
        // ==============================
        unlink($tempPath);

        // ==============================
        // 6️⃣ Delete Old Avatar
        // ==============================
        $user = $this->findUser($userId);
        $currentAvatar = $user['avatar'] ?? null;

        if ($currentAvatar && $currentAvatar !== 'default.png') {
            $oldPath = $uploadDir . $currentAvatar;
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // ==============================
        // 7️⃣ Update Database
        // ==============================
        $stmt = $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->bind_param("si", $newFileName, $userId);
        $stmt->execute();
        $stmt->close();

        return $newFileName;
    }
}