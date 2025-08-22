<?php
$redis_host = '127.0.0.1';
$redis_port = 6379;
//$redis_timeout = 2.5; //Connection timeout in seconds dalna hai agar chull ho to

// Instantiate the Redis client
$redis = new Redis();

try {
    // ye janana hai to tum chutie ho
    // Connect to the Redis server using the specified host and port.
    $redis->connect($redis_host, $redis_port, /*$redis_timeout*/);

    // man me chull hogi to password ki zarurat nahi hai
    // Uncomment the next line if your Redis server requires authentication.
    // $redis->auth('your_redis_password');

    // ping likhne par pong aayega
    if ($redis->ping('hello') !== 'hello') {
         throw new RedisException('Failed to connect to Redis: Invalid PONG received.');
    }

} catch (RedisException $e) {
    http_response_code(503); //for idiot users jinko 503 ka pata nahi hai
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Could not connect to the Redis server. Please try again later. Error: ' . $e->getMessage()
    ]);
    exit;
}
?>