#!/bin/bash

max_jobs=$1
letters="0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z"

function limit_bg_jobs {
  while [ $(jobs -r | wc -l) -ge $max_jobs ]; do
    wait -n;
  done;
}

cd "$(readlink -f "$(dirname "$0")")";

for i in $letters; do
  limit_bg_jobs;
  echo "Mirroring Letter $i";
  php mirror_worldvectorlogo.php $i &
done
