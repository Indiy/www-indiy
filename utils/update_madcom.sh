#!/bin/bash

DATE=$(date +"%Y%m%d_%H%M%S")
TAG="PROD_UPDATE_$DATE"
COMMENT="Production Pull"

pushd ~/sandbox/MAD >/dev/null

ret=$(git pull)
if [ "$ret" == "Already up-to-date." ]
 then
  echo No changes on server, tree not updated.
else
  echo Pull got changes, tagging
  git tag -a $TAG -m "$COMMENT"
  git push --tags
  echo Tagged, updating...
  popd >/dev/null
  pushd ~/public_html >/dev/null
  find . -name "*.php" -exec rm {} \; >/dev/null
  find . -name "*.css" -exec rm {} \; >/dev/null
  find . -name "*.js" -exec rm {} \; >/dev/null
  find . -name "*.html" -exec rm {} \; >/dev/null
  find . -name "*.htm" -exec rm {} \; >/dev/null
  find . -name "*.ini" -exec rm {} \; >/dev/null
  cp -r ~/sandbox/MAD/www/* . >/dev/null
  #chown -R `stat . -c %u:%g` *
  echo done updating
fi

popd >/dev/null