<?php

class volunteers_inTest extends WebTestCase
{
	public $fixtures=array(
		'volunteers_ins'=>'volunteers_in',
	);

	public function testShow()
	{
		$this->open('?r=volunteers_in/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=volunteers_in/create');
	}

	public function testUpdate()
	{
		$this->open('?r=volunteers_in/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=volunteers_in/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=volunteers_in/index');
	}

	public function testAdmin()
	{
		$this->open('?r=volunteers_in/admin');
	}
}
