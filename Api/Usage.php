<?php
/**
 * Author: liguidong94@gmail.com
 * Date: 2019/5/27 10:07 AM
 */

namespace Helper\Agora\Api;

use Helper\Agora\Agora;

class Usage extends Agora
{
    /**
     * 获取用量数据
     *
     * @param string $from_date
     * @param string $to_data
     * @param        $projects
     *
     * @return array
     */
    public function get(string $from_date, string $to_data, $projects)
    {
        $projects = is_array($projects) ? implode(',', $projects) : $projects;
        return $this->request('GET', '/usage', compact('from_date', 'to_data', 'projects'));
    }
}