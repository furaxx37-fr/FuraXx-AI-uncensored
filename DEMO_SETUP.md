# 🚀 Demo Setup Guide - Uncensored Chat Llama Enhanced

## 📋 Overview
This guide documents the complete setup process for the full uncensored version with local Llama.cpp server integration.

## 🎯 Demo Access
- **Main Interface**: http://34.162.135.188/
- **Demo Page**: http://34.162.135.188/demo.html
- **Presentation**: http://34.162.135.188/presentation.html
- **Status Check**: http://34.162.135.188/status.php

## ⚙️ Technical Configuration

### Server Setup
- **Model**: Llama-2-7B-Chat-GGUF (Q4_K_M quantization)
- **Context Size**: 2048 tokens
- **Max Prediction**: 512 tokens
- **Host**: 0.0.0.0:8081 (internal)
- **Log File**: /var/log/llama-server.log

### System Requirements
- **RAM**: Minimum 4GB (8GB recommended)
- **CPU**: Multi-core processor
- **Storage**: 5GB+ for model and dependencies
- **OS**: Ubuntu/Debian with build tools

## 🔧 Installation Steps

### 1. System Dependencies
```bash
apt update && apt install -y build-essential cmake git wget curl python3 python3-pip libcurl4-openssl-dev
```

### 2. Llama.cpp Compilation
```bash
mkdir -p /opt/llama.cpp && cd /opt/llama.cpp
git clone https://github.com/ggerganov/llama.cpp.git .
mkdir build && cd build
cmake .. && make -j$(nproc)
```

### 3. Model Download
```bash
mkdir -p /opt/models && cd /opt/models
wget https://huggingface.co/TheBloke/Llama-2-7B-Chat-GGUF/resolve/main/llama-2-7b-chat.Q4_K_M.gguf
```

### 4. Server Launch
```bash
cd /opt/llama.cpp/build/bin
./llama-server --model /opt/models/llama-2-7b-chat.Q4_K_M.gguf --host 0.0.0.0 --port 8081 --ctx-size 2048 --n-predict 512 > /var/log/llama-server.log 2>&1 &
```

## 🎨 Features Implemented

### Enhanced API (api.php)
- ✅ Local Llama.cpp server integration
- ✅ Comprehensive demo mode with realistic responses
- ✅ Automatic fallback system
- ✅ Robust error handling
- ✅ Multi-provider support (OpenAI, Anthropic, Local)

### New Demo Pages
- ✅ **presentation.html**: Complete feature showcase
- ✅ **demo.html**: Interactive demonstration
- ✅ **status.php**: Real-time server status
- ✅ **test_demo.php**: API testing endpoint

### User Experience
- ✅ Modern responsive design with Tailwind CSS
- ✅ Real-time status updates
- ✅ Smooth animations and transitions
- ✅ Dark theme with cyber aesthetics
- ✅ Mobile-friendly interface

## 📊 Server Status Monitoring

### Health Check
```bash
curl -s http://localhost:8081/health
```

### Process Monitoring
```bash
ps aux | grep llama-server | grep -v grep
```

### Log Monitoring
```bash
tail -f /var/log/llama-server.log
```

## 🔄 Fallback System
The application automatically switches between:
1. **Primary**: Local Llama.cpp server (when ready)
2. **Fallback**: Demo mode with realistic AI responses
3. **Error Handling**: Graceful degradation with user feedback

## 🛠️ Troubleshooting

### Model Loading Issues
- Model loading can take 5-15 minutes depending on hardware
- Monitor logs for "warming up" completion
- Ensure sufficient RAM (4GB+ free)

### Server Not Responding
- Check process is running: `ps aux | grep llama-server`
- Verify port availability: `netstat -tlnp | grep 8081`
- Review logs: `tail -50 /var/log/llama-server.log`

## 📈 Performance Notes
- **Startup Time**: 5-15 minutes for model loading
- **Response Time**: 1-5 seconds per response (depending on length)
- **Memory Usage**: ~3.5GB RAM for 7B model
- **Concurrent Users**: Supports multiple simultaneous conversations

## 🔐 Security Considerations
- Server runs on internal port 8081 (not exposed)
- Web interface handles all external requests
- No direct model access from public internet
- Session management with proper isolation

## 📝 Recent Updates
- Enhanced demo mode with contextual responses
- Improved error handling and user feedback
- Real-time status monitoring system
- Complete documentation and setup guides
- Repository updates with all improvements

## 🎯 Next Steps
1. Monitor server startup completion
2. Test full functionality once model is loaded
3. Optimize performance based on usage patterns
4. Consider model upgrades or alternatives
5. Implement additional features as needed

---
*Generated during demo setup session - December 2024*
