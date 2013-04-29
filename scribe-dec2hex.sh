#!/bin/sh

BIN_PHP="$(which php)"

#echo "Using PHP executable at: $BIN_PHP..."
#$BIN_PHP -v

#echo "Running script..."
$BIN_PHP /scripts/_lib/dec2hex

#echo "Done..."
exit
