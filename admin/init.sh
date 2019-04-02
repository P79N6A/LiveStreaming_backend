#!/bin/bash
path() {
    export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
}
main(){
    rm -rf /public/runtime/* /public/session/*
    chown linux:linux admin
    chown linux:linux -R public
    chmod 0600 java
}
path
main
