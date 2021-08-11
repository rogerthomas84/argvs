<?php
namespace Argvs;

/**
 * Class Argvs
 */
class Argvs implements ArgvInterface
{
    /**
     * Holds the static instance of this object.
     *
     * @var Argvs
     */
    protected static $_instance = null;

    /**
     * Holds the arguments, minus the script name.
     *
     * @example [
     *      ['name' => 'Joe'],
     *      ['age' => '24'],
     *      // etc...
     * ]
     *
     * @var array
     */
    protected $args = [];

    /**
     * Holds previously established values. These differ depending
     * on whether values have been deduplicated. And the keys are
     * appended with `|Argvs|0` or `|Argvs|1` depending on whether
     * the argument requested was also requested to be deduplicated
     * or not.
     *
     * @example [
     *      'age|Argvs|0' => '24',
     *      'favFood|Argvs|1' => ['Rice', 'Chips', 'Curry']
     * ]
     * @var array
     */
    protected $tmp = [];

    /**
     * The name of the script that's been executed.
     *
     * @example `my-script.php`
     * @var string
     */
    protected $script = null;

    /**
     * Has the parameter of `--help`, `-help` or just `help` been passed
     * into the parameters?
     *
     * @var bool
     */
    protected $help = false;

    /**
     * When accepting parameters, should `--` or `-` be removed?
     * @example `--name=Joe` would be interpreted as `name=Joe` if
     * this is true.
     *
     * @var bool
     */
    protected $stripDashes = true;

    /**
     * Construct the instance, protected to avoid `new` instances. Passing in
     * the raw $args, $num of $args and whether leading dashes should also be
     * removed.
     *
     * @param array|null $args
     * @param int|null $num
     * @param bool $stripLeadingDashes (optional) default true.
     */
    protected function __construct(array $args = null, $num = null, $stripLeadingDashes = true)
    {
        if (is_array($args) && count($args) === $num) {
            $this->stripDashes = $stripLeadingDashes;
            $this->processArguments($args);
        }
    }

    /**
     * Process the array of arguments into the stack.
     *
     * @param array $args
     * @return int
     */
    protected function processArguments(array $args)
    {
        if (count($args) > 0) {
            $this->script = $args[0];
            array_shift($args);
        }

        foreach ($args as $arg) {
            if (strstr($arg, '=') === false) {
                if (in_array('help', ['help', '--help', '-help'])) {
                    $this->help = true;
                }
                continue;
            }
            $pieces = explode('=', $arg);
            if (count($pieces) === 2) {
                $this->addArg($pieces[0], $pieces[1]);
            }
        }
        return count($this->args);
    }

    /**
     * Remove the leading `--` or `-` from a string (in that order).
     *
     * @example $this->removeLeadingDash('--foo'); // returns 'foo'
     * @param string $val
     * @return string
     */
    protected function removeLeadingDash($val)
    {
        if (substr($val, 0, 2) === '--') {
            $val = substr($val, 2);
        } elseif (substr($val, 0, 1) === '-') {
            $val = substr($val, 1);
        }
        return $val;
    }

    /**
     * Add a single argument to the stack.
     *
     * @example $inst->addArg('foo', 'bar');
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addArg($key, $value)
    {
        if ($this->stripDashes === true) {
            $key = $this->removeLeadingDash($key);
        }
        $this->tmp = [];
        $this->args[] = [$key => $value];
        return $this;
    }

    /**
     * Get an array of arrays holding the argument.
     *
     * @example [
     *      ['name' => 'Joe'],
     *      ['age' => '24'],
     *      // etc...
     * ]
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Get the name of the script.
     *
     * @example 'my-script.php'
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Has help been passed as a parameter?
     *
     * @return bool
     */
    public function hasHelp()
    {
        return $this->help;
    }

    /**
     * Remove an argument from the stack.
     *
     * @param string $key
     * @return bool
     */
    public function removeArg($key)
    {
        if ($this->stripDashes === true) {
            $key = $this->removeLeadingDash($key);
        }
        if (array_key_exists($key . '|Argvs|0', $this->tmp)) {
            unset($this->tmp[$key . '|Argvs|0']);
        }
        if (array_key_exists($key . '|Argvs|1', $this->tmp)) {
            unset($this->tmp[$key . '|Argvs|1']);
        }
        $found = false;
        foreach ($this->getArgs() as $n => $argument) {
            if (!array_key_exists($key, $argument)) {
                continue;
            }

            $found = true;
            unset($this->args[$n]);
        }
        return $found;
    }

    /**
     * Get an argument. If multiple matches are found, return an array of the
     * values, otherwise return the string of the value. If none were found,
     * just returns null.
     *
     * @param string $key
     * @param bool $uniqueValues (optional) whether to remove duplicate values
     * @return string|array|null
     */
    public function getArg($key, $uniqueValues = false)
    {
        if ($this->stripDashes === true) {
            $key = $this->removeLeadingDash($key);
        }

        $cacheKey = $key . '|Argvs|1';
        if ($uniqueValues === true) {
            $cacheKey = $key . '|Argvs|0';
        }
        if (array_key_exists($cacheKey, $this->tmp)) {
            return $this->tmp[$cacheKey];
        }
        $matches = [];
        foreach ($this->getArgs() as $n => $argument) {
            if (!array_key_exists($key, $argument)) {
                continue;
            }

            if ($uniqueValues === false || ($uniqueValues === true && !in_array($argument[$key], $matches))) {
                $matches[] = $argument[$key];
            }
        }
        if (count($matches) === 1) {
            $this->tmp[$cacheKey] = $matches[0];
            return $matches[0];
        } elseif (count($matches) > 1) {
            $this->tmp[$cacheKey] = $matches;
            return $matches;
        }
        $this->tmp[$cacheKey] = null;

        return null;
    }

    /**
     * Reset the instance with default values.
     *
     * @param array|null $args
     * @param int|null $num
     * @param bool $stripLeadingDashes (optional) default false.
     */
    public function clear(array $args = null, $num = null, $stripLeadingDashes = true)
    {
        $this->args = [];
        $this->tmp = [];
        $this->script = null;
        $this->help = null;
        $this->stripDashes = $stripLeadingDashes;
        if (is_array($args) && count($args) === $num) {
            $this->processArguments($args);
        }
    }

    /**
     * Retrieve an instance of this object, passing an array of argument, the
     * number of arguments, and whether `--` and `-` should be removed from
     * the front of the parameter keys.
     *
     * New instances of this object must contain the parameters, whereas when
     * retrieving an existing instance, these should be omitted entirely.
     *
     * @param array|null $args
     * @param int|null $num
     * @param bool $stripLeadingDashes (optional) default true.
     * @return Argvs
     */
    public static function getInstance(array $args = null, $num = null, $stripLeadingDashes = true)
    {
        if (null === static::$_instance) {
            static::$_instance = new static($args, $num, $stripLeadingDashes);
        }
        if (null !== $args && null !== $num) {
            static::$_instance->clear($args, $num, $stripLeadingDashes);
        }
        return static::$_instance;
    }
}
