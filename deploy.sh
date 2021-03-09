#!/bin/bash

read -s -p "Enter password: " SSHPASS

sshpass -p $SSHPASS ssh bajustone@ikwim.com 'rm -r public_html/api/v01'
sshpass -p $SSHPASS rsync -r ./v01 bajustone@ikwim.com:public_html/api/