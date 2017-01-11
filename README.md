Process WebSocket
=================

PHP WebSocket server which broadcasts command output to all connected clients.

Usage
-----

	$ php process_web_socket.php port command

For example:

	$ php process_web_socket 9000 tail -f /path/to/file.log
	
