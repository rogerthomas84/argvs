<?php

namespace ReignTests;

use Argvs\Argvs;
use Exception;
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
        /** @noinspection PhpParamsInspection */
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
        /** @noinspection PhpParamsInspection */
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
        /** @noinspection PhpParamsInspection */
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

        /** @noinspection PhpParamsInspection */
        $this->assertCount(3, $arguments);

        /** @noinspection PhpParamsInspection */
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

        /** @noinspection PhpParamsInspection */
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
        /** @noinspection PhpParamsInspection */
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
        /** @noinspection PhpParamsInspection */
        $this->assertCount(1, $arguments);
        $this->assertEquals('Joe', $instTwo->getArg('--name'));
        $this->assertTrue($instTwo->hasHelp());
    }
}
