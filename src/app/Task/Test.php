<?php

namespace App\Task;

use App\Classes\AbstractTask;

class Test extends AbstractTask
{
    public function execute($parameter)
    {
        // TODO: Implement execute() method.
        var_dump($parameter);

        $output = [
            'errno' => 0,
            'data' => [
                'task_name' => 'test',
                'restult'=>'ok',
            ],
        ];

        return $output;
    }

}