#!/bin/bash

S_MODULE_PATH=${S_MODULE_PATH:-`readlink -f ..`}
export SMCHECKINTERACTION_CWD="${S_MODULE_PATH}/SMCHECKINTERACTION"

if [ -f "${SMCHECKINTERACTION_CWD}/etc/SMCHECKINTERACTION.conf" ]; then
   source "${SMCHECKINTERACTION_CWD}/etc/SMCHECKINTERACTION.conf"
else
    echo "`date +%H:%M:%S`: $0(pid $$): File ${SMCHECKINTERACTION_CWD}/etc/SMCHECKINTERACTION.conf not found"
fi

function SMCHECKINTERACTION_agent() {
	
  if [[ "$#" > "0" ]]; then
	echo "SMCHECKINTERACTION_agent: Current Job: ${@}"
    #send the job string directly to php
    php ${SMCHECKINTERACTION_CWD}/apiRequest.php "${@}" "${S_OPHEME_DIR}"
  else
    echo "ERROR: invalid number of paramaters \"$#\": \"$@\""
  fi

}