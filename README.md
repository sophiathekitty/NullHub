# Null Hub

this is a base setup for my home automation and similar projects that i'm running on raspberry pis. the folder setup is to help me keep stuff organized and also because the folders have pretty icons in visual studio code.

i'm going to try to get the basic features i want for the different pis. and i'm probably going to see about forking this repo for the different projects that will use this base.

most of this project is currently private but my goal is to make as much of it available as open source and try to include instructions for setting up the different raspberry pi devices.

i apologize for anybody looking for something useful now... i'll probably focus mostly on some of the smaller device projects before i'm ready to make key features like the hub available. but if any of this code helps you with something you're working on it that's awesome.

## Setup

first run the raspberry pi config wizard and get everything setup so you can ssh into the pi and it's connected to your network. may also want to enable the serial, gpio, and spi interfaces depending on the project.

```bash
sudo raspi-config
```

### All in One super command to install everything

this is what i've used to setup a few raspberry pi zeros... slightly modified to install some different python libraries... it's kinda terrifying to run. here's hoping i didn't mess up the pi... lol well it seems to have crashed this time. i'll see if i can finish the setup and use the command log to update this setup.

#### raspberry pi

```bash
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install apache2 -y && sudo a2enmod rewrite && sudo service apache2 restart && sudo apt-get install php -y && sudo apt-get install libapache2-mod-php -y && sudo apt-get install mariadb-server -y && sudo apt-get install php-mysql -y && sudo service apache2 restart && sudo apt-get install python -y && sudo ln -s /var/www/html www && sudo chown -R pi:pi /var/www/html && sudo chmod 777 /var/www/html && sudo apt-get install git -y && sudo apt-get install python3-urllib3 -y && sudo apt-get install nmap -y
```

#### ubuntu

```bash
sudo apt-get update && sudo apt-get upgrade -y && sudo apt-get install apache2 -y && sudo a2enmod rewrite && sudo service apache2 restart && sudo apt-get install php -y && sudo apt-get install libapache2-mod-php -y && sudo apt-get install mariadb-server -y && sudo apt-get install php-mysql -y && sudo service apache2 restart && sudo apt-get install python -y && sudo ln -s /var/www/html www && sudo chown -R pi:pi /var/www/html && sudo chmod 777 /var/www/html && sudo apt-get install git -y && sudo apt-get install python-urllib3 -y && sudo apt-get install nmap -y
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
sudo apt-get install python3-urllib3 -y
```

```bash
sudo apt-get install nmap -y
```

### Setup the mysql database

```bash
sudo mysql -u root
```

```mysql
[MariaDB] use mysql;
[MariaDB] update user set plugin='' where User='root';
[MariaDB] flush privileges;
[MariaDB] \q
```

This needs to be followed by the following command:

```bash
mysql_secure_installation
```

## Install from Git

### Full Hub all in one command

```bash
cd /var/www/html && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullWeather.git && git clone https://github.com/sophiathekitty/NullLights.git && git clone https://github.com/sophiathekitty/NullSensors.git && git clone https://github.com/sophiathekitty/NullDisplay.git && git clone https://github.com/sophiathekitty/NullProfiles.git && cd ../extensions && git clone https://github.com/sophiathekitty/MealPlanner.git && sudo chmod 777 /var/www/html
```

### Micro Display all in one command

```bash
cd /var/www/html && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullWeather.git && git clone https://github.com/sophiathekitty/NullDisplay.git && sudo chmod 777 /var/www/html
```

### Micro Display with room temperature all in one command

```bash
cd /var/www/html && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullWeather.git && git clone https://github.com/sophiathekitty/NullSensors.git && git clone https://github.com/sophiathekitty/NullDisplay.git && sudo chmod 777 /var/www/html
```

### eInk Display all in one command

```bash
cd /var/www/html && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullWeather.git && git clone https://github.com/sophiathekitty/NullDisplay.git && cd ../extensions && git clone https://github.com/sophiathekitty/MealPlanner.git && sudo chmod 777 /var/www/html
```

### Thermostat all in one command

```bash
cd /var/www/html && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullSensors.git && sudo chmod 777 /var/www/html
```

### Individual commands for full install

simply skip the git clone commands for the plugins you don't need.

```bash
cd /var/www/html
```

```bash
git clone https://github.com/sophiathekitty/NullHub.git .
```

```bash
mkdir plugins
```

```bash
mkdir extensions
```

```bash
cd plugins
```

```bash
git clone https://github.com/sophiathekitty/NullWeather.git
```

```bash
git clone https://github.com/sophiathekitty/NullLights.git
```

```bash
git clone https://github.com/sophiathekitty/NullSensors.git
```

```bash
git clone https://github.com/sophiathekitty/NullDisplay.git
```

```bash
git clone https://github.com/sophiathekitty/NullProfiles.git
```

```bash
cd ../extensions
```

```bash
git clone https://github.com/sophiathekitty/MealPlanner.git
```

### All at once

```bash
cd www && git clone https://github.com/sophiathekitty/NullHub.git . && mkdir plugins && mkdir extensions && cd plugins && git clone https://github.com/sophiathekitty/NullWeather.git && git clone https://github.com/sophiathekitty/NullLights.git && git clone https://github.com/sophiathekitty/NullSensors.git && cd ../extensions && git clone https://github.com/sophiathekitty/MealPlanner.git && cd ~/
```

## Cron Jobs

```bash
sudo crontab -e
```

```Apache config
1 * * * * sh /var/www/html/gitpull.sh
2 * * * * sh /var/www/html/plugins/NullSensors/gitpull.sh
3 * * * * sh /var/www/html/plugins/NullWeather/gitpull.sh
4 * * * * sh /var/www/html/plugins/NullLights/gitpull.sh
5 * * * * sh /var/www/html/plugins/NullDisplay/gitpull.sh
6 * * * * sh /var/www/html/plugins/NullProfiles/gitpull.sh
7 * * * * sh /var/www/html/extensions/MealPlanner/gitpull.sh
8 * * * * wget -O/dev/null -q http://localhost/helpers/validate_models.php

* * * * * wget -O/dev/null -q http://localhost/services/every_minute.php
0 * * * * wget -O/dev/null -q http://localhost/services/every_hour.php
6 0 * * * wget -O/dev/null -q http://localhost/services/every_day.php
7 0 1 * * wget -O/dev/null -q http://localhost/services/every_month.php
8 0 1 1 * wget -O/dev/null -q http://localhost/services/every_year.php
9 0 * * 1 wget -O/dev/null -q http://localhost/services/every_week.php
```

## Plugins

* [NullWeather](https://github.com/sophiathekitty/NullWeather) - on main hub will pull live data. will sync from hub on other devices
* [NullSensors](https://github.com/sophiathekitty/NullSensors) - handles local sensors and syncing sensor to and from hub
* [NullLights](https://github.com/sophiathekitty/NullLights) - on main hub handles light automation. on devices sync from hub
* [NullDisplay](https://github.com/sophiathekitty/NullDisplay) - will replace the [micro_display](https://github.com/sophiathekitty/micro_display) project and add in some kiosk display options...
* [NullProfiles](https://github.com/sophiathekitty/NullProfiles) - adds profiles and custom icons for users. will let you customize your profile banner in the app and in the kiosk view.

## Extensions

* [MealPlanner](https://github.com/sophiathekitty/MealPlanner) - weekly meal schedule with some randomization

## Tools

* [favicon generator](https://www.favicon-generator.org/)
* [open source icons](https://game-icons.net/)
