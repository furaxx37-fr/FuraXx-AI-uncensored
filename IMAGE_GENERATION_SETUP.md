# Image Generation Setup Guide

## Overview
This project now includes uncensored image generation capabilities using Stable Diffusion. The feature integrates seamlessly with the existing chat interface.

## Requirements

### 1. Stable Diffusion WebUI (Automatic1111)
You need to install and run Automatic1111's Stable Diffusion WebUI:

```bash
# Clone the repository
git clone https://github.com/AUTOMATIC1111/stable-diffusion-webui.git
cd stable-diffusion-webui

# Install (follow the official installation guide for your OS)
# For Linux/Mac:
./webui.sh --api --listen

# For Windows:
webui-user.bat --api --listen
```

### 2. Model Requirements
- Download a Stable Diffusion model (e.g., v1.5, SDXL, or custom models)
- Place the model in the `models/Stable-diffusion/` directory
- Popular uncensored models: NovelAI, Waifu Diffusion, or custom fine-tuned models

## Configuration

### 1. API Settings
The image generation API is configured in `image_api.php`:
- Default Stable Diffusion API URL: `http://127.0.0.1:7860`
- Images are saved in the `generated_images/` directory
- Maximum prompt length: 1000 characters

### 2. Starting Stable Diffusion WebUI
```bash
# Start with API enabled and listening on all interfaces
./webui.sh --api --listen --port 7860

# For uncensored generation, you might want to disable safety checker:
./webui.sh --api --listen --disable-safe-unpickle --no-half-vae
```

## Features

### 1. Image Generation Interface
- **Prompt**: Describe the image you want to generate
- **Negative Prompt**: Specify what to avoid in the image
- **Steps**: Number of denoising steps (10-50, default: 20)
- **CFG Scale**: How closely to follow the prompt (1-20, default: 7.5)
- **Image Size**: Multiple preset sizes available

### 2. Image Gallery
- View all generated images in a responsive grid
- Hover to see prompt and action buttons
- Download images directly
- Remove individual images
- Clear entire gallery

### 3. Keyboard Shortcuts
- **Ctrl + Enter** in prompt field: Generate image
- **Tab switching**: Click between Chat and Image Generation

## Usage Tips

### 1. Effective Prompting
```
Good prompt examples:
- "a beautiful landscape with mountains and lake, detailed, 4k, photorealistic"
- "portrait of a woman, digital art, highly detailed, trending on artstation"
- "cyberpunk city at night, neon lights, futuristic, atmospheric"

Negative prompt examples:
- "blurry, low quality, distorted, ugly, bad anatomy"
- "text, watermark, signature, logo"
```

### 2. Parameter Guidelines
- **Low Steps (10-15)**: Faster generation, less detail
- **High Steps (30-50)**: Slower generation, more detail
- **Low CFG (3-7)**: More creative, less prompt adherence
- **High CFG (10-15)**: Strict prompt following, may over-saturate

### 3. Performance Optimization
- Use smaller image sizes for faster generation
- Reduce steps for quicker results
- Consider your GPU memory when choosing parameters

## Troubleshooting

### 1. "Stable Diffusion API is not running"
- Ensure Automatic1111 WebUI is started with `--api` flag
- Check if the service is running on port 7860
- Verify the API URL in `image_api.php`

### 2. "Failed to generate image"
- Check Stable Diffusion WebUI logs for errors
- Ensure you have a model loaded
- Verify sufficient GPU/system memory
- Try reducing image size or steps

### 3. Images not saving
- Check write permissions for `generated_images/` directory
- Verify disk space availability
- Check PHP error logs

### 4. Slow generation
- Reduce image dimensions
- Lower the number of steps
- Use a faster sampler (Euler a is default)
- Ensure adequate GPU memory

## Security Considerations

### 1. Content Filtering
- This implementation is designed to be uncensored
- No built-in content filtering is applied
- Use responsibly and in accordance with local laws

### 2. Resource Management
- Image generation is resource-intensive
- Consider implementing rate limiting for production use
- Monitor disk space usage for generated images

### 3. Access Control
- Consider adding authentication for production deployment
- Implement user quotas if needed
- Monitor API usage

## File Structure
```
project/
├── index.html              # Main interface with tabs
├── api.php                 # Chat API
├── image_api.php           # Image generation API
├── generated_images/       # Generated images directory
├── IMAGE_GENERATION_SETUP.md # This setup guide
└── image_generation.log    # Error logs
```

## Advanced Configuration

### 1. Custom Models
Place custom models in Stable Diffusion WebUI's model directory and restart the service.

### 2. API Customization
Modify `image_api.php` to:
- Change default parameters
- Add new samplers
- Implement custom post-processing
- Add watermarking or metadata

### 3. UI Customization
The interface uses Tailwind CSS and can be easily customized by modifying the HTML and CSS sections.

## Support
For issues specific to:
- **Stable Diffusion WebUI**: Check the official repository
- **Model compatibility**: Refer to model documentation
- **This integration**: Check the error logs and ensure all requirements are met
