

<p align="center"
    <a href="https://www.xgproyect.org/" target="_blank">
        <img align="center" img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-black.png" width="250px" title="XG Proyect" alt="xgp-logo">
    </a>
    <br>
    <strong>X</strong>treme <strong>G</strong>amez <strong>Proyect</strong>o
    <br>
    <strong>Open-source OGame Clon</strong>
</p> 

About
====

XG Proyect (XGP) is a web browser game based on the famous OGame. Our goal is to offer a package that is as similar as possible to the original, but keeping their original design.

Official Website: https://www.xgproyect.org/  
Live Server: https://www.xgproyect.net/  

## Requirements

PHP 7.4 or greater  
MySQLi 5.7 or greater  

## How to get XG Proyect?

### Manually
This is the simplest and easiest way if you're not a technical person. Download and install XG Proyect will be easy! ;)

- Go to the releases section and get the latest stable release, and simply download it. It is the file `.zip` then you can do whatever you want with that package, use it on your local stack, hosting or even docker!

### Composer
Choose this option if you want to contribute to the project.

Composer which will get you everything, including docker and would install the test dependencies like PHPUnit.

```
composer create-project xgproyect/xgproyect
```

## How to run XG Proyect?
### Docker
Easiest way to do it, is using Docker.

```
docker-compose up
```

You can also build with different PHP versions:
```
docker build -t xgproyect:7.4 --build-arg PHP_VERSION=7.4 .
```

Or build and run, altogether, specifying a **PHP version**:
```
docker-compose build --build-arg PHP_VERSION=8.2 && docker-compose up -d
```

Simple change the **PHP version** to any other **version** that you'd like to test.

### Other ways
- Other options are also possible like XAMPP, or using it on your own hosting.

## MailHog
XGP uses MailHog and PHPMailer as tools for better mailing support. MailHog allows you to intercept emails **locally** and receive them under a convenient panel.

Read our <a href="https://github.com/XGProyect/XG-Proyect-v3.x.x/wiki/MailHog-usage-and-setup" target="_blank">MailHog guide</a> to get started.

## Who is using XG Proyect?
We are happy to deliver this software giving others the possibility to have a good OGame Clon.  
On the other hand, it's a pleasure to see people using XG Proyect.  
<a href="https://github.com/XGProyect/XG-Proyect-v3.x.x/issues" target="_blank">Create a ticket</a> on GitHub so I can put your game logo here!  

<img align="center" img src="https://xgproyect.org/wp-content/uploads/2019/10/xgp-new-logo-black.png" width="150px" title="XG Proyect" alt="xgp-logo">

## We support
The following are tools or frameworks that we use to do our coding experience better!

<p>
    <a href="https://codeigniter.com/" rel="nofollow">
        <img src="https://codeigniter.com/favicon.ico" alt="CodeIgniter" width="75px">
    </a>
    <a href="https://getcomposer.org/" rel="nofollow">
        <img src="https://getcomposer.org/img/logo-composer-transparent2.png" alt="Composer" width="75px">
    </a>
    <a href="https://www.phpdoc.org/" rel="nofollow">
        <img src="https://avatars0.githubusercontent.com/u/1239567?s=400&v=4" alt="PHPDocumentor" width="75px">
    </a>
    <a href="https://github.com/llaville/php-compat-info" rel="nofollow">
        <img src="https://avatars2.githubusercontent.com/u/364342?s=460&v=4" alt="PHP CompatInfo" width="75px">
    </a>
</p>

## License
The XG Proyect is open-sourced software licensed under the GPL-3.0 License.
