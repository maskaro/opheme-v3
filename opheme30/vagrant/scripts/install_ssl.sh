#!/usr/bin/env bash

echo ">>> Installing SSL"

# make the directory
sudo mkdir -p /etc/nginx/SSL

# Copy files across
sudo cp /home/vagrant/Code/vagrant/SSL_FILES/cert.crt /etc/nginx/SSL/cert.crt
sudo cp /home/vagrant/Code/vagrant/SSL_FILES/cert.key /etc/nginx/SSL/cert.key
sudo cp /home/vagrant/Code/vagrant/SSL_FILES/portalv3.opheme.com /etc/nginx/sites-enabled/portalv3.opheme.com

# restart nginx
sudo service nginx restart