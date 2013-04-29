#!/bin/sh

TMPSAVE=".project"

ECHO=$(which echo)
PRINTF=$(which printf)
RM=$(which rm)
ZIP=$(which zip)
EPUBCHECK=$(which epubcheck)
FIND=$(which find)
GREP=$(which grep)

if [ -f "$TMPSAVE" ]; then
	projectName=$(cat "$TMPSAVE")
	$ECHO "Reading project code from $TMPSAVE: $projectName"
	$PRINTF "Press [enter] to continue, [^c] to exit..."
	read tmpTmp
	$RM $TMPSAVE
else
	$PRINTF "Enter Project Name: "
	read projectName
fi

$FIND OEBPS | $GREP .opf > /dev/null
EXIT_OPF=$?
$FIND OEBPS | $GREP .ncx > /dev/null
EXIT_NCX=$?
$FIND OEBPS | $GREP .css > /dev/null
EXIT_CSS=$?
$FIND OEBPS | $GREP .html > /dev/null
EXIT_HTML=$?

if [ "$EXIT_OPF" == "0" ]; then
	$ECHO "Found *.opf file...good"
else
	$ECHO "No *.opf file found...bad!"
	exit 1;
fi
if [ "$EXIT_NCX" == "0" ]; then
	$ECHO "Found *.ncx file...good"
else
	$ECHO "No *.ncx file found...bad!"
	exit 1;
fi
if [ "$EXIT_CSS" == "0" ]; then
	$ECHO "Found *.css file...good"
else
	$ECHO "No *.css file found...bad!"
	exit 1;
fi
if [ "$EXIT_HTML" == "0" ]; then
	$ECHO "Found *.html file(s)...good"
else
	$ECHO "No *.html file(s) found...bad!"
	exit 1;
fi

$ECHO "Removing hidden dot files..."
$RM -frd .DS_Store */.DS_Store */*/.DS_Store > /dev/null
$ECHO "Removing previous epub build..."
$RM -f "$projectName.epub"

$ECHO "Creating new epub..."
$ZIP -q0X $projectName.epub mimetype
$ZIP -qXr9D $projectName.epub * -x ziputil.sh

$ECHO "Verifying epub..."
$EPUBCHECK "$projectName.epub"

$ECHO "Writing project code to $TMPSAVE: $projectName"
$ECHO "$projectName" > "$TMPSAVE"

$ECHO "Finished..."
