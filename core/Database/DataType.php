<?php

namespace Core\Database;

enum DataType: string
{
    case INT = 'integer';
    case VARCHAR = 'varchar(255)';
    case PRIMARY = 'int AUTO_INCREMENT';
}
