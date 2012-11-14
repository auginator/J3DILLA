#!/bin/bash

rm *.html
rm myModels/*.html

cat indextemplate > index.html

ls myModels |grep .stl |cut -d'.' -f1 | while read line ; do
echo "<li><a href=modelViewer.php?stl=$line>$line</a></li>" >> index.html
done
echo "</ul></div></body></html>" >> index.html
