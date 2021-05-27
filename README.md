# Micro Display

a micro display using a raspberry pi zero and mini PiTFT hat. testing out pulling this to the pi so maybe i can have them do automatic pulls...

### Hardware 

 * all in one package for hardware: [Mini Color PiTFT Ad Blocking Pi-Hole Kit - No Soldering!](https://www.adafruit.com/product/4475)
 * raspberry pi zero wh: [Raspberry Pi Zero WH (Zero W with Headers)](https://www.adafruit.com/product/3708)
 * raspberry pi zero wh: [Adafruit Mini PiTFT - 135x240 Color TFT Add-on for Raspberry Pi](https://www.adafruit.com/product/4393)

## Setup

first run the raspberry pi config wizard and get everything setup so you can ssh into the pi and it's connected to your network. also make sure to enable the spi interface

```bash
sudo raspi-config
```

### All in One super command to install everything

this is what i've used to setup a few raspberry pi zeros... slightly modified to install some different python libraries... it's kinda terrifying to run. here's hoping i didn't mess up the pi... lol well it seems to have crashed this time. i'll see if i can finish the setup and use the command log to update this setup.

#### basic

```bash
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install apache2 -y && sudo a2enmod rewrite && sudo service apache2 restart && sudo apt-get install php -y && sudo apt-get install libapache2-mod-php -y && sudo apt-get install mariadb-server -y && sudo apt-get install php-mysql -y && sudo service apache2 restart && sudo apt-get install python -y && sudo apt-get install python-serial -y && sudo apt-get install python-serial -y && sudo ln -s /var/www/html www && sudo chown -R pi:pi /var/www/html && sudo chmod 777 /var/www/html && sudo apt-get install git -y && sudo apt-get install python-urllib3 -y
```

#### complete mini pitft micro display setup command of epic doom

```bash
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install apache2 -y && sudo a2enmod rewrite && sudo service apache2 restart && sudo apt-get install php -y && sudo apt-get install libapache2-mod-php -y && sudo apt-get install mariadb-server -y && sudo apt-get install php-mysql -y && sudo service apache2 restart && sudo apt-get install python -y && sudo apt-get install python-serial -y && sudo apt-get install python-serial -y && sudo ln -s /var/www/html www && sudo chown -R pi:pi /var/www/html && sudo chmod 777 /var/www/html && sudo apt-get install git -y && sudo apt-get install python-urllib3 -y && sudo apt-get install python3-pip -y && sudo pip3 install adafruit-circuitpython-rgb-display && sudo pip3 install --upgrade --force-reinstall spidev && sudo apt-get install ttf-dejavu -y && sudo apt-get install python3-pil -y && sudo apt-get install python3-numpy -y
```

#### complete eInk micro display setup command of epic doom

```bash
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install apache2 -y && sudo a2enmod rewrite && sudo service apache2 restart && sudo apt-get install php -y && sudo apt-get install libapache2-mod-php -y && sudo apt-get install mariadb-server -y && sudo apt-get install php-mysql -y && sudo service apache2 restart && sudo apt-get install python -y && sudo apt-get install python-serial -y && sudo apt-get install python-serial -y && sudo ln -s /var/www/html www && sudo chown -R pi:pi /var/www/html && sudo chmod 777 /var/www/html && sudo apt-get install git -y && sudo apt-get install python-urllib3 -y && curl https://get.pimoroni.com/inkyphat | bash
```

### Individual commands

for when you wanna take your time and not sit around worried about what's happening

```bash
sudo apt-get update
```

```bash
sudo apt-get upgrade -y
```

```bash
sudo apt-get install apache2 -y
```

```bash
sudo a2enmod rewrite
```

```bash
sudo service apache2 restart
```

```bash
sudo apt-get install php -y
```

```bash
sudo apt-get install libapache2-mod-php -y
```

```bash
sudo apt-get install mariadb-server -y
```

```bash
sudo apt-get install php-mysql -y
```

```bash
sudo service apache2 restart
```

```bash
sudo apt-get install python -y
```

```bash
sudo apt-get install python-serial -y
```

```bash
sudo apt-get install python-pip -y
```

```bash
sudo ln -s /var/www/html www
```

```bash
sudo chown -R pi:pi /var/www/html
```

```bash
sudo chmod 777 /var/www/html
```

```bash
sudo apt-get install git -y
```

```bash
sudo apt-get install python-urllib3 -y
```

### Setup the mysql database

```bash
sudo mysql -u root
```

```sql
[MariaDB] use mysql;
[MariaDB] update user set plugin='' where User='root';
[MariaDB] flush privileges;
[MariaDB] \q
```

This needs to be followed by the following command:

```bash
mysql_secure_installation
```

### Setup Mini PiTFT all in one maga command of doom

this is going to be very slow and scary. just let it do its thing. the adafruit-circuitpython-rgb-dislay is the scariest and just kinda hangs. but it's ok. it's installing

```bash
sudo apt-get install python3-pip -y && sudo pip3 install adafruit-circuitpython-rgb-display && sudo pip3 install --upgrade --force-reinstall spidev && sudo apt-get install ttf-dejavu -y && sudo apt-get install python3-pil -y && sudo apt-get install python3-numpy -y
```

### Setup Mini PiTFT individual steps.

the adafruit-circuitpython-rgb-dislay is the scariest and just kinda hangs. but it's ok. it's installing. also make sure to turn on the spi interface in the raspi-config

```bash
sudo apt-get install python3-pip -y
```

```bash
sudo pip3 install adafruit-circuitpython-rgb-display
```

```bash
sudo pip3 install --upgrade --force-reinstall spidev 
```

```bash
sudo apt-get install ttf-dejavu -y
```

```bash
sudo apt-get install python3-pil -y
```

```bash
sudo apt-get install python3-numpy -y
```

see [adafruit](https://learn.adafruit.com/adafruit-mini-pitft-135x240-color-tft-add-on-for-raspberry-pi/python-setup) for examples and documentation of mini pitft


### Setup for eInk display

```bash
curl https://get.pimoroni.com/inkyphat | bash
```

see [inky-phat](https://github.com/pimoroni/inky-phat) for examples and documentation of inky phat

## Cron Jobs

```bash
sudo crontab -e
```

```Apache config
1 * * * * sh /var/www/html/gitpull.sh
2 * * * * sh /var/www/html/plugins/NullSensors/gitpull.sh
3 * * * * sh /var/www/html/plugins/NullWeather/gitpull.sh
#4 * * * * sh /var/www/html/extensions/MealPlanner/gitpull.sh
5 * * * * wget -O/dev/null -q http://localhost/helpers/validate_models.php

* * * * * wget -O/dev/null -q http://localhost/services/every_minute.php
0 * * * * wget -O/dev/null -q http://localhost/services/every_hour.php
6 0 * * * wget -O/dev/null -q http://localhost/services/every_day.php
7 0 1 * * wget -O/dev/null -q http://localhost/services/every_month.php
8 0 1 1 * wget -O/dev/null -q http://localhost/services/every_year.php
9 0 * * 1 wget -O/dev/null -q http://localhost/services/every_week.php
```

### mini pitft cron job

```bash
crontab -e
```

```Apache config
@reboot sudo sh /var/www/html/python/pitft/screen.sh
```

### eInk python cron job

```bash
crontab -e
```

```Apache config
* * * * * sh /var/www/html/python/eInk/refresh.sh
```

## Plugins

* required for weather data: [NullWeather](https://github.com/sophiathekitty/NullWeather)
* required for room temperature data: [NullSensors](https://github.com/sophiathekitty/NullSensors)

## Extensions

* required for eInk display: [MealPlanner](https://github.com/sophiathekitty/MealPlanner)

## Tools

 * [favicon generator](https://www.favicon-generator.org/)
 * [open source icons](https://game-icons.net/)
