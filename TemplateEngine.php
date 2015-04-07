<?php

class TemplateEngine {

	private $template = '';

	/**
	 * @param string $template
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}

	public function __construct($templateName = '') {
		if ($templateName != '') {
			$this->loadTemplate($templateName);
		}
	}

	public function loadTemplate($templateName) {
		$this->template = file_get_contents(TEMPLATE_PATH.$templateName.'.html');
	}

	public function replaceMarkerArray($markerArray) {
		foreach ($markerArray as $marker => $value) {
			$this->template = str_replace('###' . $marker . '###', $value, $this->template);
		}
	}

	public function replaceMarker ($marker, $value) {
		$this->replaceMarkerArray(
			array(
				$marker => $value
			)
		);
	}

}