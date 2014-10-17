<?php
class StaticExample {
	static public $aNum = 0;

	static public function sayHello() {
		self::$aNum++;
		print "hello (".self::$aNum.")\n";
	}
}


print StaticExample::$aNum;
StaticExample::sayHello();
StaticExample::sayHello();
