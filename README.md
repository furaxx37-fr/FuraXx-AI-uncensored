# Uncensored Chat - Llama.cpp Web Interface

A sleek, modern web interface for llama.cpp with the aesthetic of uncensored.chat.

## Features

- 🎨 **Dark theme** with purple/pink gradients matching uncensored.chat
- 🚫 **No filters, no limits** - completely uncensored AI responses
- ⚙️ **Adjustable parameters** (temperature, max tokens)
- 💬 **Real-time chat** with typing indicators
- 📱 **Responsive design** works on all devices
- 🔧 **Easy setup** with llama.cpp backend

## Installation

1. Install and compile llama.cpp
2. Download a compatible model (GGUF format)
3. Start the llama.cpp server:
   ```bash
   ./llama-server --model your-model.gguf --host 0.0.0.0 --port 8081
   ```
4. Deploy the web files to your web server
5. Update the API endpoint in `api.php` if needed

## Configuration

- **Server URL**: Update `LLAMA_SERVER_URL` in `api.php`
- **Model**: Configure your model path when starting llama.cpp server
- **Port**: Default is 8081, change in both server and `api.php`

## Files

- `index.html` - Main web interface
- `api.php` - Backend API handler for llama.cpp communication

## Requirements

- Web server with PHP support
- llama.cpp compiled and running
- Compatible GGUF model file

## License

MIT License - Feel free to modify and distribute
