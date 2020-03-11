#!/bin/bash

PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

STRUCTURE=(
  "mysql"
  "log"
  "log/mysql"
  "log/std"
)

for FILE in $PATH/*; do
  if [ $FILE != "$PATH/clear.sh" ];
    then
      echo "Removing "$FILE;
      /bin/rm -rf $FILE
  fi;
done;

for DIR in ${STRUCTURE[@]}; do
  echo "Creating" $DIR;
  /bin/mkdir $PATH/$DIR
  /bin/chmod 0777 $PATH/$DIR
  > $PATH/$DIR/.gitkeep
done
