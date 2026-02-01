<?php
// UserModel.php
namespace App\Models;

use App\Core\Database;
use PDO;

class UserModel {
    private string $id;
    private string $username;
    private string $first_name;
    private string $last_name;
    private string $email;
    private string $password_hash;

    public static function findByEmail(string $email): ?self {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) return null;

        return self::fromArray($data);
    }

    public static function create(string $username, string $email, string $password): self {
        $db = Database::getInstance();
        $id = self::generateUUID();
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (id, username, email, password_hash) VALUES (:id, :username, :email, :password_hash)");
        $stmt->execute([
            'id' => $id, 
            'username' => $username, 
            'email' => $email, 
            'password_hash' => $hash, 
        ]);

        return self::findByEmail($email);
    }

    public static function verify(string $email, string $password): ?self {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user->password_hash)) {
            return $user;
        }
        return null;
    }

    private static function fromArray(array $data): self {
        $user = new self();
        $user->id = $data['id'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
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

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of username
     */ 
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the value of first_name
     */ 
    public function getFirst_name()
    {
        return $this->first_name;
    }

    /**
     * Get the value of last_name
     */ 
    public function getLast_name()
    {
        return $this->last_name;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }
}