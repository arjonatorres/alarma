sudo dd if=/dev/mmcblk0 |pv|dd of=alarma07022018.img bs=1M

sudo dd if=alarma07022018.img of=/dev/mmcblk0 bs=1M
