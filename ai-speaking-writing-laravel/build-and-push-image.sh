#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Configuration
DOCKER_USERNAME="${DOCKER_USERNAME:-thienv29}"
IMAGE_NAME="${IMAGE_NAME:-iclc-speaking-and-writing-app}"
VERSION="${VERSION:-latest}"
FULL_IMAGE_NAME="${DOCKER_USERNAME}/${IMAGE_NAME}:${VERSION}"

log_info "Building Docker image: ${FULL_IMAGE_NAME}"

# Build image
docker build -t "${FULL_IMAGE_NAME}" .

if [ $? -eq 0 ]; then
    log_info "Image built successfully!"
    
    # Ask if user wants to push to Docker Hub
    read -p "Do you want to push this image to Docker Hub? (y/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        log_info "Pushing image to Docker Hub..."
        docker push "${FULL_IMAGE_NAME}"
        
        if [ $? -eq 0 ]; then
            log_info "Image pushed successfully to Docker Hub!"
            log_info "You can now use this image in docker-compose.deploy.yml:"
            log_info "  image: ${FULL_IMAGE_NAME}"
        else
            log_error "Failed to push image to Docker Hub"
            exit 1
        fi
    else
        log_warn "Image built but not pushed. You can push it later with:"
        log_warn "  docker push ${FULL_IMAGE_NAME}"
    fi
else
    log_error "Failed to build image"
    exit 1
fi

