#!/bin/bash

##
# Start/Stop all homebrew launchd daemons
##

#
# launchagenets directory
#
launchagents_dir="/Users/rmf/Library/LaunchAgents/"

#
# launchctl bin
#
launchctl_bin="$(which launchctl)"

#
# display script usage information
#
usage() {
	echo "USAGE:"
	echo -e "\t${0} stop|start"
}

#
# first argument required and must be either "load" or "unload"
#
if [[ -z "${1}" ]] || [[ "${1}" != "load" && "${1}" != "unload" ]]; then
	usage
	exit 1
fi

#
# list of all homebrew daemons
#
daemons=$(ls ${launchagents_dir}|grep -o "homebrew.*")

for d in $daemons; do
	d_path="${launchagents_dir}${d}"
	echo "Running: ${launchctl_bin} ${1} ${d_path}"
	${launchctl_bin} ${1} ${d_path}
done