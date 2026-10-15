<?php
/**
 * CORE-006 Content Analysis input contract.
 *
 * @package AMANAH_SEO_AI
 */
defined( 'ABSPATH' ) || exit;

class AMANAH_SEO_AI_Content_Analysis_Input {
	private $title;
	private $content;
	private $keyword;
	private $entity;

	public function __construct( $title, $content, $keyword, $entity ) {
		$this->title   = (string) $title;
		$this->content = (string) $content;
		$this->keyword = (string) $keyword;
		$this->entity  = (string) $entity;
	}

	public function get_title() { return $this->title; }
	public function get_content() { return $this->content; }
	public function get_keyword() { return $this->keyword; }
	public function get_entity() { return $this->entity; }

	public function to_array() {
		return array(
			'title'   => $this->title,
			'content' => $this->content,
			'keyword' => $this->keyword,
			'entity'  => $this->entity,
		);
	}
}
