<?php

declare( strict_types=1 );

namespace Amanah\SEOEngine\Infrastructure\Repository;

defined( 'ABSPATH' ) || exit;

abstract class WordPressRepository implements RepositoryInterface {

	protected $wpdb;

	protected $table;

	public function __construct( \wpdb $wpdb, string $table ) {
		$this->wpdb  = $wpdb;
		$this->table = $table;
	}

	protected function findRawById( int $id ): ?array {
		$row = $this->wpdb->get_row(
			$this->wpdb->prepare(
				"SELECT * FROM {$this->table} WHERE id = %d LIMIT 1",
				$id
			),
			ARRAY_A
		);

		return is_array( $row ) ? $row : null;
	}

	public function deleteById( int $id ): bool {
		return false !== $this->wpdb->delete(
			$this->table,
			array( 'id' => $id ),
			array( '%d' )
		);
	}
}