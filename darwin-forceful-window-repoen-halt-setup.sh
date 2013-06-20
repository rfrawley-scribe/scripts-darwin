#!/bin/bash
FILEPATH="/scripts/bin-enabled/darwin-forceful-window-repoen-halt.sh"
rm /Users/*/Library/Preferences/ByHost/com.apple.loginwindow.*
defaults write com.apple.loginwindow LoginHook $FILEPATH
