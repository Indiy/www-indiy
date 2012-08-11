#!/bin/bash

if [ -z "$@" ]
 then
  echo "Please supply a commit comment for the tag"
  exit -1
fi

DATE=$(date +"%Y%m%d_%H%M%S")
TAG="MASTER_TAG_$DATE"
COMMENT="$@"

pushd ~/sandbox/MAD >/dev/null

echo "Tagging with tag: $TAG"
echo git tag -a $TAG -m "$COMMENT"
echo git push --tags
echo "Tagged and pushed!"
