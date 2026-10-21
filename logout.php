<?php
// code that will execute upon clicking the log out button, to check for the session token and delete it so the status can update automatically to 'checked_out'
require 'CapstoneSample_db.php';

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];

    try {
        
        $stmt = $pdo->prepare("SELECT userLog_id FROM user_sessions WHERE session_token = :token");
        $stmt->execute([':token' => $token]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($session) {
            $userLog_id = $session['userLog_id'];

           
            $pdo->beginTransaction();

            
            $updateStatus = $pdo->prepare("UPDATE visitor_log SET status = 'checked_out' WHERE log_id = :user_id");
            $updateStatus->execute([':user_id' => $userLog_id]);

            
            $deleteSession = $pdo->prepare("DELETE FROM user_sessions WHERE session_token = :token");
            $deleteSession->execute([':token' => $token]);

            $pdo->commit();
        }
    } catch (Exception $e) {
    
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }

    
    setcookie("auth_token", "", [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}


header("Location: visitorLogin.html");
exit;