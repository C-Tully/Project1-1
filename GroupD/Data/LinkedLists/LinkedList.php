<?php
/**
 * This file is part of the GroupD\Data\LinkedLists package.
 *
 * For full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GroupD\Data\LinkedLists;

use \Data\ILinkedNode;
require_once '../LinkedNode.php';
require_once '../Iterator.php';
require_once '../../../Data/LinkedLists/ILinkedList.php';

/**
 * LinkedList is the class which represents the LinkedList
 * 
 * @author Tom Eastwood, Josh Parons, Nick Hiebert, Chris Tully
 * @package Data\LinkedLists
 * @version 1.0.0
 */


/*
use do while and while loops instead of iteratot foreach
 */


class LinkedList implements \Data\LinkedLists\ILinkedList
{   
    /**
     * a variable representing the first node in the list
     * 
     * @access private
     */
    private $f_node;

    /**
     * a variable representing the last node
     *
     * @access private
     */
    private $l_node;

    /**
     * a variable representing the count
     * @access private
     */
    private $count = 0;

    /**
     * Returns the first element in the list.
     *
     * @access public
     * @return ILinkedNode|null Returns the first ILinkedNode in the list, otherwise returns NULL.
     */
    public function getFirst()
    {
        if (isset($this->f_node) ) 
        {
            return $this->f_node;
        }

        return NULL;
    }
    
    /**
     * Returns the last element in the list.
     *
     * @access public
     * @return ILinkedNode|null Returns the last ILinkedNode in the list, otherwise returns NULL.
     */
    public function getLast()
    {
        if (isset($this->l_node) ) 
        {
            return $this->l_node;
        }

        return NULL;
    }
    
    /**
     * Adds a value onto the end of the list.
     *
     * This method will create a new ILinkedNode instance assigning a
     * numeric key value to the node and the value is assigned to the
     * node's value property.
     *
     * @access public
     * @param mixed $value The value to add.
     * @return int The key value of the node that was created and added.
     */
    public function add($value)
    {
        $node = new \Data\LinkedNode($value);
        $this->addNode($node);
        
        //Return the new nodes key
        return $node->getKey();//Not too sure on the syntax and/or correctness of what i just put here... including it becuase the key needs to be returned ~Josh
    }
    
    /**
     * Adds an ILinkedNode instance onto the end of the list.
     *
     * The node that is to be added to the list should have its key reset so that
     * it is the next key in the list's key sequence.
     *
     * @access public
     * @param ILinkedNode $node The ILinkedNode to add.
     * @return mixed The key value of the node that was added.
     */
    public function addNode(\Data\ILinkedNode $node)
    {
        $node->setKey($this->count);
        ++$this->count;

        if (empty($this->f_node) ) 
        {
            $this->f_node = $node;
            $this->l_node = $node;
            return $node->getKey();
        }
        
        if($this->f_node === $this->l_node)
        {   
            $this->f_node->setNext($node);
            $this->l_node = $node;
            return $node->getKey();    
        }

        $this->l_node->setNext($node);
        $this->l_node = $node;
        return $node->getKey(); 
    }
    
    /**
     * Returns the list as an associative array.
     *
     * The return array will be formatted so that each node within the list
     * will be returned as a key => value representation. 
     *
     * @access public
     * @return array An associative array of key and value pairs for all nodes.
     */
    public function asArray()
    {
        $iterator = $this->getIterator();
        $array = array();

        foreach ($iterator as $key => $value) 
        {
            $array[$key] = $value;
        }

        return $array;
    }
    
