<?php

namespace database\interfaces;

use \database\models\Saveable;

interface iMapper {
	
	public function __construct(iDatabase $conn);

	public function create(Saveable $saveable);

	public function read(Array $criteria, Saveable $obj);

	public function update(Saveable $saveable, Array $values);

	public function delete(Saveable $saveable);

}