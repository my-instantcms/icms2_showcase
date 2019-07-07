<?php

class onShowcaseBeforePrintHead extends cmsAction {

    public function run($template) {

		$template->addCSS($template->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
		$template->addJS($template->getTplFilePath('controllers/showcase/js/showcase.js', false), false);

        if ($template->getLayout() === "admin" || !empty($this->options['fa'])) {
            return $template;
        }
		
		$template->addMainCSS('templates/default/controllers/showcase/css/font-awesome.min.css', false);

        return $template;

    }

}