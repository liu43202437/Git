#!/bin/bash
awk -F '=' '/^\$config\['\''base_url/ {gsub(";","",$2);gsub("'\''","",$2);print $2}' '../application/config/config.php'
