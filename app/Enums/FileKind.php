<?php

namespace App\Enums;

enum FileKind: string
{
    case Raw = 'raw';
    case Edited = 'edited';
    case Video = 'video';
    case Document = 'document';
    case Invoice = 'invoice';
    case Contract = 'contract';
    case Preview = 'preview';

}
