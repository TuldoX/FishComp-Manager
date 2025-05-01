FROM nginx:stable-alpine

# Add custom Nginx config
COPY conf.d/default.conf /etc/nginx/conf.d/default.conf

# Create required directories
RUN mkdir -p /var/www/frontend/public /var/www/backend/public

# Use non-root nginx user from the nginx image
USER nginx

# Expose port
EXPOSE 80

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]