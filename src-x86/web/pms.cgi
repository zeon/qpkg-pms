#!/bin/sh
#--------------------------------------------
#  xdove.cgi
#
#	Abstract: 
#   A CGI program for XDove Configuration
#   Change XMail server.tab configuration 
#
#	HISTORY:
#       2008/12/12 -    Written by Laurent (Ad'Novea)
#		2008/08/20 -	idea from Y.C. Ken Chen
#
#--------------------------------------------

# Retreive command line parameters
	paramVar1=`echo $QUERY_STRING | cut -d \& -f 1 | cut -d \= -f 1`
	paramVal1=`echo $QUERY_STRING | cut -d \& -f 1 | cut -d \= -f 2`
	paramVar2=`echo $QUERY_STRING | cut -d \& -f 2 | cut -d \= -f 1`
	paramVal2=`echo $QUERY_STRING | cut -d \& -f 2 | cut -d \= -f 2`
	paramVar3=`echo $QUERY_STRING | cut -d \& -f 3 | cut -d \= -f 1`
	paramVal3=`echo $QUERY_STRING | cut -d \& -f 3 | cut -d \= -f 2`
	paramVar4=`echo $QUERY_STRING | cut -d \& -f 4 | cut -d \= -f 1`
	paramVal4=`echo $QUERY_STRING | cut -d \& -f 4 | cut -d \= -f 2`

	SYS_MODEL=`/sbin/getcfg system model`;

# Determine Platform type
	CPU_MODEL=`uname -m`
	KERNEL=`uname -mr | cut -d '-'  -f 1 | cut -d ' '  -f 1`
#	if [ "${KERNEL}" == "2.6.12.6" ] ; then CHROOTED=1; else CHROOTED=0; fi

# Debugging
	echo -e "content-type: text/html\n"
	echo -e "\n`date`"
	echo -e "\nCPU=${CPU_MODEL} / KERNEL=${KERNEL}"
	echo -e "\nSCRIPT: pms.cgi param1[${paramVar1}=${paramVal1}] param2[${paramVar2}=${paramVal2}] param3[${paramVar3}=${paramVal3}] param4[${paramVar4}=${paramVal4}]"

	
	case $paramVar1 in
		# Start/Stop PMS
		pms)
			echo -e "PMS: ${paramVal1}"
			/etc/init.d/pms.sh $paramVal1
			;;

		# Truncating PMS logs
		truncate)
			echo -e "PMS: Truncating logs = ${paramVal1}"
			if [ $paramVal1 = "1" ]; then
				/etc/init.d/pms.sh truncate_log
			fi
			;;

		# Invalid command line parameters
		*)
			echo -e "ERROR: wrong params"
			;;
	esac

	echo -e "\nEND OF SCRIPT: pms.cgi"
exit $?

