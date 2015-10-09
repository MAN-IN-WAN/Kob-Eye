Defaults	env_reset
Defaults	mail_badpass
Defaults	secure_path="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

root	ALL=(ALL:ALL) ALL
formation	ALL=NOPASSWD: /sbin/reboot, /sbin/halt, /bin/cp, /usr/sbin/service, /sbin/iwconfig, /sbin/iwlist

# Allow members of group sudo to execute any command
%sudo	ALL=(ALL:ALL) ALL

# See sudoers(5) for more information on "#include" directives:

#includedir /etc/sudoers.d
pi ALL=(ALL) NOPASSWD: ALL