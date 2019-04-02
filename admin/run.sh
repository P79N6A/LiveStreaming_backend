#!/bin/bash
path() {
    export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
}
main(){
    step=1 #间隔的秒数，不能大于60
    DIR="$( cd "$( dirname "$0"  )" && pwd  )"
    for (( i = 0; i < 60; i=(i+step) )); do
    php $DIR/crontab.php>/dev/null &
    #php $DIR/crontab_award.php>/dev/null &
    php $DIR/crontab_autogame.php>/dev/null &
    php $DIR/crontab_game.php>/dev/null &
    php $DIR/crontab_deal.php>/dev/null &
    php $DIR/crontab_guard.php>/dev/null &
    # php $DIR/crontab_heat_rank.php>/dev/null &
    php $DIR/crontab_prop.php>/dev/null &
    php $DIR/crontab_shop.php>/dev/null &
    php $DIR/crontab_lucknum.php>/dev/null &
    # php $DIR/crontab_yun.php >/dev/null &
    sleep $step
    done
    exit 0
}
path
main