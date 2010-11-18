#!/bin/sh
#================================================================
# Copyright (C) 2010 QNAP Systems, Inc.
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#----------------------------------------------------------------
#
# install.sh
#
#	Abstract: 
#		A QPKG installation script for
#		PS3 Media Server v1.20.412
#
#	HISTORY:
#		2008/03/31	-	Created	- KenChen
#		2009/01/16	-	Revised for xDove	- AndyChuo
#		2010/07/28	-	Modified for PMS- KenChen
# 
#================================================================

##### Util #####
CMD_ADDUSER="/bin/adduser"
CMD_CHMOD="/bin/chmod"
CMD_CHOWN="/bin/chown"
CMD_CHROOT="/usr/sbin/chroot"
CMD_CP="/bin/cp"
CMD_CUT="/bin/cut"
CMD_ECHO="/bin/echo"
CMD_GETCFG="/sbin/getcfg"
CMD_GREP="/bin/grep"
CMD_IFCONFIG="/sbin/ifconfig"
CMD_LN="/bin/ln"
CMD_LS="/bin/ls"
CMD_MKDIR="/bin/mkdir"
CMD_MV="/bin/mv"
CMD_READLINK="/usr/bin/readlink"
CMD_RM="/bin/rm"
CMD_SED="/bin/sed"
CMD_SETCFG="/sbin/setcfg"
CMD_SLEEP="/bin/sleep"
CMD_SYNC="/bin/sync"
CMD_TAR="/bin/tar"
CMD_TOUCH="/bin/touch"
CMD_WLOG="/sbin/write_log"
CMD_IPKG="/opt/bin/ipkg"
##### System #####
UPDATE_PROCESS="/tmp/update_process"
UPDATE_PB=0
UPDATE_P1=1
UPDATE_P2=2
UPDATE_PE=3
SYS_HOSTNAME=`/bin/hostname`
SYS_IP=`$CMD_IFCONFIG eth0 | $CMD_GREP "inet addr" | $CMD_CUT -f 2 -d ':' | $CMD_CUT -f 1 -d ' '`
#SYS_IP=`$CMD_GREP "${SYS_HOSTNAME}" /etc/hosts | $CMD_CUT -f 1`
SYS_CONFIG_DIR="/etc/config" #put the configuration files here
SYS_INIT_DIR="/etc/init.d"
SYS_rcS_DIR="/etc/rcS.d/"
SYS_rcK_DIR="/etc/rcK.d/"
SYS_QPKG_CONFIG_FILE="/etc/config/qpkg.conf" #qpkg infomation file
SYS_QPKG_CONF_FIELD_QPKGFILE="QPKG_File"
SYS_QPKG_CONF_FIELD_NAME="Name"
SYS_QPKG_CONF_FIELD_VERSION="Version"
SYS_QPKG_CONF_FIELD_ENABLE="Enable"
SYS_QPKG_CONF_FIELD_DATE="Date"
SYS_QPKG_CONF_FIELD_SHELL="Shell"
SYS_QPKG_CONF_FIELD_INSTALL_PATH="Install_Path"
SYS_QPKG_CONF_FIELD_CONFIG_PATH="Config_Path"
SYS_QPKG_CONF_FIELD_WEBUI="WebUI"
SYS_QPKG_CONF_FIELD_WEBPORT="Web_Port"
SYS_QPKG_CONF_FIELD_SERVICEPORT="Service_Port"
SYS_QPKG_CONF_FIELD_SERVICE_PIDFILE="Pid_File"
SYS_QPKG_CONF_FIELD_AUTHOR="Author"

#
##### QPKG Info #####
##################################
# please fill up the following items
##################################

. qpkg.cfg

#####	Func ######
##################################
# custum exit
##################################
_exit(){
	local ret=0
	
	case $1 in
		0)#normal exit
			ret=0
			if [ "x$QPKG_INSTALL_MSG" != "x" ]; then
				$CMD_WLOG "${QPKG_INSTALL_MSG}" 4
			else
				$CMD_WLOG "${QPKG_NAME} ${QPKG_VER} installation succeeded." 4
			fi
			$CMD_ECHO "$UPDATE_PE" > ${UPDATE_PROCESS}
		;;
		*)
			ret=1
			if [ "x$QPKG_INSTALL_MSG" != "x" ];then
				$CMD_WLOG "${QPKG_INSTALL_MSG}" 1
			else
				$CMD_WLOG "${QPKG_NAME} ${QPKG_VER} installation failed" 1
			fi
			$CMD_ECHO -1 > ${UPDATE_PROCESS}
		;;
	esac	
	exit $ret
}

