<?php
// UserModel.php
namespace App\Models;

use App\Core\Database;

class UserModel extends BaseModel {
    private string $id;
    private string $username;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $password_hash;
    private string $role;
    private ?string $reset_token = null;
    private ?string $reset_expires_at = null;

    // Profile - Find profile by id (UUID)
    public static function findById(string $id): ?self {
        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT * FROM users WHERE id = :id LIMIT 1", 
            [
                'id' => $id
            ]
        );

        return $row ? self::fromArray($row) : null;
    }

    // Profile - Find profile by email
    public static function findByEmail(string $email): ?self {
        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT * FROM users WHERE email = :email LIMIT 1", 
            [
                'email' => $email
            ]
        );

        return $row ? self::fromArray($row) : null;
    }

    // Profile - Create profile
    public static function create(string $username, string $email, string $password): self {
        $db = Database::getInstance();
        $id = self::generateUUID();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $db->execute(
            "INSERT INTO users (id, username, email, password_hash) VALUES (:id, :username, :email, :password_hash)", 
            [
                'id' => $id, 
                'username' => $username, 
                'email' => $email, 
                'password_hash' => $hash, 
            ]
        );

        return self::findByEmail($email);
    }

    // Profile - Update profile
    public function updateProfile(string $username, string $email): bool {
        $this->username = $username;
        $this->email = $email;

        return $this->db->execute(
            "UPDATE users SET username = :username, email = :email WHERE id = :id", 
            [
                'username' => $username, 
                'email' => $email, 
                'id' => $this->id,
            ]
        );
    }

    // Profile - Delete profile
    public function delete(): bool {
        return $this->db->execute(
            "DELETE FROM users WHERE id = :id", 
            [
                'id' => $this->id
            ]
        );
    }

    // Password - Verify user and password
    public static function verify(string $email, string $password): ?self {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }
        return null;
    }

    // Password - Verify password
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password_hash);
    }

    // Password - Update password
    public function updatePassword(string $new_password): bool {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        return $this->db->execute(
            "UPDATE users SET password_hash = :hash WHERE id = :id", 
            [
                'hash' => $password_hash, 
                'id' => $this->id, 
            ]
        );
    }

    // Reset password - Create reset token
    public static function createPasswordResetToken(string $email): ?string {
        $user = self::findByEmail($email);
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + 3600);

        
        $user->db->execute(
            "UPDATE users SET reset_token = :token, reset_token_expires_at = :expires WHERE id = :id", 
            [
                'token' => $token,
                'expires' => $expires_at, 
                'id' => $user->id,
            ]
        );

        return $token;
    }

    // Reset password - Find reset token
    public static function findByResetToken(string $token): ?self {
        $db = Database::getInstance();
        $row = $db->fetch(
            "SELECT * FROM users WHERE reset_token = :token AND reset_token_expires_at > NOW() LIMIT 1", 
            [
                'token' => $token
            ]
        );

        return $row ? self::fromArray($row) : null;
    }

    // Reset password - Clear reset token
    public function clearResetToken(): bool {
        $this->reset_token = null;
        $this->reset_expires_at = null;

        return $this->db->execute(
            "UPDATE users SET reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id", 
            [
                'id' => $this->id
            ]
        );
    }

    private static function fromArray(array $data): self {
        $user = new self();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->role = $data['role'] ?? 'user';
        return $user;
    }

    private static function generateUUID(): string {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', 
            random_int(0, 0xffff), random_int(0, 0xffff), 
            random_int(0, 0xffff), 
            random_int(0, 0x0fff) | 0x4000, 
            random_int(0, 0x3fff) | 0x8000, 
            random_int(0, 0xffff), random_int(0, 0xffff), random_int(0, 0xffff) 
        );
    }

    // Get the value of id 
    public function getId(): string {
        return $this->id;
    }

    // Get the value of username 
    public function getUsername(): string {
        return $this->username;
    }

    // Get the value of first_name
    public function getFirst_name(): string {
        return $this->first_name;
    }

    // Get the value of last_name
    public function getLast_name(): string {
        return $this->last_name;
    }

    // Get the value of email
    public function getEmail(): string {
        return $this->email;
    }

    // Get the value of role
    public function getRole(): string {
        return $this->role;
    }

    // Check if User is Admin
    public function isAdmin(): bool {
        return $this->role === 'admin';
    }
}