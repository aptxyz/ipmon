## IPMON

IPMON monitors your router's bandwidth.

![](https://github.com/aptxyz/ipmon/blob/master/snapshot.png)

## Installation

1. Make sure your server has mySQL and php
2. Make sure your router has jffs enable
3. Put `index.php` and `index.htm` on your server
4. Put `ipmon.wanup` on your router of `/jffs/etc/config/`
5. Change your variables
 * $routerIP on `index.php`
 * ipmon.resetD on `index.htm`
 * serverIP, serverPath on `ipmon.wanup`
6. Run `./ipmon.wanup server-setup` on your server, enter your mySQL root password
7. Run `./ipmon.wanup &` on your router
8. Open your browser

![](https://github.com/aptxyz/ipmon/blob/master/snapshot2.png)

## Version

* v0.02 (2014-12-02) - fixed something
* v0.01 (2014-08-11) - initial commit

## Credit

- Based on work from Fredrik Erlandsson (erlis AT linux.nu)
- Based on traff_graph script by twist - http://wiki.openwrt.org/RrdTrafficWatch
- Based on wrtbwmon by Emmanuel Brucy (e.brucy AT qut.edu.au)i - https://code.google.com/p/wrtbwmon/
- Based on lal-projects by Ajudica...@gmail.com - https://code.google.com/p/lal-projects/
- Used the libraries
 * Highcharts JS by Highsoft AS - http://www.highcharts.com/
 * Datejs by Coolite Inc.- http://www.datejs.com/
 * jQuery by jQuery Foundation- http://jquery.com/



