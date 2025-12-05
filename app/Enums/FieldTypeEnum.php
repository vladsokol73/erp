<?php

namespace App\Enums;

enum FieldTypeEnum: string
{
    case TEXT         = 'text';
    case NUMBER       = 'number';
    case SELECT       = 'select';
    case MULTISELECT  = 'multiselect';
    case COUNTRY      = 'country';
    case TEXTAREA     = 'textarea';
    case DATE         = 'date';
    case FILE         = 'file';
    case CHECKBOX     = 'checkbox';
    case PROJECT      = 'project';
}
