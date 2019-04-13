<?php
namespace Argvs;

interface ArgvInterface
{
    /**
     * Add a single argument to the stack.
     *
     * @example $inst->addArg('foo', 'bar');
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addArg($key, $value);

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
    public function getArgs();

    /**
     * Get the name of the script.
     *
     * @example 'my-script.php'
     * @return string
     */
    public function getScript();

    /**
     * Has the parameter of `--help`, `-help` or just `help` been passed
     * into the parameters?
     *
     * @return bool
     */
    public function hasHelp();

    /**
     * Get an argument. If multiple matches are found, return an array of the
     * values, otherwise return the string of the value. If none were found,
     * just returns null.
     *
     * @param string $key
     * @param bool $uniqueValues (optional) whether to remove duplicate values
     * @return string|array|null
     */
    public function getArg($key, $uniqueValues = false);

    /**
     * Reset the instance with default values.
     *
     * @param array|null $args
     * @param int|null $num
     * @param bool $stripLeadingDashes (optional) default false.
     */
    public function clear(array $args = null, $num = null, $stripLeadingDashes = true);

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
    public static function getInstance(array $args = null, $num = null, $stripLeadingDashes = true);
}
