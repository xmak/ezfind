<?php

class eZFindElevateConfiguration_MockUp extends eZFindElevateConfiguration
{
    public static function generateConfiguration()
    {
        return parent::generateConfiguration();
    }

    public static function getConfiguration()
    {
        return parent::getConfiguration();
    }
}

eZFindElevateConfiguration_MockUp::generateConfiguration();
var_dump( eZFindElevateConfiguration_MockUp::getConfiguration() );
?>