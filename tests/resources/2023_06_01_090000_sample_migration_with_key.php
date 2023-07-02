<?php

class SampleMigrationWithKey
{

   /*
   |--------------------------------------------------------------------------
   | Cache Migration File
   |--------------------------------------------------------------------------
   |
   | Redis keys that you wish to clear should be added to the patterns array.
   | Invalid patterns: '*', less than 3 characters.
   |
   */

    public $patterns = [
        'exampleRedisKey:*:subKey',
    ];
}
