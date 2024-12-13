FROM nginx:alpine

COPY mysite.local.conf /etc/nginx/conf.d/default.conf

RUN adduser -u 82 -D -S -G www-data www-data \
  && chown -R www-data:www-data /var/cache/nginx /var/run /var/log/nginx \
  && sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf

COPY nginx.conf /etc/nginx/

WORKDIR /app
