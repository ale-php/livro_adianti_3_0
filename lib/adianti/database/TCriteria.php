<?php
namespace Adianti\Database;

use Adianti\Database\TExpression;

/**
 * Provides an interface for filtering criteria definition
 *
 * @version    2.0
 * @package    database
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCriteria extends TExpression
{
    private $expressions;  // store the list of expressions
    private $operators;    // store the list of operators
    private $properties;   // criteria properties
    
    /**
     * Constructor Method
     * @author Pablo Dall'Oglio
     */
    public function __construct()
    {
        $this->expressions = array();
        $this->operators   = array();
        
        $this->properties['order']     = '';
        $this->properties['offset']    = 0;
        $this->properties['direction'] = '';
    }

    /**
     * When clonning criteria
     */    
    function __clone()
    {
        $newExpressions = array();
        foreach ($this->expressions as $key => $value)
        {
            $newExpressions[$key] = clone $value;
        }
        $this->expressions = $newExpressions;
    }
    
    /**
     * Adds a new Expression to the Criteria
     * 
     * @param   $expression  TExpression object
     * @param   $operator    Logic Operator Constant
     * @author               Pablo Dall'Oglio
     */
    public function add(TExpression $expression, $operator = self::AND_OPERATOR)
    {
        // the first time, we don't need a logic operator to concatenate
        if (empty($this->expressions))
        {
            $operator = NULL;
        }
        
        // aggregates the expression to the list of expressions
        $this->expressions[] = $expression;
        $this->operators[]   = $operator;
    }
    
    /**
     * Return the prepared vars
     */
    public function getPreparedVars()
    {
        $preparedVars = array();
        if (is_array($this->expressions))
        {
            if (count($this->expressions) > 0)
            {
                foreach ($this->expressions as $expression)
                {
                    $preparedVars = array_merge($preparedVars, $expression->getPreparedVars());
                }
                return $preparedVars;
            }
        }
    }
    
    /**
     * Returns the final expression
     * 
     * @param   $prepared Return a prepared expression
     * @return  A string containing the resulting expression
     * @author  Pablo Dall'Oglio
     */
    public function dump( $prepared = FALSE)
    {
        // concatenates the list of expressions
        if (is_array($this->expressions))
        {
            if (count($this->expressions) > 0)
            {
                $result = '';
                foreach ($this->expressions as $i=> $expression)
                {
                    $operator = $this->operators[$i];
                    // concatenates the operator with its respective expression
                    $result .=  $operator. $expression->dump( $prepared ) . ' ';
                }
                $result = trim($result);
                return "({$result})";
            }
        }
    }
    
    /**
     * Define a Criteria property
     * 
     * @param $property Name of the property (limit, offset, order, direction)
     * @param $value    Value for the property
     * @author          Pablo Dall'Oglio
     */
    public function setProperty($property, $value)
    {
        if (isset($value))
        {
            $this->properties[$property] = $value;
        }
        else
        {
            $this->properties[$property] = NULL;
        }
        
    }
    
    /**
     * reset criteria properties
     */
    public function resetProperties()
    {
        $this->properties['limit']  = NULL;
        $this->properties['order']  = NULL;
        $this->properties['offset'] = NULL;
    }
    
    /**
     * Set properties form array
     * @param $properties array of properties
     */
    public function setProperties($properties)
    {
        if (isset($properties['order']) AND $properties['order'])
        {
            $this->properties['order'] = addslashes($properties['order']);
        }
        
        if (isset($properties['offset']) AND $properties['offset'])
        {
            $this->properties['offset'] = (int) $properties['offset'];
        }
        
        if (isset($properties['direction']) AND $properties['direction'])
        {
            $this->properties['direction'] = $properties['direction'];
        }
    }
    
    /**
     * Return a Criteria property
     * 
     * @param $property Name of the property (LIMIT, OFFSET, ORDER)
     * @return          A String containing the property value
     * @author          Pablo Dall'Oglio
     */
    public function getProperty($property)
    {
        if (isset($this->properties[$property]))
        {
            return $this->properties[$property];
        }
    }
}
