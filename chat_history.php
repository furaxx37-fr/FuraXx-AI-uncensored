<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$historyDir = 'chat_history';
if (!file_exists($historyDir)) {
    mkdir($historyDir, 0755, true);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'save':
        $input = json_decode(file_get_contents('php://input'), true);
        $sessionId = $input['session_id'] ?? date('Y-m-d_H-i-s') . '_' . uniqid();
        $messages = $input['messages'] ?? [];
        $title = $input['title'] ?? 'Chat Session';
        
        $historyData = [
            'session_id' => $sessionId,
            'title' => $title,
            'messages' => $messages,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $filename = $historyDir . '/' . $sessionId . '.json';
        if (file_put_contents($filename, json_encode($historyData, JSON_PRETTY_PRINT))) {
            echo json_encode(['success' => true, 'session_id' => $sessionId]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to save chat history']);
        }
        break;
        
    case 'load':
        $sessionId = $_GET['session_id'] ?? '';
        if (empty($sessionId)) {
            echo json_encode(['success' => false, 'error' => 'Session ID required']);
            break;
        }
        
        $filename = $historyDir . '/' . $sessionId . '.json';
        if (file_exists($filename)) {
            $historyData = json_decode(file_get_contents($filename), true);
            echo json_encode(['success' => true, 'data' => $historyData]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Chat history not found']);
        }
        break;
        
    case 'list':
        $files = glob($historyDir . '/*.json');
        $sessions = [];
        
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                $sessions[] = [
                    'session_id' => $data['session_id'],
                    'title' => $data['title'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['updated_at'],
                    'message_count' => count($data['messages'])
                ];
            }
        }
        
        // Sort by updated_at descending
        usort($sessions, function($a, $b) {
            return strtotime($b['updated_at']) - strtotime($a['updated_at']);
        });
        
        echo json_encode(['success' => true, 'sessions' => $sessions]);
        break;
        
    case 'delete':
        $sessionId = $_GET['session_id'] ?? $_POST['session_id'] ?? '';
        if (empty($sessionId)) {
            echo json_encode(['success' => false, 'error' => 'Session ID required']);
            break;
        }
        
        $filename = $historyDir . '/' . $sessionId . '.json';
        if (file_exists($filename)) {
            if (unlink($filename)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to delete chat history']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Chat history not found']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        break;
}
?>
