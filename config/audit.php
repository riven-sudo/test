<?php
// Simple Audit helper (POC)
// Usage: Audit::log('action', 'entity', $entity_id, $old_array, $new_array, $meta_array);

class Audit {
    public static function maskSensitive($arr) {
        if (!$arr || !is_array($arr)) return $arr;
        $sensitive = ['password','pass','pwd','cc_number','card_number','cvv','ssn'];
        foreach ($sensitive as $k) {
            if (array_key_exists($k, $arr)) {
                $arr[$k] = '***REDACTED***';
            }
            // also check nested arrays
            foreach ($arr as $key => $val) {
                if (is_array($val) && array_key_exists($k, $val)) {
                    $arr[$key][$k] = '***REDACTED***';
                }
            }
        }
        return $arr;
    }

    public static function getIP() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    public static function log($action, $entity=null, $entity_id=null, $old=null, $new=null, $meta=array()) {
        global $conn;
        if (!isset($conn)) return false;

        // Prepare user info
        $user_type = null;
        $user_id = null;
        $username = null;

        if (!empty($_SESSION['user'])) {
            $user_type = 'admin';
            $username = $_SESSION['user'];
        } elseif (!empty($_SESSION['customer_id'])) {
            $user_type = 'customer';
            $user_id = $_SESSION['customer_id'];
            $username = $_SESSION['customer'] ?? null;
        } else {
            $user_type = 'anonymous';
        }

        // Mask sensitive fields
        if (is_array($old)) $old = self::maskSensitive($old);
        if (is_array($new)) $new = self::maskSensitive($new);
        if (is_array($meta)) $meta = self::maskSensitive($meta);

        // We only store a concise action; do not persist verbose old/new/meta details or entity identifiers.
        $ip = self::getIP();
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $url = (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')) . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');

        $sql = "INSERT INTO audit_logs (user_type, user_id, username, action, ip, user_agent, url) VALUES (?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, 'sisssss', $user_type, $user_id, $username, $action, $ip, $ua, $url);

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    // Simple fetch for admin viewer (POC)
    public static function fetchRecent($limit = 100) {
        global $conn;
        $limit = intval($limit);
        $sql = "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT {$limit}";
        $res = mysqli_query($conn, $sql);
        $rows = [];
        if ($res) {
            while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;
        }
        return $rows;
    }
}

?>