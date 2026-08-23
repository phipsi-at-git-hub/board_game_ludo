<?php
// UserModel.php
namespace App\Models;

use App\Constants\Application;

final class UserModel extends BaseModel {
    // ToDo: Use constant from application.php 

    private string $id;
    private string $username;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $password_hash;
    private string $role;
    private string $status;
    private ?string $last_login = null;
    private string $created_at;
    private string $updated_at;
    private ?string $reset_token = null;
    private ?string $reset_expires_at = null;

    // User - Find user by id (UUID)
    public static function findById(string $id): ?self {
        $row = static::fetchOne(
            "SELECT * FROM users WHERE id = :id LIMIT 1", 
            [
                'id' => $id
            ]
        );
        return $row ? static::fromArray($row) : null;
    }

    // User - Find user by email
    public static function findByEmail(string $email): ?self {
        $row = static::fetchOne(
            "SELECT * FROM users WHERE email = :email LIMIT 1", 
            [
                'email' => $email
            ]
        );
        return $row ? static::fromArray($row) : null;
    }

    // User - Get all users
    public static function all(): array {
        $rows = static::fetchAll("SELECT * FROM users ORDER BY created_at DESC");
        return array_map(fn($row) => self::fromArray($row), $rows);
    }

    // User - Count all users
    public static function countAll(): int {
        return static::count("SELECT COUNT(*) FROM users");
    }

    // User - Count all users with specific status
    public static function countByStatus(string $status): int {
        return static::count(
            "SELECT COUNT(*) FROM users WHERE status = :status", 
            ['status' => $status]
        );
    }

    // User - Count all users with specific role
    public static function countByRole(string $role): int {
        return static::count(
            "SELECT COUNT(*) FROM users WHERE role = :role",
            ['role' => $role]
        );
    }

    // User - Create user
    /*
    public static function create(string $username, string $email, string $password): self {
        $id = self::generateUUID();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        static::execute(
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
    */
    
    /**
     * Create new user
     * 
     * create
     *
     * @param string $username
     * @param string $email
     * @param ?string $password
     * @param string $role
     * @param string $status
     * @return self
     */
    public static function create(string $username, string $email, ?string $password = null, string $role = Application::USER, string $status = Application::ACTIVE): self {
        $id = self::generateUUID();
        $password_hash = $password !== null ? password_hash($password, PASSWORD_DEFAULT) : null;

        static::execute(
            "INSERT INTO users (
                id,
                username,
                email,
                password_hash,
                role,
                status
            ) VALUES (
                :id,
                :username,
                :email,
                :password_hash,
                :role,
                :status
            )",
            [
                'id' => $id,
                'username' => $username,
                'email' => $email,
                'password_hash' => $password_hash,
                'role' => $role,
                'status' => $status,
            ]
        );
        return self::findById($id);
    }

    // Save current User
    public function save(): bool {
        return $this->updateUser($this->toArray());
    }

    private function updateUser(array $user_array) {
        return static::execute(
            sprintf(
                "UPDATE 
                    users
                SET
                    username = :username, 
                    email = :email, 
                    role = :role, 
                    status = :status 
                WHERE 
                    id = :id", 
                
                Application::TABLE_USERS, 

                Application::USERNAME, 
                Application::EMAIL, 
                Application::ROLE, 
                Application::STATUS, 

                Application::ID
            ), [
                'username' => $user_array[Application::USERNAME], 
                'email' => $user_array[Application::EMAIL], 
                'role' => $user_array[Application::ROLE], 
                'status' => (int)$user_array[Application::STATUS], 
                'id' => $this->id
            ]
        );
    }

    // User - Update user
    public function updateProfile(string $username, string $email): bool {
        $this->username = $username;
        $this->email = $email;

        return static::execute(
            "UPDATE users SET username = :username, email = :email WHERE id = :id", 
            [
                'username' => $username, 
                'email' => $email, 
                'id' => $this->id,
            ]
        );
    }

    // User - Delete user
    public function delete(): bool {
        return static::execute(
            "DELETE FROM users WHERE id = :id", 
            [
                'id' => $this->id
            ]
        );
    }

    // User - Update last_login
    public function updateLastLogin(): bool {
        return static::execute(
            sprintf(
                "UPDATE 
                    users
                SET
                    last_login = NOW()
                WHERE 
                    id = :id", 
                
                Application::TABLE_USERS, 

                Application::LAST_LOGIN, 

                Application::ID
            ), [
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
        return static::execute(
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
        
        static::execute(
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
        $row = static::fetchOne(
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

        return static::execute(
            "UPDATE users SET reset_token = NULL, reset_token_expires_at = NULL WHERE id = :id", 
            [
                'id' => $this->id
            ]
        );
    }

    // Helper - Create UserModel from Array
    private static function fromArray(array $data): self {
        $user = new self();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->role = $data['role'] ?? 'user';
        $user->status = $data['status'] ?? 'active'; 
        $user->last_login = $data['last_login']; 
        /*
        $user->created_at = $data['created_at'];
        $user->updated_at = $data['updated_at'];
        */
        return $user;
    }

    // Helper - Create Array from GameModel
    private function toArray(): array {
        $user_array[Application::ID] = $this->id;
        $user_array[Application::USERNAME] = $this->username;
        $user_array[Application::EMAIL] = $this->email;
        $user_array[Application::PASSWORD_HASH] = $this->password_hash;
        $user_array[Application::ROLE] = $this->role;
        $user_array[Application::STATUS] = $this->status;
        $user_array[Application::LAST_LOGIN] = $this->last_login; 
        $user_array[Application::CREATED_AT] = $this->created_at;
        $user_array[Application::UPDATED_AT] = $this->updated_at;

        return $user_array;
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

    // Check if User is User
    public function isUser(): bool {
        return $this->role === Application::USER;
    }

    // Check if User is Admin
    public function isAdmin(): bool {
        return $this->role === Application::ADMIN;
    }

    // Check if User is Moderator
    public function isModerator(): bool {
        return $this->role === Application::MODERATOR;
    }

    // Check if User is Game Master
    public function isGameMaster(): bool {
        return $this->role === Application::GAME_MASTER;
    }

    // Get the value of status
    public function getStatus() {
        return $this->status;
    }

    // Check if User is active
    public function isActive(): bool {
        return $this->status === Application::ACTIVE;
    }

    // Check if User is inactive
    public function isInactive(): bool {
        return $this->status === Application::INACTIVE;
    }

    // Check if User is closed
    public function isClosed(): bool {
        return $this->status === Application::CLOSED;
    }

    // Check if User is Blocked
    public function isBlocked(): bool {
        return $this->status === Application::BLOCKED;
    }

    // Check if User is Banned
    public function isBanned(): bool {
        return $this->status === Application::BANNED;
    }

    // Get last login
    public function getLastLogin() {
        return $this->last_login;
    }

     // Get the value of created_at
    public function getCreated_at() {
        return $this->created_at;
    }

    // Get the value of updated_at
    public function getUpdated_at() {
        return $this->updated_at;
    }
}