#!/bin/bash

OLOGPATH=/opt/opheme20/logs
OLOGLOAD=$OLOGPATH/load.log
OLOGMEM=$OLOGPATH/memory.log
OLOGCPU=$OLOGPATH/cpu.count

if [ ! -e "$OLOGLOAD" ]; then
	touch $OLOGLOAD
	chown www-data: $OLOGLOAD
fi
if [ ! -e "$OLOGMEM" ]; then
	touch $OLOGMEM
	chown www-data: $OLOGMEM
fi
if [ ! -e "$OLOGCPU" ]; then
	touch $OLOGCPU
	nproc > $OLOGCPU
	chown www-data: $OLOGCPU
fi

top -b -n 1 > $OLOGPATH/overall.log
cat $OLOGPATH/overall.log | grep load | grep average | echo `date +%s` `awk '{ for (i=(NF-2); i<=NF; i++) print $i }'` | tr ',' ' ' >> $OLOGLOAD
cat $OLOGPATH/overall.log | grep KiB | grep Mem | echo `date +%s` `awk '{ print $3, $5, $7 }'` >> $OLOGMEM