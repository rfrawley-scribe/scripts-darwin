#!/bin/sh

BIN_PHP="$(which php)"

#echo "Using PHP executable at: $BIN_PHP..."
#$BIN_PHP -v

#echo "Running script..."
$BIN_PHP /scripts/_lib/urlcheck

#echo "Done..."
exit
