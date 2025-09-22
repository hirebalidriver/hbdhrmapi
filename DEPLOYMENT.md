# Deployment Guide

This document explains how to set up and use the deployment workflows for the HBD HRM API application.

## Deployment Workflows

There are two deployment workflows available:

1. **Production Deployment** (`.github/workflows/deploy-production.yml`)
   - Triggers on pushes to `main` or `production` branches
   - Deploys to the production environment

2. **Staging Deployment** (`.github/workflows/deploy-staging.yml`)
   - Triggers on pushes to `develop` or `staging` branches
   - Deploys to the staging environment

## Required Secrets

For the deployment workflows to function correctly, you need to set the following secrets in your GitHub repository:

### Docker Hub Secrets
- `DOCKER_HUB_USERNAME` - Your Docker Hub username
- `DOCKER_HUB_TOKEN` - Your Docker Hub access token

### Production Server Secrets
- `PRODUCTION_SERVER_IP` - IP address of your production server
- `PRODUCTION_SERVER_USER` - SSH username for your production server
- `PRODUCTION_SSH_KEY` - Private SSH key for accessing the production server

### Production Environment Variables
- `APP_KEY` - Laravel application key (base64 encoded)
- `APP_URL` - Production application URL
- `SPA_URL` - Production SPA URL
- `SESSION_DOMAIN` - Session domain for production
- `SANCTUM_STATEFUL_DOMAINS` - Sanctum stateful domains for production
- `DB_HOST` - Production database host
- `DB_PORT` - Production database port
- `DB_DATABASE` - Production database name
- `DB_USERNAME` - Production database username
- `DB_PASSWORD` - Production database password
- `MAIL_MAILER` - Production mail mailer
- `MAIL_HOST` - Production mail host
- `MAIL_PORT` - Production mail port
- `MAIL_USERNAME` - Production mail username
- `MAIL_PASSWORD` - Production mail password
- `MAIL_ENCRYPTION` - Production mail encryption
- `MAIL_FROM_ADDRESS` - Production mail from address
- `MAIL_FROM_NAME` - Production mail from name
- `AWS_ACCESS_KEY_ID` - AWS access key ID for S3
- `AWS_SECRET_ACCESS_KEY` - AWS secret access key for S3
- `AWS_DEFAULT_REGION` - AWS default region
- `AWS_BUCKET` - AWS S3 bucket name
- `FIREBASE_SERVER_KEY` - Firebase server key for push notifications

### Staging Environment Variables
- `STAGING_SERVER_IP` - IP address of your staging server
- `STAGING_SERVER_USER` - SSH username for your staging server
- `STAGING_SSH_KEY` - Private SSH key for accessing the staging server
- `STAGING_APP_URL` - Staging application URL
- `STAGING_SPA_URL` - Staging SPA URL
- `STAGING_SESSION_DOMAIN` - Session domain for staging
- `STAGING_SANCTUM_STATEFUL_DOMAINS` - Sanctum stateful domains for staging
- `STAGING_DB_HOST` - Staging database host
- `STAGING_DB_PORT` - Staging database port
- `STAGING_DB_DATABASE` - Staging database name
- `STAGING_DB_USERNAME` - Staging database username
- `STAGING_DB_PASSWORD` - Staging database password
- `STAGING_MAIL_MAILER` - Staging mail mailer
- `STAGING_MAIL_HOST` - Staging mail host
- `STAGING_MAIL_PORT` - Staging mail port
- `STAGING_MAIL_USERNAME` - Staging mail username
- `STAGING_MAIL_PASSWORD` - Staging mail password
- `STAGING_MAIL_ENCRYPTION` - Staging mail encryption
- `STAGING_MAIL_FROM_ADDRESS` - Staging mail from address
- `STAGING_MAIL_FROM_NAME` - Staging mail from name
- `STAGING_AWS_ACCESS_KEY_ID` - AWS access key ID for S3 (staging)
- `STAGING_AWS_SECRET_ACCESS_KEY` - AWS secret access key for S3 (staging)
- `STAGING_AWS_DEFAULT_REGION` - AWS default region (staging)
- `STAGING_AWS_BUCKET` - AWS S3 bucket name (staging)
- `STAGING_FIREBASE_SERVER_KEY` - Firebase server key for push notifications (staging)

## Deployment Process

1. **Build Process**:
   - The workflow checks out the code
   - Builds a Docker image using the existing Dockerfile
   - Pushes the image to Docker Hub

2. **Deployment Process**:
   - Connects to the server via SSH
   - Stops and removes existing containers
   - Removes all Docker images
   - Pulls the latest image from Docker Hub
   - Creates a new `.env` file with the appropriate environment variables
   - Starts services with `docker-compose up -d --force-recreate --build`
   - Runs database migrations
   - Clears application caches

## Manual Deployment Commands

If you need to deploy manually, you can use these commands on your server:

```bash
# Navigate to the project directory
cd /var/www/hbdhrmapi

# Stop services
docker-compose down

# Remove containers
docker rm -f $(docker ps -a -q) || true

# Remove images
docker rmi -f $(docker images -q) || true

# Pull the latest image
docker pull your-docker-hub-username/hbdhrmapi:latest

# Create .env file with your environment variables
cat > .env << EOF
# Your environment variables here
EOF

# Start services
docker-compose up -d --force-recreate --build

# Run migrations
docker-compose exec app php artisan migrate --force

# Clear caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Troubleshooting

If you encounter issues during deployment:

1. Check that all required secrets are set in GitHub
2. Verify that the SSH key has the correct permissions on the server
3. Ensure the server has Docker and Docker Compose installed
4. Check that the server has sufficient disk space
5. Review the GitHub Actions logs for specific error messages

## Security Considerations

1. Never commit sensitive information to the repository
2. Use strong, unique passwords for all services
3. Regularly rotate your secrets and access tokens
4. Restrict SSH access to only necessary IP addresses
5. Keep your server and Docker images updated with security patches
