#!/bin/bash

 # Is this script already running? (https://benhoskins.dev/run-bash-once/)
pidof -o %PPID -x $0 >/dev/null && echo "ERROR: Script $0 already running" && exit 1

# do your script here...