#!/bin/bash
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
    curl -s "$u"|sed s#"<img"#"\n<img"#g > .svgs.letter-${l}-page-${p};
    nextpage="$(cat .svgs.letter-${l}-page-${p} | sed -e s#"<a "#"\n<a "#g -e s#"<img"#"\n<img"#g | grep "^<a class=\"button.*>Next" | sed s#"<a class=\"button.*/$l/\([0-9]*\)\">Next.*$"#"\1"#g)"
    cat .svgs.letter-${l}-page-${p} | grep "^<img.*logo__img"|sed s#"^.*<img.*src=\"\([^\"]*\)\".*alt=\"\([^\"]*\) logo vector\".*$"#"\1 \2"#g > svgs.tmp;
    rm -f .svgs.letter-${l}-page-${p}
    echo "$(cat svgs.tmp|wc -l) SVGs added for '$l' page '$p'";
    cat svgs.tmp >> svgs;
    if [ "$nextpage" != "" ]; then
      p=$(($p + 1))
    fi
  done;
done;
echo "$(cat svgs|wc -l) Total SVGs";
rm -f svgs.tmp;
