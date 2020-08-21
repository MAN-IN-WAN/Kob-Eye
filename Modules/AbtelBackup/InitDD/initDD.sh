#!/bin/bash 


sgdisk -n 1:0:0 -t 1:8e00 /dev/sdb
pvcreate /dev/sdb1
vgreduce /dev/centos --remove-missing --force
vgextend /dev/centos /dev/sdb1
lvcreate -l 100%FREE -n lv_backup /dev/centos
mkfs.xfs /dev/centos/lv_backup
echo "/dev/mapper/centos-lv_backup /backup xfs defaults 0 0" | tee -a "/etc/fstab" >/dev/null
mount -a
chown backup. /backup