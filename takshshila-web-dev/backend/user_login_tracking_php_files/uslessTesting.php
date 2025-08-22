<?php
class RegistrationFinalizer
{
    /** @var mysqli */
    private $conn;

    /** @var Redis */
    private $redis;

    /** @var array */
    private $session;

    /**
     * Constructor to inject dependencies.
     * @param mysqli $conn The active MySQLi database connection.
     * @param Redis $redis The active Redis client connection.
     * @param array $session The $_SESSION superglobal.
     */
    public function __construct(mysqli $conn, Redis $redis, array &$session)
    {
        $this->conn = $conn;
        $this->redis = $redis;
        $this->session = &$session;
    }

    /**
     * Executes the entire registration finalization process.
     */
    public function processRequest(): void
    {
        
            // 1. Validate session and get temporary user data from Redis.
            $username = $this->validateAndGetUsername();
            $redisUserKey = 'user:' . $username;
            $userData = $this->fetchDataFromRedis($redisUserKey);

            // 2. Validate the integrity of the fetched data.
            $this->validateUserData($userData);

            // 3. Persist the user data to the database in a transaction.
            $this->saveToDatabase($userData);

            
            
            // 4. Clean up temporary data from Redis on success.
            $this->redis->del($redisUserKey);
            
    }
    
    private function validateAndGetUsername(): string
    {
        if (!isset($this->session['username'])) {
            throw new Exception('User session not found. Please start the registration process again.', 401);
        }
        return $this->session['username'];
    }

    private function fetchDataFromRedis(string $redisKey): array
    {
        $userData = $this->redis->hgetall($redisKey);
        if (empty($userData)) {
            throw new Exception('Temporary user registration data not found. Please start the registration process again.', 404);
        }
        return $userData;
    }

    private function saveSessionToDatabase(int $uid): void
    {
        $sessionId = $this->session['session_id'] ?? session_id();
        
        $sql = "INSERT INTO user_sessions (uid, session_id, last_active) VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE session_id = VALUES(session_id), last_active = NOW()";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare session insert statement: ' . $this->conn->error, 500);
        }
        $stmt->bind_param("is", $uid, $sessionId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to save session data: ' . $stmt->error, 500);
        }
        $stmt->close();
    }


    private function validateUserData(array $userData): void
    {
        $requiredKeys = ['username', 'password', 'first_name', 'last_name', 'email', 'phone'];
        foreach ($requiredKeys as $key) {
            if (!isset($userData[$key])) {
                throw new Exception("Incomplete user data. Missing '{$key}'. Please restart registration.", 400);
            }
        }
    }

    private function saveToDatabase(array $userData): void
    {
        $this->conn->begin_transaction();
        try {
            // Insert into the parent table `user_password`.
            $sqlUserPassword = "INSERT INTO user_password (username, pass) VALUES (?, ?)";
            $stmtUserPassword = $this->conn->prepare($sqlUserPassword);
            $stmtUserPassword->bind_param("ss", $userData['username'], $userData['password']);
            $stmtUserPassword->execute();

            // Get the new ID for the foreign key.
            $newUserId = $this->conn->insert_id;

            // Insert into the child table `user_data`.
            $sqlUserData = "INSERT INTO user_data (uid, first_name, last_name, email_id, Phone_number) VALUES (?, ?, ?, ?, ?)";
            $stmtUserData = $this->conn->prepare($sqlUserData);
            $stmtUserData->bind_param("issss", $newUserId, $userData['first_name'], $userData['last_name'], $userData['email'], $userData['phone']);
            $stmtUserData->execute();

            // Save the session ID to the database.
            $this->saveSessionToDatabase($newUserId);
            
            
            // Commit the transaction if everything succeeded.
            $this->conn->commit();
            
        } catch (mysqli_sql_exception $e) {
            $this->conn->rollback();
            throw new Exception('Database error during registration: ' . $e->getMessage(), 500, $e);
        } finally {
            // Statements are closed automatically when they go out of scope.
            if (isset($stmtUserPassword)&& isset($stmtUserData)) {
                $stmtUserPassword->close();
                $stmtUserData->close();
            }
        }
    }
}
?>