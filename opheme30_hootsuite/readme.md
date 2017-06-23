Initial setup:

vagrant up
vagrant provision (if this is the very first time vagrant is used)
vagrant ssh

Add this line to your hosts file:
192.168.20.30 hootsuite.opheme.com

mysql -u root -p
Use password: opheme135!

Run commands:
GRANT all privileges ON hootsuite.* TO 'hootsuite'@'localhost';
exit;

Run commands in root folder:
npm install grunt grunt-cli grunt-contrib-clean grunt-contrib-concat grunt-contrib-copy grunt-contrib-less grunt-contrib-uglify grunt-contrib-watch

Done!

Every session:

vagrant up

Do work!

vagrant halt

Done!

---------
UPDATING SERVER CODE

cd /opt/opheme30_hootsuite/ && git pull && grunt default && cp ../opheme30_hootsuite._local_common.php.bkp public/_local_common.php && chown -R www-data: ../opheme30_hootsuite/

Note: /opt/opheme30_hootsuite._local_common.php.bkp  - contains the right DB credentials built in
Note2: will change this in the near future to use a local file with settings
