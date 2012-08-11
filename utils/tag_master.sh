#!/bin/bash

DATE=$(date -u +"%Y%m%d_%H%M%S")
TAG="MASTER_TAG_$DATE"
COMMENT="$@"

if [ -z "$COMMENT" ]
 then
  echo "Please supply a commit comment for the tag"
  exit -1
fi

pushd ~/sandbox/MAD >/dev/null

echo "Tagging with tag: $TAG"
git tag -a $TAG -m "$COMMENT"
git push --tags
echo "Tagged and pushed!"
