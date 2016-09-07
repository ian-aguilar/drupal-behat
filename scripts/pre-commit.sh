#!/bin/sh

echo "Running Code Sniffer..."
composer phpcs
if [ $? != 0 ]
then
    echo "Please fix Code Sniffer errors before commit."
    exit 1
fi

exit $?
