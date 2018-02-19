#!/bin/bash
sudo find /tmp/motion -iname '*.jpg*' -type f -ctime +2 -delete
exit
