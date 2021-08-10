cd /var/www/html
sudo chmod 777 -R .git/
git reset --hard
git pull
wget -O/dev/null -q http://localhost/helpers/validate_models.php