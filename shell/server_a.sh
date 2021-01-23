#!/bin/sh
rsync -ah -e "ssh -i /xxx/key" user@server_a:/sync_dir /backup_directory/server_a
