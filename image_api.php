<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Configuration
$STABLE_DIFFUSION_API = 'http://127.0.0.1:7860'; // Default Automatic1111 WebUI URL
$IMAGES_DIR = 'generated_images';
$MAX_PROMPT_LENGTH = 1000;

// Create images directory if it doesn't exist
if (!file_exists($IMAGES_DIR)) {
    mkdir($IMAGES_DIR, 0755, true);
}

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . PHP_EOL, 3, 'image_generation.log');
}

// Function to check if Stable Diffusion API is running
function checkStableDiffusionAPI($api_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url . '/sdapi/v1/options');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}

// Function to generate image using Stable Diffusion API
function generateImageSD($prompt, $negative_prompt = '', $steps = 20, $cfg_scale = 7.5, $width = 512, $height = 512) {
    global $STABLE_DIFFUSION_API;
    
    $payload = [
        'prompt' => $prompt,
        'negative_prompt' => $negative_prompt,
        'steps' => $steps,
        'cfg_scale' => $cfg_scale,
        'width' => $width,
        'height' => $height,
        'sampler_name' => 'Euler a',
        'batch_size' => 1,
        'n_iter' => 1,
        'seed' => -1,
        'restore_faces' => false,
        'tiling' => false
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $STABLE_DIFFUSION_API . '/sdapi/v1/txt2img');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minutes timeout
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception("cURL Error: $error");
    }
    
    if ($httpCode !== 200) {
        throw new Exception("HTTP Error: $httpCode");
    }
    
    $data = json_decode($response, true);
    if (!$data || !isset($data['images']) || empty($data['images'])) {
        throw new Exception("Invalid response from Stable Diffusion API");
    }
    
    return $data['images'][0]; // Return base64 encoded image
}

// Function to save base64 image
function saveBase64Image($base64_data, $filename) {
    global $IMAGES_DIR;
    
    // Remove data URL prefix if present
    if (strpos($base64_data, 'data:image') === 0) {
        $base64_data = substr($base64_data, strpos($base64_data, ',') + 1);
    }
    
    $image_data = base64_decode($base64_data);
    if ($image_data === false) {
        throw new Exception("Failed to decode base64 image data");
    }
    
    $filepath = $IMAGES_DIR . '/' . $filename;
    if (file_put_contents($filepath, $image_data) === false) {
        throw new Exception("Failed to save image file");
    }
    
    return $filepath;
}

// Main API handler
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST method is allowed");
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        throw new Exception("Invalid JSON input");
    }
    
    // Validate required fields
    if (empty($input['prompt'])) {
        throw new Exception("Prompt is required");
    }
    
    $prompt = trim($input['prompt']);
    if (strlen($prompt) > $MAX_PROMPT_LENGTH) {
        throw new Exception("Prompt is too long (max $MAX_PROMPT_LENGTH characters)");
    }
    
    // Get parameters with defaults
    $negative_prompt = isset($input['negative_prompt']) ? trim($input['negative_prompt']) : '';
    $steps = isset($input['steps']) ? max(10, min(50, intval($input['steps']))) : 20;
    $cfg_scale = isset($input['cfg_scale']) ? max(1.0, min(20.0, floatval($input['cfg_scale']))) : 7.5;
    
    // Parse image size
    $size = isset($input['size']) ? $input['size'] : '512x512';
    $size_parts = explode('x', $size);
    $width = isset($size_parts[0]) ? intval($size_parts[0]) : 512;
    $height = isset($size_parts[1]) ? intval($size_parts[1]) : 512;
    
    // Validate dimensions
    $allowed_sizes = [512, 768, 1024];
    if (!in_array($width, $allowed_sizes) || !in_array($height, $allowed_sizes)) {
        throw new Exception("Invalid image dimensions");
    }
    
    // Check if Stable Diffusion API is running
    if (!checkStableDiffusionAPI($STABLE_DIFFUSION_API)) {
        throw new Exception("Stable Diffusion API is not running. Please start Automatic1111 WebUI on $STABLE_DIFFUSION_API");
    }
    
    // Generate image
    $base64_image = generateImageSD($prompt, $negative_prompt, $steps, $cfg_scale, $width, $height);
    
    // Save image
    $filename = 'img_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.png';
    $filepath = saveBase64Image($base64_image, $filename);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'image_url' => $filepath,
        'filename' => $filename,
        'prompt' => $prompt,
        'parameters' => [
            'negative_prompt' => $negative_prompt,
            'steps' => $steps,
            'cfg_scale' => $cfg_scale,
            'width' => $width,
            'height' => $height
        ]
    ]);
    
} catch (Exception $e) {
    logError("Image generation error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
