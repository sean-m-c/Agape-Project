<?php

class volunteerTest extends WebTestCase
{
	public $fixtures=array(
		'volunteers'=>'volunteer',
	);

	public function testShow()
	{
		$this->open('?r=volunteer/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=volunteer/create');
	}

	public function testUpdate()
	{
		$this->open('?r=volunteer/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=volunteer/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=volunteer/index');
	}

	public function testAdmin()
	{
		$this->open('?r=volunteer/admin');
	}
}
