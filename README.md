# YT-downloader-page
An easy and straightforward Youtube downloader page

1st make sure yt-dlp and python3/pip3 are installed:

  sudo apt install python3-pip
  sudo pip3 install yt-dlp

I've designed it to work with lighttpd and tested it on Debian based distributions.

To install the page and start downloading videos you have to install the following packages:

  sudo apt install lighttpd
  sudo apt install php php-cgi libapache2-mod-php8.1- (to avoid installing Apache)
  sudo lighty-enable-mod fastcgi
  sudo lighty-enable-mod fastcgi-php
  sudo service lighttpd force-reload

Put the file index.php in /var/www/html/

Create the folder /var/www/html/tempDownloads and change its rights to 777
  chmod 777 -R /var/www/html/tempDownloads

Your Easy Web based graphical YT Downloader is now ready to go!
Don't forget to update your yt-dlp with the following command:

 sudo pip3 install yt-dlp
