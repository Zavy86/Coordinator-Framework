#!/bin/bash
#
# Count Line Of Code
#
# @package Coordinator
# @author  Manuel Zavatta <manuel.zavatta@gmail.com>
# @link    http://www.coordinator.it
#
# @todo modificare in modo che calcoli anche i sottomoduli (probabilmente usando ls puro)
#
cloc $(find . -type f | grep -vE "(git/|helpers/|uploads/|tmp/)")
