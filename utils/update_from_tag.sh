#!/bin/bash

INPUT_TAG=$1

if [ -z $INPUT_TAG ]
 then
  echo "Please supply a tag to checkout."
  exit -1
fi

DATE=$(date -u +"%Y%m%d_%H%M%S")
TAG="PROD_UPDATE_$DATE"
COMMENT="Production Update from tag $INPUT_TAG"

pushd ~/sandbox/MAD >/dev/null
git pull
git checkout tags/$TAG
RET=$?
echo "result: $?"
if [ $RET -ne 0 ]
 then
  echo "Failed to checkout tags/$INPUT_TAG"
else
  echo "Checked out tag: tags/$INPUT_TAG"
  exit
  git tag -a $TAG -m "$COMMENT"
  git push --tags
  echo "Tagged, updating..."
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