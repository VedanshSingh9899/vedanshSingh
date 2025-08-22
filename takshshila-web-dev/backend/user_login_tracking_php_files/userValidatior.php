<?php

class UserValidator {
    private $conn;
    private $redis;

    /**
     * The constructor now requires both a mysqli and a Redis connection.
     */
    public function __construct(mysqli $conn, Redis $redis) {
        $this->conn = $conn;
        $this->redis = $redis;
    }

    /**
     * The main validation function to check both databases.
     * @param string $username The username to check.
     * @param string $email The email to check.
     * @return array An array containing a boolean 'status' and a 'message'.
     */
    public function isUserUnique(string $username, string $email, string $phone): array {
        // 1. Check if username exists in Redis
        if ($this->usernameExistsInRedis($username)) {
            return ['status' => false, 'message' => 'Username already exists.'];
        }

        // 2. Check if username exists in SQL
        if ($this->usernameExistsInSql($username)) {
            return ['status' => false, 'message' => 'Username already exists.'];
        }
        
        // 3. Check if email exists in SQL
        if ($this->emailExistsInSql($email)) {
            return ['status' => false, 'message' => 'Email address is already registered.'];
        }
        // 4. check if email exists in Redis
        if ($this->emailExistsInRedis($email)) {
            return ['status' => false, 'message' => 'Email address is already registered in Redis.'];
        }
        // 5. check if phone exists in Redis
        if ($this->phoneExistsInRedis($phone)) {
            return ['status' => false, 'message' => 'Phone number is already registered in Redis.'];
        }
        // 6. check if phone exists in SQL
        if ($this->phoneExistsInSql($phone)) {
            return ['status' => false, 'message' => 'Phone number is already registered in SQL.'];
        }
        // If all checks pass, the user is unique
        return ['status' => true, 'message' => 'Username and email are available.'];
    }

    /**
     * Checks if a username exists in the Redis database.
     * It checks for the existence of the key 'user:username'.
     */
    private function usernameExistsInRedis(string $username): bool {
        // exists() returns 1 if the key exists, 0 otherwise.
        return $this->redis->exists('user:' . $username) === 1;
    }
 

    /**
     * Checks if an email exists in the 'personalinfo' SQL table.
     */
    private function emailExistsInSql(string $email): bool {
        $stmt = $this->conn->prepare("SELECT uid FROM user_data WHERE email_id = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $count = $stmt->num_rows;
        $stmt->close();
        return $count > 0;
    }

    /**
     * Checks if a username exists in the 'logindata' SQL table.
     * !! IMPORTANT: You must change 'username_column' to your actual column name !!
     */
    private function usernameExistsInSql(string $username): bool {
        // !! CHANGE 'username_column' to your actual column name !!
        $stmt = $this->conn->prepare("SELECT * FROM user_password WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $count = $stmt->num_rows;
        $stmt->close();
        return $count > 0;
    }
    /**
     * Checks if an email exists in the Redis database.
     * It scans through all 'user:*' hashes to find a matching email.
     * WARNING: This can be inefficient on databases with a very large number of users.
     * A more performant approach would be to maintain an email-to-username index in Redis.
     */
    private function emailExistsInRedis(string $email): bool {

        // Scan through all keys that match the pattern 'user:*'
        $keys = $this->redis->keys('user:*');
            foreach ($keys as $key) {
                // For each user key, get the email field from the hash
                $storedEmail = $this->redis->hget($key, 'email');
                if ($storedEmail === $email) {
                    // Email found, no need to scan further
                    return true;
                }
            }
        

        // Scanned all keys and no match was found
        return false;
    }
         /**
     * Checks if an phone exists in the Redis database.
     * It scans through all 'user:*' hashes to find a matching phone.
     * WARNING: This can be inefficient on databases with a very large number of users.
     * A more performant approach would be to maintain an phone-to-username index in Redis.
     */
    private function phoneExistsInRedis(string $phone): bool {
        $keys = $this->redis->keys('user:*');
            foreach ($keys as $key) {
                $storedPhone = $this->redis->hget($key, 'phone');
                if ($storedPhone === $phone) {
                    // Phone found, no need to scan further
                    return true;
                }
            }
        

        // Scanned all keys and no match was found
        return false;   
    }
    /**
     * Checks if an phone exists in the 'personalinfo' SQL table.
     */
    private function phoneExistsInSql(string $phone): bool {
        $stmt = $this->conn->prepare("SELECT uid FROM user_data WHERE phone_number = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->store_result();
        $count = $stmt->num_rows;
        $stmt->close();
        return $count > 0;
    }
}
?>