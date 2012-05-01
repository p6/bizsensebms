#!/bin/bash
# Script to generate the BizSense build
# Copy this script to the directory where you want to generate the build

# Usage 
# sh build.sh "<SVN URL>" "<release number>"
# this is the URL from where we SVN export
# The release number 
# Example sh build.sh svn://svn.binaryvibes.co.in/bizsense/tags/release-0.3-alpha 0.3-alpha
 
rm -rf bizsense
STRING="$1"
svn export $1 bizsense
cd bizsense
rm -rf tests
rm -rf util
cd library/
find . -name '*.php' -not -wholename '*/Loader/Autoloader.php' \
    -not -wholename '*/Application.php' -print0 | \
    xargs -0 sed --regexp-extended --in-place 's/(require_once)/\/\/ \1/g'
cd ..
cd ..
TARPATH="bizsense-$2.tar.gz"
tar -czvf $TARPATH bizsense 
TARPATH="bizsense-$2.tar.bz2"
tar -cjvf $TARPATH bizsense 
ZIPPATH="bizsense-$2"
zip $ZIPPATH bizsense 
