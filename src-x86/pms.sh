#!/bin/sh

# model compatibility
PUBLIC_SHARE=`/sbin/getcfg SHARE_DEF defPublic -d Public -f /etc/config/def_share.info`
QWEB_SHARE=`/sbin/getcfg SHARE_DEF defWeb -d Qweb -f /etc/config/def_share.info`

# globals
RETVAL=0
DATE=`date +%Y-%m-%d`
DIRNAME=`dirname $0`
QPKG_NAME=PMS
QPKG_DESC="PS3 UPnP Media Server"
QPKG_DIR=
JAVA_HOME=/usr/local/jre

_exit()
{
	/bin/echo -e "Error: $*"
	exit 1
}

# ensure correct app running status
if [ `/sbin/getcfg $QPKG_NAME Enable -u -d FALSE -f /etc/config/qpkg.conf` = UNKNOWN ]; then
	/sbin/setcfg $QPKG_NAME Enable TRUE -f /etc/config/qpkg.conf
elif [ `/sbin/getcfg $QPKG_NAME Enable -u -d FALSE -f /etc/config/qpkg.conf` != TRUE ]; then
	echo "${QPKG_NAME} is disabled."
fi

# Setup the JVM
if [ "x$JAVA" = "x" ]; then
	if [ "x$JAVA_HOME" != "x" ]; then
		JAVA="$JAVA_HOME/bin/java"
	else
		JAVA="java"
	fi
fi

check_jre()
{	
	JRE_INSTALL_PATH=`/sbin/getcfg JRE Install_Path -f /etc/config/qpkg.conf`
	JRE_SHELL=`/sbin/getcfg JRE Shell -f /etc/config/qpkg.conf`
	if [ "x${JRE_INSTALL_PATH}" = "x" ]; then
		QPKG_MSG="Failed to start ${QPKG_NAME}. In order to run ${QPKG_NAME}, you need to install JRE first."
		/sbin/write_log "$QPKG_MSG"
		exit 1
	fi
	if [ `/sbin/getcfg ${QPKG_NAME} Enable -u -d FALSE -f /etc/config/qpkg.conf` != TRUE ]; then
        /sbin/setcfg ${QPKG_NAME} Enable TRUE -f /etc/config/qpkg.conf
		[ "x${JRE_SHELL}" = x ] || ${JRE_SHELL} start 1>>/dev/null 2>>/dev/null
	fi
}

# Determine BASE installation location according to smb.conf
find_base(){
	BASE_GROUP="/share/HDA_DATA /share/HDB_DATA /share/HDC_DATA /share/HDD_DATA /share/HDE_DATA /share/HDF_DATA /share/HDG_DATA /share/HDH_DATA /share/MD0_DATA /share/MD1_DATA /share/MD2_DATA /share/MD3_DATA"
	QPKG_BASE=
	publicdir=`/sbin/getcfg $PUBLIC_SHARE path -f /etc/config/smb.conf`
	if [ ! -z $publicdir ] && [ -d $publicdir ];then
		publicdirp1=`/bin/echo $publicdir | /bin/cut -d "/" -f 2`
		publicdirp2=`/bin/echo $publicdir | /bin/cut -d "/" -f 3`
		publicdirp3=`/bin/echo $publicdir | /bin/cut -d "/" -f 4`
		if [ ! -z $publicdirp1 ] && [ ! -z $publicdirp2 ] && [ ! -z $publicdirp3 ]; then
			[ -d "/${publicdirp1}/${publicdirp2}/${PUBLIC_SHARE}" ] && QPKG_BASE="/${publicdirp1}/${publicdirp2}"
		fi
	fi

  # Determine BASE installation location by checking where the Public folder is.
	if [ -z $QPKG_BASE ]; then
		for datadirtest in $BASE_GROUP; do
			[ -d $datadirtest/$PUBLIC_SHARE ] && QPKG_BASE="/${publicdirp1}/${publicdirp2}"
		done
	fi
	if [ -z $QPKG_BASE ] ; then
		echo "The Public share not found."
		_exit 1
	fi
	QPKG_DIR=${QPKG_BASE}/.qpkg/${QPKG_NAME}
}

create_sym_links()
{
	[ -d ${QPKG_DIR}/javaps3media ] || /bin/mkdir -p ${QPKG_DIR}/javaps3media
	[ -d /tmp/javaps3media ] || /bin/ln -sf ${QPKG_DIR}/javaps3media /tmp/
	[ -d ${QPKG_DIR}/logs ] || /bin/mkdir ${QPKG_DIR}/logs
	[ -d "/var/log/pms" ] || /bin/ln -sf ${QPKG_DIR}/logs /var/log/pms
	[ -d "/usr/lib/codecs" ] || /bin/ln -sf "${QPKG_DIR}/codecs" /usr/lib
	[ -f "/share/${QWEB_SHARE}/pms/logs/pms_debug.log" ] || /bin/ln -sf ${QPKG_DIR}/debug.log /share/${QWEB_SHARE}/pms/logs/pms_debug.log
	[ -f "/share/${QWEB_SHARE}/pms/logs/${QPKG_LOG_FILE}" ] || /bin/ln -sf "${QPKG_LOG}" "/share/${QWEB_SHARE}/pms/logs/${QPKG_LOG_FILE}"
	[ -d "/etc/mplayer" ] || /bin/ln -sf "${QPKG_DIR}/etc/mplayer" /etc/mplayer
	
}

