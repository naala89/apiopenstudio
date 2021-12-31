Creating processors
===================

Processors can essentially be seen as a function (in functional programming) or
semantic block in programming. They are used by resources. They are completely
abstracted from the actual resources themselves. All processors care about is
inputs, processing and output.

They do not need to know where the inputs are coming from. They will validate
the inputs against their requirements, and if the input is invalid, then an
ApiException will be thrown.

All processors, whether they are endpoint, output, processing or security
processors must extend ```ApiOpenStudio\Core\ProcessorEntity``` or a child of
this base class.

Attributes
----------

There is only one mandatory attribute.

### $details

The attribute is an array with the following indexes:

* ```name```
    * Human-readable name.
* ```machineName```
    * Machine-readable name.
        * This must match the class and filename, and be in snake-case while the
          class/file names must be in camelcase.
        * e.g.
            * File name: ExampleProcessor.php
            * Class name: ExampleProcessor
            * Machine name: example_processor
* ```description```
    * Human-readable description of the processor and what it does, plus any
      special notes required.
* ```menu```
    * The category heading in the processor list, under which it will appear.
* ```conditional``` (optional (default: ```false```))
    * This is used in rare cases.
    * Most processors will allow the core TreeParser to use depth-first
      iteration: each input will be calculated to its final return value, before
      process the current (this) processor.
    * But if ```'contitional'``` is set to ```true```: TreeParser will calculate
      all inputs not defined as ```'contitional'```, and return the metadata of
      the result branch, instead of the final calculated result. e.g.
          ```return $this->meta->{input_name}```
    * EXAMPLE: if_then_else processor.
* ```input```
    * an array of inputs

#### input

Each input in the input array require s the following key/value pairs:

* ```description```
    * Human-readable description of the input.
* ```cardinality```
    * The cardinality of values to this input, e.g.:
        * ```[0, 1]``` - 0 or 1
        * ```[0,'*']``` - 0 to many.
        * ```[1, '*']``` - 1 to many.
        * ```[1, 1]``` - 1.
        * ```[2, 5]``` - 2 to 5. literalAllowed
    * Allow a literal input. If set to false, the input must always come from a
      processor. This is a security feature, to prevent developers hard-coding
      values.
* ```limitProcessors```
    * Limit the inputs to specific processors.
* ```limitTypes```
    * Limit the input type, e.g. to text, integer or float.
* ```limitValues```
    * Limit the final value of an input to a set literal values.
* ```conditional``` (optional)
   * Only used if ```conditional``` is set to true in the main processor
     definition (see above).
   * If set to true, this input will not be pre-calculated before processing,
     but will only be calculated if the result of logic dictates it should be
     processed (all other conditional branches will  be discarded and not
     processed)/
* ```default```
    * provide a default value if no input is received.

Example:

    /**
     * {@inheritDoc}
     */
    protected array $details = [
        'name' => 'Example processor',
        'machineName' => 'example_processor',
        'description' => 'This is for an example detail.',
        'menu' => 'Example',
        'input' => [
            'input_1' => [
                'description' => 'Input value 1.',
                'cardinality' => [1, 1],
                'literalAllowed' => true,
                'limitProcessors' => ['var_request'],
                'limitTypes' => ['text'],
                'limitValues' => ['get', 'put'],
                'default' => 'get',
            ],
        ],
    ];

### Methods

The only required method is public ```process()```.

This takes no input in its contract.

You should always log the start of the processor processing and the processor
name. Because there are potentially a lot of processors in a resource metadata
tree, this helps developrs debug and find where an issue is occurring:

    $this->logger->info('api', Processor: ' . $this->details()['machineName']);

It will fetch its values defined in the $details attribute, using the
```val()``` function.

The ```val()``` function can return the real value, or a ```DataContainer```. In
general, you will want to use the real value within the ```process()```
function. e.g.

    $input1 = $this->val('input_1`, true);

The output must always be a ```ApiOpenStudio\Core\DataContainer```. This ensures
a consistent value is always pased between processors.

Throwing Exceptions
-------------------

In order to fail gracefully, processors should trap all errors and throw
an ```ApiOpenStudio\Core\ApiException```. This will because by the core
prcessing code, and its message return in the result with the defined error
code, HTML reponse code and processor ID. e.g.

    if (false) {
        throw new ApiException("I could not process this input.", 6, $this->id, 400);
    }

Logging
-------

ApiOpenStudio uses Monolog for it's logging, and there are several levels of
debugging:

* ```DEBUG```: Detailed debugging information.
* ```INFO```: Handles normal events. Example: SQL logs
* ```NOTICE```: Handles normal events, but with more important events
* ```WARNING```: Warning status, where you should take an action before it will
  become an error.
* ```ERROR```: Error status, where something is wrong and needs your immediate action
* ```CRITICAL```: Critical status. Example: System component is not available
* ```ALERT```: Immediate action should be exercised. This should trigger some
  alerts and wake you up at night.
* ```EMERGENCY```: It is used when the system is unusable.

The logger is available as an attribute in all processors:

    $this->logger->info('api', My message');

Database
--------

ApiOpenStudio uses a DB mapper design pattern.

Use a table related mapper (e.g. ```ApiOpenStudio\Db\AccountMapper``` to make
requests against the Account table). Call one of the predefined methods in the
mapper to make the SQL call. This will return an object, representing the row or
rows that are the result of your query.

e.g.

    $accountMapper = new AccountMapper($this->db);
    $account = $accountMapper->findByName($name);
    $account_id = $account->getAccid();