##################################
# Determine BASE installation location 
##################################

find_base(){
	# Determine BASE installation location according to smb.conf
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
		for datadirtest in $VOLUME_BASE_GROUP; do
			[ -d $datadirtest/$PUBLIC_SHARE ] && QPKG_BASE="/${publicdirp1}/${publicdirp2}"
		done
	fi
	if [ -z $QPKG_BASE ] ; then
		echo "The Public share not found."
		_exit 1
	fi
	QPKG_INSTALL_PATH="${QPKG_BASE}/.qpkg"
	QPKG_DIR="${QPKG_INSTALL_PATH}/${QPKG_NAME}"
}

##################################
# Check NAS Platform
##################################
check_platform()
{
	if [ "x$PLATFORM" != "x$REQ_PLATFORM" ]; then
		QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} installation failed. Not for this Model."
		_exit 1
	fi
}

##################################
# Check NAS model to ensure QPKG target version
##################################
check_model()
{
	local OK=
	for model in $ALL_X86_BASED_X39_MODELS; do  [ "${REAL_MODEL}" =  "${model}" ] && OK=1;  done
	if [ -z $OK ]; then 
		QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} installation failed. Model mismatch."
		${CMD_ECHO} "$QPKG_INSTALL_MSG"
		_exit 1
	fi
}

