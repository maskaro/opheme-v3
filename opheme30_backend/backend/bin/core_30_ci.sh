#!/bin/bash


if [ -f ../etc/settings.conf ]
then
	. ../etc/settings.conf
else
	echo "No settings found. Exit 1."
	exit 1
fi

if [ -f ../lib/libcore.sh ]
then
	. ../lib/libcore.sh
else
  echo "No core library found. Exit 1."
  exit 1
fi

trap childProcessDied SIGCHLD # Linux system may ignore this trap
trap cleanUp SIGKILL SIGHUP SIGQUIT SIGINT SIGTERM

#makeDirectory ${S_DIR_LASTID}
makeFifo
#removeLocks ${S_CONSUMERSDIR}
removeLocks ${S_AGENTSDIR}
spawnChildrenOfType agent
#spawnChildrenOfType consumer

#RUN_EMAILS=1

while true
do
	
	#run PHP to send notification emails only at midnight, once
	#H=$(date +%H)
	#if (( 0 == 10#$H && $RUN_EMAILS == 1 )); then
		php ${S_PHP_BACKEND_EMAIL_NOTIFICATIONS} "${S_OPHEME_DIR}"
		#RUN_EMAILS=0
    #fi
	#if (( 1 <= 10#$H )); then
		#RUN_EMAILS=1
	#fi

	#run PHP to populate ${S_FILE_JOBS}
	php ${S_PHP_BACKEND_GENERATE} "${S_OPHEME_DIR}" "${S_FILE_JOBS}"

	JOB_INDEX=1
	cat ${S_FILE_JOBS} | while read line
	do
		if [ "x${line}" != "x" -a "${line:0:1}" != "#" ]; then                    # Ensure no empty lines are processed
			AGENTCHANNEL=`freeChannelId agent`            #echo "`date +%H:%M:%S`: $0(pid $$): Find free agent channel"
			signalToChannel "${S_AGENTSDIR}/${S_AGENTSSIGNAL}" ${AGENTCHANNEL} #echo "`date +%H:%M:%S`: $0(pid $$): Signalling Agent via ${S_AGENTSDIR}/${S_AGENTSSIGNAL} -> ${S_AGENTSDIR}/${AGENTCHANNEL}"
			lockChannel "${S_AGENTSDIR}/${AGENTCHANNEL}"  #echo "`date +%H:%M:%S`: $0(pid $$): Locking Agent data channel -> ${S_AGENTSDIR}/${AGENTCHANNEL}"
			echo "$line" > ${S_AGENTSDIR}/${AGENTCHANNEL} #1->job #echo "`date +%H:%M:%S`: $0(pid $$): Delivering job to agent at -> ${S_AGENTSDIR}/${AGENTCHANNEL}"
			(( JOB_INDEX++ ))
		fi
	done

	statusReport

	sleep "${S_RUNINTERVAL}"

	. ../etc/settings.conf

done
