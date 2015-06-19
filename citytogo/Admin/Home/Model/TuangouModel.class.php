<?php

namespace Home\Model;

/**
 * Tuangou
 * 团购信息模型
 */
class TuangouModel extends CommonModel {

    // realtions
    protected $_link = array(
        
        // 一个团购信息有多个数据表
        'data' => array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'Data',
            'foreign_key' => 'tuangou_id',
        ),

        'picture' => array(
            'mapping_type' => self::HAS_ONE,
            'class_name' => 'Picture',
            'foreign_key' => 'tuangou_id',
        ),  
    );

    protected $_validate = array(
        
    );

    /**
     * title
     */
    protected $validateTuangouTitle = array(
        // 模型名唯一性验证
        array('title','','团购已经存在！',2,'unique',3),
    );

    protected $_auto = array(
        // 创建时间
        array('created_time', 'time', [1, 'function']),
        //更新时间
        array('update_time', 'time', [2, 'function'])
    );

    /**
     * 团购标题是否可用
     * @param  array  $model Model数组
     * @param  int    $id    需要更新模型的id
     * @return boolean       是否可用
     */
    public function isValidTuangouTitle($tuangou, $id) {
        return $this->validateConditions($this->validateTuangouTitle, $model,$id);
    }
}
