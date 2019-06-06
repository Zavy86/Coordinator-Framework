#!/bin/bash
#
# Count Line Of Code
#
# @package Coordinator
# @author  Manuel Zavatta <manuel.zavatta@gmail.com>
# @link    http://www.coordinator.it
#
cloc $(git ls-files | grep -v "^helpers")
