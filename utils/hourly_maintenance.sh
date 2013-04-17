#!/bin/bash
pushd $1
php audio_make_alt.php >/tmp/audio_make_alt.log
php video_make_alt.php >/tmp/video_make_alt.log
popd