    /**
     * Checks if the list contains a node with the specified key value.
     *
     * @access public
     * @param mixed $key Contains the key value to search for.
     * @return bool Returns true if the $key was found, otherwise returns false.
     */
    public function containsKey($key)
    {   
        $iterator = $this->getIterator();

        foreach ($iterator as $_key => $value) 
        {
            if ($key == $_key) 
            {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Checks if the list contains a node with the specified value.
     *
     * @access public
     * @param mixed $value Contains the value to search for.
     * @return bool Returns true if the $value was found, otherwise returns false.
     */
    public function contains($value)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $key => $_value) 
        {
            if ($value == $_value) 
            {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Returns the ILinkedNode object by the specified value.
     * 
     * @access public
     * @param mixed $value Contains the value to find.
     * @return ILinkedNode|null Returns the first ILinkedNode that contains the value, otherwise null.
     */
    public function find($value)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $key => $_value) 
        {
            if ($_value== $value) 
            {
                return $iterator->currentNode();
            }
        }

        return NULL;
    }
    
    /**
     * Returns an array of all ILinkedNodes found by the specified value.
     *
     * @access public
     * @param mixed $value Contains the value to find.
     * @return array|null Returns an array with all the ILinkedNode instances whose value is equal to $value, otherwise returns null.
     */
    public function findAll($value)
    {
        $iterator = $this->getIterator();
        $values = array();

        foreach ($iterator as $key => $_value) 
        {
            if ($_value == $value) 
            {
                $values[] = $iterator->currentNode();
            }
        }

        if (isset($values) ) 
        {
            return $values;
        }
        return NULL;
    }
    
    /**
     * Returns the first ILinkedNode instance found by with the specified value.
     * 
     * @access public
     * @param mixed $value
     */
    public function findFirst($value)
    {
        $this->find($value);
    }
    
    /**
     * Returns the last ILinkedNode instance found by the specified value.
     *
     * The searching operations for this method are in reverse, therefore starting at the
     * bottom of the list. This is done so on purpose to reduce unneeded overhead.
     *
     * @access public
     * @param mixed $value Contains the value to find.
     * @return ILinkedNode|null Returns the last ILinkedNode that contains the value, otherwise null if none found.
     */
    public function findLast($value)
    {
        //this variable's value will keep changing whenever we find a node with that particular value
        //it will store the most recent node the iterator has come across
        $last;
        $iterator = $this->getIterator();

        foreach ($iterator as $key => $_value) 
        {
            if ($_value== $value) 
            {
                $last = $iterator->currentNode();
            }
        }

        if (isset($last)) 
        {
            return $last;
        }
        return NULL;

    }
    
    /**
     * Returns the ILinkedNode at the specified $key.
     *
     * @access public
     * @param mixed Contains the key of the ILinkedNode to get.
     * @return mixed Returns the ILinkedNode at $key if found, otherwise null.
     */
    public function get($key)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $_key => $value) 
        {
            if ($_key == $key) 
            {
                return $iterator->currentNode();
            }
        }

        return NULL;

    }
    
    /**
     * Inserts a new ILinkedNode at before the specified key.
     *
     * The ILinkedNode instance is created within this method. When inserting, all nodes should
     * be shifted and key values shifted as well for all nodes that follow this newly inserted.
     * Additionally, when inserting a new ILinkedNode, the key will be automatically generated as the
     * next numeric value in the sequence of nodes.
     *
     * @access public
     * @param int $before Contains the key value to insert a new ILinkedNode before.
     * @param mixed $value Contains the value used to create a new ILinkedNode with and inserted before $before.
     * @return int Returns the newly create ILinkedNode's key.
     */
    public function insertBefore($before, $value)
    {
        if($this->containsKey($before) )
        {
            //just shift the values!!!!
            //1. create new node!
            //2. put that new node onto the end
            //3.look $key+1 < count
            //4. node @ $i -> value = node @ $i-
            
            /*
             *LOGIC USED
             *1:Add new node
             *2: for loop structure. i gets the total count, decrements as long as i is greater than the key of the node we're inserting into.
             *3: set the value for the current node i (note: first one is empty) to be the value of the one before it.
             *
             *Using this we eliminate the need for a temp variable and thus just assign the values, no need to change the keys.
             *
             *~Josh
             */
            
            //adding a new node onto the end of the LinkedList with a NULL value
            $this->add(NULL);
            

            for ($i = $this->count; $i < $this->before; $i--) 
            { 
                $this->get($i)->setValue($this->get($i-1)->getValue() ); 
            }

            $this->get($before)->setValue($value);

        }

        throw new invalidArgumentException("The key you're looking for does not exists!");
        
    }
    
