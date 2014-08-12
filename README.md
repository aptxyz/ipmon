IPMON
=====

IPMON monitors your router's bandwidth.

![](https://github.com/aptxyz/ipmon/blob/master/snapshot.png)

Installation
=====

1. Make sure your server has mySQL and php
2. Make sure your router has jffs enable.
3. Put index.php and index.htm on your server.
4. Put index.startup on your router of /jffs/etc/config/
5. Change your variables
 * $routerIP, $resetD on index.php
 * ipmon.resetD on index.htm
 * serverIP, serverPath on ipmon.startup
6. Run `./ipmon.startup server-setup` on your server, enter your mySQL root password
7. Run `./ipmon.startup router-setup` on your router
8. Reboot your router or run `./ipmon.startup start &` on your router
9. Open your browser

![](https://github.com/aptxyz/ipmon/blob/master/snapshot2.png)

Version
=====

0.01

Credit
=====

- Based on work from Fredrik Erlandsson (erlis AT linux.nu)
- Based on traff_graph script by twist - http://wiki.openwrt.org/RrdTrafficWatch
- Based on wrtbwmon by Emmanuel Brucy (e.brucy AT qut.edu.au)i - https://code.google.com/p/wrtbwmon/
- Based on lal-projects by Ajudica...@gmail.com - https://code.google.com/p/lal-projects/