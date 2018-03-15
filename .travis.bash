#!/usr/bin/env bash
readonly DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )";
cd $DIR;
set -e
set -u
set -o pipefail
standardIFS="$IFS"
IFS=$'\n\t'
echo "
===========================================
$(hostname) $0 $@
===========================================
"
rm -f composer.lock
gitBranch=$(if [ "$TRAVIS_PULL_REQUEST" == "false" ]; then echo $TRAVIS_BRANCH; else echo $TRAVIS_PULL_REQUEST_BRANCH; fi)
export gitBranch
git checkout $gitBranch
composer install
git checkout HEAD composer.lock


mkdir -p $DIR/cache/qa && chmod 777 $DIR/cache/qa



echo "
===========================================
$(hostname) $0 $@ COMPLETED
===========================================
"