    /**
     * Inserts a new ILinkedNode after the specified key.
     *
     * The ILinkedNode instance is created within this method. When inserting, all nodes that are
     * to follow (come after) this node should be shifted and key values shifted as well.
     * Additionally, when inserting a new ILinkedNode, the key will be automatically generated
     * the next numeric value in the sequence of nodes.
     *
     * @access public
     * @param int $after Contains the key value to insert a new ILinkedNode after.
     * @param mixed $value Contains the value used to create a new ILinkedNode with and inserted before $after.
     * @return int Returns the newly create ILinkedNode's key.
     */
    public function insertAfter($after, $value)
    {

    }
    
    /**
     * Returns a boolean to represent whether or not this list is empty.
     *
     * @access public
     * @return bool Returns true if the list is empty, otherwise returns false.
     */
    public function isEmpty()
    {
        if ($f_node == null) 
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns, but does not remove, the first node in the list. 
     *
     * @access public
     * @return ILinkedNode|null Returns the first node in the list. Will returns NULL if the list empty.
     */
    public function peek()
    {
        return $this->getFirst();
    }
    
    /**
     * Returns, but does not remove, the first node in the list. 
     *
     * @access public
     * @return ILinkedNode|null Returns the first node in the list. Will returns NULL if the list empty.
     */
    public function peekFirst()
    {
        return $this->getFirst();
    }
    
    /**
     * Returns, but does not remove, the last node in the list. 
     *
     * @access public
     * @return ILinkedNode|null Returns the last node in the list. Will returns NULL if the list empty.
     */
    public function peekLast()
    {
        return $this->getLast();
    }
    
    /**
     * Returns and removes the first node in the list.
     *
     * @access public
     * @return ILinkedNode|null Returns the first node in the list. Will return NULL if the list is empty.
     */
    public function poll()
    {
        if ($this->isEmpty() ) 
        {
            return NULL;
        }
        if ($this->f_node === $this->l_node) 
        {
            $return = $this->f_node;
            unset($this->f_node);
            //return NULL;//should not be null ~Josh 26/02/13
            return $return;
        }
        //$this->f_node->getNext() = $this->f_node;//Not sure why, but we can't use this method here.
        $this->f_node->setNext($this->f_node);//i put this here. im not sure if it's correct. but i think this is what you were trying to do? ~Josh
        $return = $this->f_node;//assign the first node to the return variable
        unset($this->f_node);//unset first
        --$this->count;
        //I'm just returning the new first node, not sure if we should return the new first node of the old first node
        return $return;//return the first node (contained in $return). 
    }
    
    /**
     * Returns and removes the first node in the list.
     *
     * @access public
     * @return ILinkedNode|null Returns the first node in the list. Will return NULL if the list is empty.
     */
    public function pollFirst()
    {
        $this->poll();
    }
    
    /**
     * Returns and removes the last node in the list.
     *
     * @access public
     * @return ILinkedNode|null Returns the last node in the list. Will return NULL if the list is empty.
     */
    public function pollLast()
    {   
        if ($this->isEmpty() ) 
        {
            return NULL;
        }
        if ($this->f_node === $this->l_node) 
        {
            $return = $this->f_node;
            unset($this->f_node);
            //return NULL;//this function never returns null unless called on an empty list. which will never exist. ~Josh 26/02/13
            return $return;//method always returns a node, never null. the first if case is just a precaution. ~Josh 26/02/13
        }

        //grabbing the second to last node in the list based upon key
        $second_l_key = $this->l_node->getKey() - 1;

        //assinging that node to be the new last node based upon its key
        //$this->get($second_l_key) = $this->l_node;//cant use method return value error. not sure what to do ~Josh
        $this->l_node = $this->get($second_l_key);//switched around the two variables. same effect, no error ~Josh 26/02/13
        $this->l_node->clearNext();

        --$this->count;
        return $this->l_node;
    }
    
    /**
     * Returns the last node's value and removes the last node in the list.
     *
     * @access public
     * @return mixed Returns the last node value in the list. Will return NULL if the list empty.
     */
    public function pop()
    {
        if ($this->f_node === $this->l_node) 
        {
            $return = $this->l_node->getValue();
            unset($this->f_node);
           // return NULL;//should be the value of l_node, not null ~Josh 26/02/13
           return $return;
           
        }

        //grabbing the second to last node in the list based upon key
        $second_l_key = $this->l_node->getKey() - 1;

        //assinging that node to be the new last node based upon its key
        //$this->get($second_l_key) = $this->l_node;//can't use method return value -  can't use '=' which means we need a set Function? ~Josh
        $this->l_node = $this->get($second_l_key);
        $this->l_node->clearNext();//clears the next value seeing as how it is now the last node in the list ~Josh 26/02/13

        --$this->count;
        return $this->l_node->getValue();
    }
    
    /**
     * Adds a new value onto the end of the list.
     *
     * A new ILinkedNode instance will be created and the value assigned to the specified. A numeric
     * key will be created based on the sequence (last numeric key + 1) and assigned to this node.
     *
     * @access public
     * @param mixed Contains the value to push onto the list.
     */
    public function push($value)
    {
        $this->add($value);
    }
    
    /**
     * Removes all nodes whose value is equal to that specified.
     *
     * Will remove all nodes within the list in addition to shifting and adjusting their
     * keys, for those that are within a numeric sequence.
     *
     * @access public
     * @param mixed Contains the value to remove.
     */
    public function remove($value)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $key => $_value) 
        {
            if ($_value == $value) 
            {
               // unset($iterator->currentNode() );//error with unsetting ~Josh
            }
        }

