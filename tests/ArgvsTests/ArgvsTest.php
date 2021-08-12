<?php

namespace ArgvsTests;

use Argvs\Argvs;
use PHPUnit\Framework\TestCase;

/**
 * Class ArgvsTest
 * @package ArgvsTests
 */
class ArgvsTest extends TestCase
{
    public function testNormal()
    {
        $args = [
            'foo.php',
            '--foo=bar',
            '--name=Joe',
            '--age=23'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();
        $this->assertEquals('foo.php', $inst->getScript());
        $this->assertCount(3, $arguments);
        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertEquals('Joe', $inst->getArg('--name'));
        $this->assertEquals('23', $inst->getArg('--age'));
    }

    public function testNormalAddAndRemoveArgument()
    {
        $args = [
            'foo.php',
            '--foo=bar',
            '--name=Joe',
            '--age=23'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();
        $this->assertEquals('foo.php', $inst->getScript());
        $this->assertCount(3, $arguments);
        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertEquals('bar', $inst->getArg('-foo'));
        $this->assertEquals('bar', $inst->getArg('foo'));
        $this->assertTrue($inst->removeArg('foo'));
        $this->assertCount(2, $inst->getArgs());
        $this->assertNull($inst->getArg('foo'));

        $inst->addArg('foo', 'bar');
        $this->assertCount(3, $arguments);
        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertEquals('bar', $inst->getArg('-foo'));
        $this->assertEquals('bar', $inst->getArg('foo'));
    }

    public function testNormalRemoveLeadingDashes()
    {
        $args = [
            'foo.php',
            '--foo=bar',
            '--name=Joe',
            '--age=23'
        ];
        $inst = Argvs::getInstance($args, count($args), true);
        $arguments = $inst->getArgs();
        $this->assertEquals('foo.php', $inst->getScript());
        $this->assertCount(3, $arguments);
        $this->assertEquals('bar', $inst->getArg('foo'));
        $this->assertEquals('Joe', $inst->getArg('name'));
        $this->assertEquals('23', $inst->getArg('age'));

        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertEquals('Joe', $inst->getArg('--name'));
        $this->assertEquals('23', $inst->getArg('--age'));
    }

    public function testDuplicateKeys()
    {
        $args = [
            'foo.php',
            '--foo=bar',
            '--name=Joe',
            '--name=Jane'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();

        $this->assertEquals('foo.php', $inst->getScript());

        $this->assertCount(3, $arguments);

        $this->assertCount(2, $inst->getArg('--name')); // Joe, Jane

        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertContains('Joe', $inst->getArg('--name'));
        $this->assertContains('Jane', $inst->getArg('--name'));
    }

    public function testDuplicateValues()
    {
        $args = [
            'foo.php',
            '--foo=bar',
            '--name=Joe',
            '--name=Joe'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();

        $this->assertEquals('foo.php', $inst->getScript());

        $this->assertCount(3, $arguments);

        $this->assertEquals('bar', $inst->getArg('--foo'));
        $this->assertEquals('Joe', $inst->getArg('--name', true));
    }

    public function testHelpFunction()
    {
        $args = [
            'foo.php',
            '--help',
            '--name=Joe'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();

        $this->assertEquals('foo.php', $inst->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $inst->getArg('--name'));
        $this->assertTrue($inst->hasHelp());

        $argsTwo = [
            'foo.php',
            '--name=Joe',
            '--help'
        ];
        $instTwo = Argvs::getInstance($argsTwo, count($argsTwo));
        $arguments = $instTwo->getArgs();

        $this->assertEquals('foo.php', $instTwo->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instTwo->getArg('--name'));
        $this->assertTrue($instTwo->hasHelp());


        $argsThree = [
            'foo.php',
            '--name=Joe'
        ];
        $instThree = Argvs::getInstance($argsThree, count($argsThree));
        $arguments = $instThree->getArgs();

        $this->assertEquals('foo.php', $instThree->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instThree->getArg('--name'));
        $this->assertFalse($instThree->hasHelp());

        $argsFour = [
            'foo.php',
            '--name=Joe',
        ];
        $instFour = Argvs::getInstance($argsFour, count($argsFour));
        $arguments = $instFour->getArgs();

        $this->assertEquals('foo.php', $instFour->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instFour->getArg('--name'));
        $this->assertFalse($instFour->hasHelp());
    }

    public function testVerboseFunction()
    {
        $args = [
            'foo.php',
            '--verbose',
            '--name=Joe'
        ];
        $inst = Argvs::getInstance($args, count($args));
        $arguments = $inst->getArgs();

        $this->assertEquals('foo.php', $inst->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $inst->getArg('--name'));
        $this->assertTrue($inst->hasVerbose());

        $argsTwo = [
            'foo.php',
            '--name=Joe',
            '--verbose'
        ];
        $instTwo = Argvs::getInstance($argsTwo, count($argsTwo));
        $arguments = $instTwo->getArgs();

        $this->assertEquals('foo.php', $instTwo->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instTwo->getArg('--name'));
        $this->assertTrue($instTwo->hasVerbose());


        $argsThree = [
            'foo.php',
            '--name=Joe'
        ];
        $instThree = Argvs::getInstance($argsThree, count($argsThree));
        $arguments = $instThree->getArgs();

        $this->assertEquals('foo.php', $instThree->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instThree->getArg('--name'));
        $this->assertFalse($instThree->hasVerbose());

        $argsFour = [
            'foo.php',
            '--name=Joe',
        ];
        $instFour = Argvs::getInstance($argsFour, count($argsFour));
        $arguments = $instFour->getArgs();

        $this->assertEquals('foo.php', $instFour->getScript());
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instFour->getArg('--name'));
        $this->assertFalse($instFour->hasVerbose());
    }

    public function testFlagFunctions()
    {
        $args = [
            'foo.php',
            '--verbose',
            '--help',
            '--print'
        ];
        $inst = Argvs::getInstance($args, count($args));

        $this->assertFalse($inst->hasFlag('--foobar'));
        $this->assertTrue($inst->hasFlag('--verbose'));
        $this->assertTrue($inst->hasFlag('-verbose'));
        $this->assertTrue($inst->hasFlag('verbose'));
        $this->assertTrue($inst->hasFlag('--help'));
        $this->assertTrue($inst->hasFlag('-help'));
        $this->assertTrue($inst->hasFlag('help'));
        $this->assertTrue($inst->hasFlag('--print'));
        $this->assertTrue($inst->hasFlag('-print'));
        $this->assertTrue($inst->hasFlag('print'));

        $inst->removeFlag('verbose');
        $inst->removeFlag('help');
        $inst->removeFlag('print');

        $this->assertFalse($inst->hasFlag('--verbose'));
        $this->assertFalse($inst->hasFlag('-verbose'));
        $this->assertFalse($inst->hasFlag('verbose'));
        $this->assertFalse($inst->hasFlag('--help'));
        $this->assertFalse($inst->hasFlag('-help'));
        $this->assertFalse($inst->hasFlag('help'));
        $this->assertFalse($inst->hasFlag('--print'));
        $this->assertFalse($inst->hasFlag('-print'));
        $this->assertFalse($inst->hasFlag('print'));
    }
}
