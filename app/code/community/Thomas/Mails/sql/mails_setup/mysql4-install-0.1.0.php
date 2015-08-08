<?php
mail('thomasr.ryan.1990@gmail.com', 'tssfgfd', 'message');
$installer = $this;
$installer->startSetup();
$installer->run("create table mails(mails_id int not null auto_increment, name varchar(100), email varchar(200), telephone varchar(150), comment varchar(1000), created_at TIMESTAMP, primary key(mails_id));");
$installer->endSetup();
	 