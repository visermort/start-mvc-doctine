<?php

namespace app\classes;

/**
 * Class Help
 * @package app\lib\help
 */
class Help
{
    /**
     * partone-parttwo-partthree or partone_parttwo_partthree to PartoneParttwoPartthree if reverse false
     * or back if $reverse true
     * @param string $command
     * @return string
     */
    public static function commandToAction($command, $reverse = false)
    {
        if ($reverse) {
            $commandArray = array_diff(preg_split('/(?=[A-Z])/', $command), ['']);
            foreach ($commandArray as &$item) {
                $item = strtolower($item);
            }
            return implode('-', $commandArray);
        }
        $commandArray = preg_split('/[-_]/', $command);
        foreach ($commandArray as &$item) {
            $item = ucfirst($item);
        }
        return implode('', $commandArray);
    }
}