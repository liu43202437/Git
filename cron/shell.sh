#!/bin/bash

start() {
	bash AutoReminder.sh
	
	echo "Daemon started."
}
 
stop() {
	pkill -f "AutoReminder.sh"
 
	echo -e "\nDaemon stopped."
	return 1
}
 
restart() {
	stop	
	sleep 2
	start
	return $?
}
 
case "$1" in
	start)
		start
		;;
	stop)
		stop
		;;
  	restart)
		stop
		start
		;;
	*)
		echo "Usage: $0 {start|stop|restart}"
		exit 2
esac
 
exit $?
