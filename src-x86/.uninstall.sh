#!/bin/sh
QWEB_SHARE=`/sbin/getcfg SHARE_DEF defWeb -d Qweb -f /etc/config/def_share.info`

[ ! -d /share/${QWEB_SHARE}/pms ] || /bin/rm -rf /share/${QWEB_SHARE}/pms
/bin/rm -rf /usr/local/lib/codecs
/bin/rm -rf /usr/share/mplayer

