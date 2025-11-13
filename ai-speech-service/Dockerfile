FROM python:3.11-slim

WORKDIR /app

# Install system dependencies for audio processing (--no-install-recommends để giảm packages)
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends curl ca-certificates xz-utils; \
    rm -rf /var/lib/apt/lists/*; \
    ARCH="$(dpkg --print-architecture)"; \
    case "$ARCH" in \
      amd64) FFMPEG_URL="https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-amd64-static.tar.xz" ;; \
      arm64|aarch64) FFMPEG_URL="https://johnvansickle.com/ffmpeg/releases/ffmpeg-release-arm64-static.tar.xz" ;; \
      *) echo "Unsupported architecture: $ARCH" >&2; exit 1 ;; \
    esac; \
    curl -L "$FFMPEG_URL" -o /tmp/ffmpeg.tar.xz; \
    mkdir -p /opt/ffmpeg; \
    tar -xJf /tmp/ffmpeg.tar.xz -C /opt/ffmpeg --strip-components=1; \
    ln -s /opt/ffmpeg/ffmpeg /usr/local/bin/ffmpeg; \
    ln -s /opt/ffmpeg/ffprobe /usr/local/bin/ffprobe; \
    rm /tmp/ffmpeg.tar.xz

# Copy requirements and install Python dependencies
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY app.py .

# Create output directories
RUN mkdir -p outputs/audio outputs/text

# Expose port
EXPOSE 8000

# Run the application
CMD ["uvicorn", "app:app", "--host", "0.0.0.0", "--port", "8000"]

