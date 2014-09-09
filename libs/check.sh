#!/bin/bash
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"; cd $DIR
#echo Attogram check: $DIR; echo;

find . -type f -name "*.php" -exec php -l {} \;; echo;
DB="./db/global"; echo $DB; ls -l $DB; echo;
HT="./.htaccess"; echo $HT; cat $HT; echo;
HT="./actions/.htaccess"; echo $HT; cat $HT; echo;
HT="./plugins/.htaccess"; echo $HT; cat $HT; echo;
HT="./templates/.htaccess"; echo $HT; cat $HT; echo;
HT="./libs/.htaccess"; echo $HT; cat $HT; echo;
HT="./db/.htaccess"; echo $HT; cat $HT; echo;


CO="./libs/config.php"; echo $CO; echo "<textarea rows='10' cols='95'>"; cat $CO; echo "</textarea>";

echo END CHECK; 
