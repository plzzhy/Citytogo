<?php

namespace Home\Model;

/**
 * City
 * 城市模型
 */
class CityModel extends CommonModel {

    // realtions
    protected $_link = array(

    );

    protected $_validate = array (
    	array('name', 'require', '子栏目名称不能为空', 0, 'regex', 3),
    	array('level', 'require', '子栏目等级不能为空', 0, 'regex', 3),
    	array('pid', 'require', '父节点不能为空', 0, 'regex', 3),
    );
}
