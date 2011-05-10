<?php

class community_partnerTest extends WebTestCase
{
	public $fixtures=array(
		'community_partners'=>'community_partner',
	);

	public function testShow()
	{
		$this->open('?r=community_partner/view&id=1');
	}

	public function testCreate()
	{
		$this->open('?r=community_partner/create');
	}

	public function testUpdate()
	{
		$this->open('?r=community_partner/update&id=1');
	}

	public function testDelete()
	{
		$this->open('?r=community_partner/view&id=1');
	}

	public function testList()
	{
		$this->open('?r=community_partner/index');
	}

	public function testAdmin()
	{
		$this->open('?r=community_partner/admin');
	}
}
