#!/bin/sh
mysql -u homestead -psecret -e "GRANT ALL PRIVILEGES ON homestead.* TO 'travis'@'%' IDENTIFIED BY ''"
perl -pi -e 's/upload_max_filesize = 2M/upload_max_filesize = 50M/g' /etc/php5/fpm/php.ini
perl -pi -e 's/post_max_size = 8M/post_max_size = 51M/g' /etc/php5/fpm/php.ini
cp /vagrant/files/20-xdebug.ini /etc/php5/fpm/conf.d/20-xdebug.ini
service nginx restart
service php5-fpm reload
/usr/local/bin/composer self-update
echo "" >> /home/vagrant/.bashrc
echo "PATH=$PATH:/home/vagrant/nerds.dev/vendor/bin" >> /home/vagrant/.bashrc
echo "export PATH" >> /home/vagrant/.bashrc
echo Done!