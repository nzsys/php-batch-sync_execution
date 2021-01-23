#!/bin/sh
rsync -ah -e "ssh -i /xxx/key" user@server_b:/sync_dir /backup_directory/server_b