<?php

namespace oPheme;

use Illuminate\Support\Facades\App;
use oPheme\Classes\Helpers\DataHelper_unused;

App::bind('datahelper', function()
{
    return new DataHelper_unused;
});