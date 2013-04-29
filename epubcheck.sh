#!/bin/sh

JAVA="$(which java)"

#echo "Using Java executable at: $JAVA..."
#$JAVA -version

#echo "Running script..."
$JAVA -jar /scripts/_lib/epubcheck-3.0/epubcheck-3.0.jar "$1"

#echo "Done..."
exit
