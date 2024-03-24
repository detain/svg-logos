#!/bin/bash
IFS="
"
for u in $(cat svgs|cut -d" " -f1); do
  n=$(echo $u|cut -d/ -f5);
  l=$(echo $n|cut -c1);
  wget -bq $u -O svg/${l}/${n};
done