change_permission()
{
	chmod 766 "${QPKG_DIR}/PMS.conf"
	chmod 766 "${QPKG_DIR}/WEB.conf"
}

enable_font_support()
{
	[ -d "/root/.mplayer" ] || /bin/ln -sf ${QPKG_DIR}/.mplayer /root/
	[ -d "/etc/fonts" ] || /bin/ln -sf ${QPKG_DIR}/etc/fonts /etc/
	[ -d "/root/.fonts" ] ||  /bin/ln -sf ${QPKG_DIR}/.mplayer /root/.fonts
}

setup_web_frontend(){
	[ ! -d /share/${QWEB_SHARE}/pms ] || /bin/mkdir -p /share/${QWEB_SHARE}/pms
	/bin/cp -af ${QPKG_DIR}/web/. /share/${QWEB_SHARE}/pms/
	[ -f "/home/httpd/pms.cgi" ] || /bin/ln -sf /share/${QWEB_SHARE}/pms/pms.cgi /home/httpd/
}

find_base
QPKG_LOG=${QPKG_DIR}/logs/pms-$DATE.log
QPKG_LOG_FILE=pms-$DATE.log
create_sym_links
change_permission
enable_font_support
setup_web_frontend

PMS_HOME=$QPKG_DIR

# exports
export PATH=${PMS_HOME}:${PMS_HOME}/linux:$PATH
export LD_LIBRARY_PATH=${PMS_HOME}/lib
export PMS_HOME

# Setup the classpath
PMS_JARS=""$PMS_HOME"/update.jar:"$PMS_HOME"/pms.jar:"$PMS_HOME"/plugins/:"$PMS_HOME"/plugins/*"

# Use the following line to enable remote debugging of PMS
#JAVA_DEBUG="-agentlib:jdwp=transport=dt_socket,server=y,suspend=n,address=5002"
JAVA_DEBUG=""
JAVA_OPTS="-Xmx768M -Xss16M -Djava.awt.headless=true -Dfile.encoding=UTF-8 -Djava.net.preferIPv4Stack=true $JAVA_DEBUG"

PIDFILE="$PMS_HOME/pms.pid"

# Function that starts the daemon/service
do_start() {

   # Check if PIDFILE exists. If yes check if PMS is already running
   if [ -e $PIDFILE ]
   then
      PID=`cat $PIDFILE`
      TESTPID=`ps | grep $PID | grep -v grep`
      if [ x"$TESTPID" != "x" ] 
      then
         echo "$QPKG_NAME already running. Performing restart."
         do_stop
      fi
   fi
        
    # Test if the PMS logfile already exists
   if [ ! -e $QPKG_LOG ]
   then
      /bin/touch $QPKG_LOG
   fi
      
   DAEMON_OPTS="--start --verbose --chdir $PMS_HOME --background --make-pidfile --pidfile $PIDFILE"
   JCMD="exec $JAVA $JAVA_OPTS  -classpath $PMS_JARS net.pms.PMS $@ >>$QPKG_LOG"
   
      #$PMS_HOME/linux/start-stop-daemon $DAEMON_OPTS --exec "$JAVA" -- $JAVA_OPTS -classpath "$PMS_JARS" net.pms.PMS "$@"
       $PMS_HOME/linux/start-stop-daemon $DAEMON_OPTS --exec "$JAVA" --startas /bin/sh -- -c "$JCMD"
   sleep 3

   # Test if PMS is actually running
   if [ -e $PIDFILE ]
   then
      PID=`cat $PIDFILE`
      TESTPID=`ps | grep $PID | grep -v grep`
      if [ x"$TESTPID" != "x" ] 
      then
         echo "pid: [$PID]"
      else
         echo "$QPKG_NAME failed to start"
      fi
   else
      echo "$QPKG_NAME failed to start"
   fi
}

# Function that stops the daemon/service
do_stop() {
   DAEMON_OPTS="--stop --verbose --pidfile $PIDFILE --retry 5"
   $PMS_HOME/linux/start-stop-daemon $DAEMON_OPTS
   rm $PIDFILE
}

# Function that restarts the daemon/service
do_restart() {
   do_stop
   do_start
	create_sym_links
}

case "$1" in
   start)
      echo "Starting $QPKG_DESC" "$QPKG_NAME"
	  check_jre
      do_start
      ;;
   stop)
      echo "Stopping $QPKG_DESC" "$QPKG_NAME"
      do_stop
      ;;
   restart)
      echo "Restarting $QPKG_DESC" "$QPKG_NAME"
      do_restart
      ;;
   truncate_log)
	/etc/init.d/pms.sh restart
	create_sym_links
      ;;
   *)
      echo "Usage: $0 {start|stop|restart}" >&2
      exit 1
      ;;
esac

exit $RETVAL
