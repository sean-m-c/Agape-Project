<?php

class issue_typeTest extends WebTestCase
{
	public $fixtures=array(
		'issue_types'=>'issue_type',
	);

	public function testShow()
	{
		$this->open('?r=issue_type/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=issue_type/create');
	}

	public function testUpdate()
	{
		$this->open('?r=issue_type/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=issue_type/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=issue_type/index');
	}

	public function testAdmin()
	{
		$this->open('?r=issue_type/admin');
	}
}