        --$this->count;
    }
    
     /**
     * Removes the node that lives at the specified key.
     *
     * Will remove the node at $key within the list in addition to shifting and adjusting the keys for
     * remaining nodes that follow the removed.
     *
     * @access public
     * @param mixed Contains the value to remove.
     */
    public function removeAt($key)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $_key => $value) 
        {
            if ($_key == $key) 
            {
                //unset($iterator->currentNode() );//Can't use return value... ~Josh
            }
        }

        --$this->count;
    }
    
    /**
     * Removes the first node from the list.
     *
     * @access public
     */
    public function removeFirst()
    {
        $this->poll();
    }
    
    /**
     * Removes the last node from the list.
     * 
     * @access public
     */
    public function removeLast()
    {
        $this->pollLast();
    }
    
    /**
     * Removes the specified node from the list.
     *
     * If the node exists, it will be removed and all nodes that follow will be shifted and their keys
     * will be adjusted.
     *
     * @access public
     * @param ILinkedNode $node The node to remove from the list.
     */
    public function removeNode(\Data\ILinkedNode $node)
    {
        $iterator = $this->getIterator();

        foreach ($iterator as $_key => $value) 
        {
            if ($iterator->currentNode() == $node) 
            {
                //unset($iterator->currentNode() );//can't use method return value ~Josh
            }
        }

        --$this->count;
    }
    
    /**
     * Sorts the list by the node values.
     *
     * The keys of all moved nodes will be adjusted so that the numeric key sequence
     * remains (n - 1) + 1 for all nodes.
     *
     * @access public
     */
    public function sort()
    {

    }
    
    /**
     * Sorts the list by using a callback to specify the value to compare on.
     *
     * The callback should take one parameter of type ILinkedNode and return a single
     * value that will be used for comparison.
     *
     * @access public
     * @param callable The specified callback.
     */
    public function sortBy(callable $predicate)
    {

    }

    public function count()
    {
        return $this->count;
    }

    public function getIterator()
    {
        return new \GroupD\Data\Iterator($this);  
    }

}