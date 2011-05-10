<?php

class has_issuesTest extends WebTestCase
{
	public $fixtures=array(
		'has_issues'=>'has_issues',
	);

	public function testShow()
	{
		$this->open('?r=has_issues/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=has_issues/create');
	}

	public function testUpdate()
	{
		$this->open('?r=has_issues/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=has_issues/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=has_issues/index');
	}

	public function testAdmin()
	{
		$this->open('?r=has_issues/admin');
	}
}
