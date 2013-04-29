#!/bin/sh

SHCOMMON="/storage/_lib/sh-common"

#if [ -e "$SHCOMMON" ]; then
#	. "$SHCOMMON"
#else
#	echo "Could not find sh-common file...exiting."
#	exit -1
#fi

BIN_PHP="$(which php)"

#echo "Using PHP executable at: $BIN_PHP..."
#$BIN_PHP -v

#echo "Running script..."
$BIN_PHP /scripts/_lib/hex2dec

#echo "Done..."
exit