##################################
# Check dependency
##################################
check_prerequisite()
{
        # jre dependency checking
        JRE_INSTALL_PATH=`${CMD_GETCFG} JRE Install_Path -f ${SYS_QPKG_CONFIG_FILE}`
        if [ "${JRE_INSTALL_PATH}" = "" ]; then
                QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} installation failed. In order to run PMS you will need to install JRE first."
                $CMD_ECHO "$QPKG_INSTALL_MSG"
                _exit 1
        fi
}
#
##################################
# Link service start/stop script
##################################
#
link_start_stop_script(){
	if [ "x${QPKG_SERVICE_PROGRAM}" != "x" ]; then
		$CMD_ECHO "Link service start/stop script: ${QPKG_SERVICE_PROGRAM}"
		$CMD_LN -sf "${QPKG_DIR}/${QPKG_SERVICE_PROGRAM}" "${SYS_INIT_DIR}/${QPKG_SERVICE_PROGRAM}"
		$CMD_LN -sf "${SYS_INIT_DIR}/${QPKG_SERVICE_PROGRAM}" "${SYS_rcS_DIR}/QS${QPKG_RC_NUM}${QPKG_NAME}"
		$CMD_LN -sf "${SYS_INIT_DIR}/${QPKG_SERVICE_PROGRAM}" "${SYS_rcK_DIR}/QK${QPKG_RC_NUM}${QPKG_NAME}"
		$CMD_CHMOD 755 "${QPKG_DIR}/${QPKG_SERVICE_PROGRAM}"
	fi

	# Only applied on TS-109/209/409
	#if [ "x${QPKG_SERVICE_PROGRAM_CHROOT}" != "x" ] ]; then
	#	$CMD_MV ${QPKG_DIR}/${QPKG_SERVICE_PROGRAM_CHROOT} ${QPKG_ROOTFS}/etc/init.d
	#	$CMD_CHMOD 755 ${QPKG_ROOTFS}/etc/init.d/${QPKG_SERVICE_PROGRAM_CHROOT}
	#fi
}
#
##################################
# Set QPKG information
##################################
#
register_qpkg(){

	$CMD_ECHO "Set QPKG information to $SYS_QPKG_CONFIG_FILE"
	[ -f ${SYS_QPKG_CONFIG_FILE} ] || $CMD_TOUCH ${SYS_QPKG_CONFIG_FILE}
	$CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_NAME} "${QPKG_NAME}" -f ${SYS_QPKG_CONFIG_FILE}
	$CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_VERSION} "${QPKG_VER}" -f ${SYS_QPKG_CONFIG_FILE}
		
	#default value to activate(or not) your QPKG if it was a service/daemon
	$CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_ENABLE} "UNKNOWN" -f ${SYS_QPKG_CONFIG_FILE}

	#set the qpkg file name
	[ "x${SYS_QPKG_CONF_FIELD_QPKGFILE}" = "x" ] && $CMD_ECHO "Warning: ${SYS_QPKG_CONF_FIELD_QPKGFILE} is not specified!!"
	[ "x${SYS_QPKG_CONF_FIELD_QPKGFILE}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_QPKGFILE} "${QPKG_QPKG_FILE}" -f ${SYS_QPKG_CONFIG_FILE}
	
	#set the date of installation
	$CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_DATE} `date +%F` -f ${SYS_QPKG_CONFIG_FILE}
	
	#set the path of start/stop shell script
	[ "x${QPKG_SERVICE_PROGRAM}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_SHELL} "${QPKG_DIR}/${QPKG_SERVICE_PROGRAM}" -f ${SYS_QPKG_CONFIG_FILE}
	
	#set path where the QPKG installed, should be a directory
	$CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_INSTALL_PATH} "${QPKG_DIR}" -f ${SYS_QPKG_CONFIG_FILE}

	#set path where the QPKG configure directory/file is
	[ "x${QPKG_CONFIG_PATH}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_CONFIG_PATH} "${QPKG_CONFIG_PATH}" -f ${SYS_QPKG_CONFIG_FILE}
	
	#set the port number if your QPKG was a service/daemon and needed a port to run.
	[ "x${QPKG_SERVICE_PORT}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_SERVICEPORT} "${QPKG_SERVICE_PORT}" -f ${SYS_QPKG_CONFIG_FILE}

	#set the port number if your QPKG was a service/daemon and needed a port to run.
	[ "x${QPKG_WEB_PORT}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_WEBPORT} "${QPKG_WEB_PORT}" -f ${SYS_QPKG_CONFIG_FILE}

	#set the URL of your QPKG Web UI if existed.
	[ "x${QPKG_WEBUI}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_WEBUI} "${QPKG_WEBUI}" -f ${SYS_QPKG_CONFIG_FILE}

	#set the pid file path if your QPKG was a service/daemon and automatically created a pidfile while running.
	[ "x${QPKG_SERVICE_PIDFILE}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_SERVICE_PIDFILE} "${QPKG_SERVICE_PIDFILE}" -f ${SYS_QPKG_CONFIG_FILE}

	#Sign up
	[ "x${QPKG_AUTHOR}" = "x" ] && $CMD_ECHO "Warning: ${SYS_QPKG_CONF_FIELD_AUTHOR} is not specified!!"
	[ "x${QPKG_AUTHOR}" = "x" ] || $CMD_SETCFG ${QPKG_NAME} ${SYS_QPKG_CONF_FIELD_AUTHOR} "${QPKG_AUTHOR}" -f ${SYS_QPKG_CONFIG_FILE}		
}
#
##################################
# Check existing installation
##################################
#
check_existing_install(){
	CURRENT_QPKG_VER="`/sbin/getcfg ${QPKG_NAME} Version -f /etc/config/qpkg.conf`"
	QPKG_INSTALL_MSG="${QPKG_NAME} ${CURRENT_QPKG_VER} is already installed. Setup will now perform package upgrading."
	$CMD_ECHO "$QPKG_INSTALL_MSG"			
}
#
##################################
# Copy QPKG icons
##################################
#
copy_qpkg_icons()
{
        ${CMD_RM} -rf /home/httpd/RSS/images/${QPKG_NAME}.gif; ${CMD_CP} -af ${QPKG_DIR}/.qpkg_icon.gif /home/httpd/RSS/images/${QPKG_NAME}.gif
        ${CMD_RM} -rf /home/httpd/RSS/images/${QPKG_NAME}_80.gif; ${CMD_CP} -af ${QPKG_DIR}/.qpkg_icon_80.gif /home/httpd/RSS/images/${QPKG_NAME}_80.gif
        ${CMD_RM} -rf /home/httpd/RSS/images/${QPKG_NAME}_gray.gif; ${CMD_CP} -af ${QPKG_DIR}/.qpkg_icon_gray.gif /home/httpd/RSS/images/${QPKG_NAME}_gray.gif
}
#
##################################
# Custom functions
##################################

##################################
# Pre-install routine
##################################
#
pre_install(){
	${CMD_ECHO} "> pre_install"
	#check_model
	check_platform
	check_prerequisite
	# look for the base dir to install
	find_base
}
#
##################################
# Post-install routine
##################################
#
post_install(){
	${CMD_ECHO} "> post_install"
	
	$CMD_SED -i 's/XXXX/\/share\/'$MULTIMEDIA_SHARE'/g' "${QPKG_DIR}/PMS.conf"
}
#
##################################
# Pre-update routine
##################################
#
pre_update()
{
	${CMD_ECHO} "> pre_update"
	${CMD_ECHO} "PMS.conf" >> /tmp/exclude
	${CMD_ECHO} "WEB.conf" >> /tmp/exclude
}
#
##################################
# Update routines
##################################
#
update_routines()
{
	${CMD_ECHO} "> update_routines"
	# add your own routines below
	#$CMD_TAR xzf "${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE}"  --exclude=PMS.conf -C ${QPKG_DIR}
	[ -f /tmp/exclude ]  && $CMD_TAR xfX "${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE}" /tmp/exclude -C ${QPKG_DIR}
	
	if [ $? != 0 ]; then
		# add your own routines below
		#create_req_symlinks
		#set_req_permissions
	#else
		return 2
	fi
}
#
##################################
# Post-update routine
##################################
#
post_update()
{
	${CMD_ECHO} "> post_update"
	# Reformat uebimiau config.php before reimport
	${CMD_RM} -rf /tmp/exclude
}
#
##################################
# Install routines
##################################
install_routines()
{
	${CMD_ECHO} "> install_routines"
	# decompress the QNAP file (do not remove, required routine)
	$CMD_TAR xzf "${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE}" -C ${QPKG_DIR}
	
	if [ $? != 0 ]; then
		# add your own routines below
		#create_req_symlinks
		#set_req_permissions
	#else
		return 2
	fi
}
#
##################################
# Main installation
##################################
#
install()
{
	# pre install routines (do not remove, required routine)
	pre_install
	
	if [ -f "${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE}" ]; then
		
		# check for existing install
		if [ -d ${QPKG_DIR} ]; then
			check_existing_install
			UPDATE_FLAG=1
			
			# pre update routines (do not remove, required routine)
			pre_update
		else
			# create main QNAP installation folder
			$CMD_MKDIR -p ${QPKG_DIR}
		fi

		# install/update QPKG files 		
		if [ ${UPDATE_FLAG} -eq 1 ]; then
			# update routines (do not remove, required routine)
			update_routines 
			
			# post update routines (do not remove, required routine)
			post_update
		else
			# installation routines
			install_routines
			
			# post install routines (do not remove, required routine)
			post_install
		fi
		
		# install progress indicator (do not remove, required routine)
		$CMD_ECHO "$UPDATE_P2" > ${UPDATE_PROCESS}
		
		# create rcS/rcK start/stop scripts 
		copy_qpkg_icons	
		link_start_stop_script	#(Do not remove, required routine)
		register_qpkg 					#(Do not remove, required routine)
			
		$CMD_SYNC
		return 0
	else
		return 1		
	fi
}

#
##################################
# Main
##################################
#
# install progress indicator
$CMD_ECHO "$UPDATE_PB" > ${UPDATE_PROCESS}

install
if [ $? = 0 ]; then
	QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} has been installed in $QPKG_DIR."
	$CMD_ECHO "$QPKG_INSTALL_MSG"
	#Enable ans Start this addon
	if [ "x${QPKG_SERVICE_PROGRAM}" != "x" ]; then
		$CMD_ECHO "${SYS_INIT_DIR}/${QPKG_SERVICE_PROGRAM} start" 
		${SYS_INIT_DIR}/${QPKG_SERVICE_PROGRAM} start 1>>/dev/null 2>>/dev/null
	fi
	_exit 0
elif [ $? = 1 ]; then
	QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} installation failed. ${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE} file not found."
	$CMD_ECHO "$QPKG_INSTALL_MSG"
	_exit 1
elif [ $? = 2 ]; then
	${CMD_RM} -rf ${QPKG_INSTALL_PATH}
	QPKG_INSTALL_MSG="${QPKG_NAME} ${QPKG_VER} installation failed. ${QPKG_SOURCE_DIR}/${QPKG_SOURCE_FILE} file error."
	$CMD_ECHO "$QPKG_INSTALL_MSG"
	_exit 1
else
	# never reach here
	echo ""
fi
