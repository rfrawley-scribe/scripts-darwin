#!/bin/sh

ECHO=$(which echo)
KINDLEGEN=$(which kindlegen)

$ECHO "Building mobi..."
$KINDLEGEN "content.opf"

$ECHO "Finished..."
