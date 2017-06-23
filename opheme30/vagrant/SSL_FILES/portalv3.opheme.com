server {
  listen 443;

 ssl on;
 ssl_certificate /etc/nginx/SSL/cert.crt;
 ssl_certificate_key /etc/nginx/SSL/cert.key;
 ssl_session_cache shared:SSL:10m;
 ssl_session_timeout 5m;
 ssl_protocols SSLv3 TLSv1 TLSv1.1 TLSv1.2;
 ssl_ciphers HIGH:!aNULL:!MD5;
 ssl_prefer_server_ciphers on;

    server_name portalv3.opheme.com;
    root /home/vagrant/Code/public;

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    access_log off;
    error_log  /var/log/nginx/portalv3.opheme.com-error.log error;

    error_page 404 /index.php;

    sendfile off;

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}

