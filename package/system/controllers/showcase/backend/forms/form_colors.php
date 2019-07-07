<?php

class formShowcaseColors extends cmsForm{

    public function init($ctype_name = false){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required')
                        )
                    )),

					new fieldColor('color', array(
                        'title' => LANG_PARSER_COLOR,
                        'default' => '#fff'
                    )),

                )
            )
        );
    }
}