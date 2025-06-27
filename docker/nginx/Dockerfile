FROM nginx:latest

WORKDIR /var/www

COPY public public
COPY docker/nginx/conf.d/ /etc/nginx/conf.d/
# COPY cert.pem /root/ssl/cert.pem
# COPY key.pem /root/ssl/key.pem

CMD ["nginx", "-g", "daemon off;"]

