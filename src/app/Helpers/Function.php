<?php

function isCli()
{
    return preg_match("/cli/i", php_sapi_name()) ? true : false;
}
