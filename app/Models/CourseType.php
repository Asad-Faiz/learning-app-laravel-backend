<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\ModelTree;

class CourseType extends Model
{
    use HasFactory;
    use ModelTree;
    use DefaultDatetimeFormat;

}