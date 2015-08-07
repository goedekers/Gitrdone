# Gitrdone

## Installation instructions

With root privileges, save the following to */etc/apache2/sites-available/gitrdone.conf*.  Alter the ServerName to match your dev box.

```
<VirtualHost *:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        ServerName crusselldev.gitrdone.lan.goedekers.com

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/Gitrdone

        <Directory /var/www/Gitrdone/>
                AllowOverride All
        </Directory>

        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf

        ProxyPassMatch ^/(.+\.(hh|php)(/.*)?)$ fcgi://127.0.0.1:9000/var/www/Gitrdone/$1
</VirtualHost>
```

Run these commands:

```
cd /var/www
git clone https://github.com/goedekers/Gitrdone.git
a2ensite gitrdone
service apache2 reload
```

Now add the appropriate entry to your Windows hosts file (*c:\Window\System32\drivers\etc\hosts*), and save.  You should be able to visit the application in your browser by navigating to the URL that you entered in the first step.
