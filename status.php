<?php
header('Content-Type: application/json');

// Check llama server status
$health_check = @file_get_contents('http://localhost:8081/health');
$llama_status = $health_check ? json_decode($health_check, true) : null;

// Check if server is ready
$server_ready = false;
if ($llama_status && !isset($llama_status['error'])) {
    $server_ready = true;
}

// Get server process info
$process_info = shell_exec('ps aux | grep llama-server | grep -v grep');
$server_running = !empty(trim($process_info));

echo json_encode([
    'server_running' => $server_running,
    'server_ready' => $server_ready,
    'llama_response' => $llama_status,
    'demo_mode_available' => true,
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
