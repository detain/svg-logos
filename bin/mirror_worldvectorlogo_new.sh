#!/bin/bash
set -x
rm -f svgs;
IFS="
"
for l in a b c d e f g h i j k l m n o p q r s t u v w x y z 0 1 2 3 4 5 6 7 8 9; do
  p=1
  nextpage=2
  while [ "$nextpage" != "" ]; do
    if [ $p -eq 1 ]; then
      u="https://worldvectorlogo.com/alphabetical/$l"
    else
      u="https://worldvectorlogo.com/alphabetical/$l/$p"
    fi
    curl -s "$u"|sed s#'\(<a\)'#"\n\1"#g > .svgs.letter-${l}-page-${p};
    nextpage="$(cat .svgs.letter-${l}-page-${p} | sed -e s#"<a "#"\n<a "#g -e s#"<img"#"\n<img"#g | grep "^<a class=\"button.*>Next" | sed s#"<a class=\"button.*/\([^/\"]*\)\">Next.*$"#"\1"#g)"
    for data in $(cat .svgs.letter-${l}-page-${p} | grep logo__img|sed s#"^<a class=\"logo\" href=\"https://worldvectorlogo.com/logo/\([^\"]*\)\".*<img.*src=\"\([^\"]*\)\".*alt=\"\([^\"]*\) logo vector\".*$"#"\1 \2 \3"#g); do
      id="$(echo "$data"|cut -d" " -f1)"
      logo="$(echo "$data"|cut -d" " -f2)"
      name="$(echo "$data"|cut -d" " -f3-)"
      tags="$(curl -s https://worldvectorlogo.com/logo/${id}|sed -e s#"<a"#"\n<a"#g|grep meta__tag-link|cut -d\> -f2|cut -d\< -f1|tr "\n" ,)"
      echo "{\"id\": \"${id}\", \"name\": \"${name}\", \"logo\": \"${logo}\", \"tags\": [${tags}]},"
    done
    echo "$(cat svgs.tmp|wc -l) SVGs added for '$l' page '$p'";
    cat svgs.tmp >> svgs;
    if [ "$nextpage" != "" ]; then
      p=$(($p + 1))
    fi
  done;
done;
echo "$(cat svgs|wc -l) Total SVGs";
rm -f svgs.tmp;
