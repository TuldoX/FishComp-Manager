FROM nginx:latest

#copy everything in the frontend folder to this folder
COPY . /usr/share/nginx/html

#copy configuration to configuration inside container
COPY nginx.conf /etc/nginx/conf.d/default.conf

#expose port
EXPOSE 80