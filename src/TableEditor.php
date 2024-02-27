<?php

/* 
 * Class for editing MySQL tables. Especially useful for migrations.
 */


namespace iRAP\TableCreator;


use Exception;
use mysqli;

class TableEditor
{
    private string $m_name; # The name of this table

    private mysqli $m_mysqliConn; # the mysqli connection to the database.
    
    
    const ENGINE_INNODB = 'INNODB';
    const ENGINE_MYISAM = 'MYISAM';


    /**
     * Create a table editor.
     * @param mysqli $mysqliConn - the connection to the database.
     * @param string $name - the name of the table that is being edited.
     */
    public function __construct(mysqli $mysqliConn, string $name)
    {
        $this->m_mysqliConn = $mysqliConn;
        $this->m_name = $name;
    }


    /**
     * Adds the specified fields to this table.
     * @param array $fields - an array of DatabaseField objects
     * @return void
     * @throws Exception - if one of the fields being added is set to primary key. In such a case, the developer should
     * call the changePrimaryKey method instead.
     */
    public function addFields(array $fields) : void
    {
        $addFields = [];

        foreach ($fields as $field)
        {
            $name = $field->getName();
            $addFields[$name] = $field; # we key by name to prevent duplicates!
        }
        
        $keysString = "";
        $fieldStrings = array();
        
        foreach ($addFields as $fieldName => $field)
        {
            $fieldStrings[] = $field->getFieldString();
                
            /* @var $field DatabaseField */
            if ($field->isPrimaryKey())
            {
                $errMsg = 'Do not set field to be primary key when adding fields. ' .
                          'Instead, use the changePrimaryKey method';
                throw new Exception($errMsg);
            }
            elseif ($field->isKey())
            {
                if ($keysString !== "")
                {
                    $keysString .= ", ";
                }
                
                if ($field->isUnique())
                {
                    $keysString .= "UNIQUE ";
                }
                
                $keysString .= "KEY (`" . $fieldName . "`) ";
            }
        }
        
        $fieldsString = implode(", ", $fieldStrings);
        $fieldsString .= $keysString;
        $query = "ALTER TABLE `{$this->m_name}` ADD ({$fieldsString})";
        $this->m_mysqliConn->query($query);
    }
    
    
    /**
     * Adds the DatabaseField object to this table.
     * @param string $fieldName
     */
    public function removeField(string $fieldName) : void
    {
        $query = "ALTER TABLE `{$this->m_name}` DROP COLUMN `{$fieldName}`";
        $this->m_mysqliConn->query($query);
    }
    
    
    /**
     * Adds the DatabaseField object to this table.
     * @param array<String> $fields
     */
    public function removeFields(array $fields) : void
    {
        foreach ($fields as $fieldName)
        {
            $this->removeField($fieldName);
        }
    }
    
    
    /**
     * Remove a key from the database. This does not remove the field itself.
     * @param string|array $key - the name(s) of the field(s) that currently act as the key that we wish to remove.
     */
    public function removeKey(string|array $key) : void
    {
        if (is_array($key))
        {
            $keyString = "(" . implode(',', $key) . ")";
        }
        else
        {
            $keyString = "`" . $key . "`";
        }
        
        $query = "ALTER TABLE `{$this->m_name}` DROP INDEX {$keyString}";
        $this->m_mysqliConn->query($query);
    }


    /**
     * Given a list of $keys, this adds each one as a key. A key can be a field name or an array
     *  of field names which represents a combined-key.
     * @param array $keys - array of field names or arrays that represent a combined-key
     * @param bool $unique - whether these keys are to be unique.
     * @return void
     */
    public function addKeys(array $keys, bool $unique=false) : void
    {
        foreach ($keys as $key)
        {
            $this->addKey($key, $unique);
        }
    }
    
    
    /**
     * Adds a key to the table. This is NOT for primary keys.
     * @param mixed $key - String representing the field name to act as key, or array of field names for a single
     * "combined" key.
     * @param bool $unique - optionally set to true to make this a unique key.
     * @return void.
     */
    public function addKey(string|array $key, bool $unique=false) : void
    {
        if (is_array($key))
        {
            $keyString = implode(",", $key);
        }
        else
        {
            $keyString = "`" . $key . "`";
        }
        
        $uniqueString = "";
        
        if ($unique)
        {
            $uniqueString = "UNIQUE";
        }
        
        $query = "ALTER TABLE `{$this->m_name}` ADD {$uniqueString} KEY({$keyString})";
        $this->m_mysqliConn->query($query);
    }
    
    
    
    /**
     * Change the primary key to something else (you can only have one primary key, although it
     * can be made up of multiple fields).
     * @param mixed $newPrimary - string field name or array of field names that make up a combined
     *                            primary key.
     */
    public function changePrimaryKey(string|array $newPrimary) : void
    {
        if (is_array($newPrimary))
        {
            $primaryKeyString = "(" . implode(",", $newPrimary) . ")";
        }
        else
        {
            $primaryKeyString = "(`" . $newPrimary . "`)";
        }
        
        $query = 
            "ALTER TABLE `" . $this->m_name . "` " .
            "DROP PRIMARY KEY, ADD PRIMARY KEY " . $primaryKeyString;
        
        $this->m_mysqliConn->query($query);
    }
    
    
    /**
     * Change the tables engine to something else. Please make use of the classes constants
     * when providing the engine parameter.
     * @param String $engine - the name of the engine to change to.
     * @throws Exception - if the query failed
     */
    public function changeEngine(string $engine) : void
    {
        $allowed_engines = array(
            self::ENGINE_INNODB,
            self::ENGINE_MYISAM
        );
        
        if (!in_array($engine, $allowed_engines))
        {
            throw new Exception('Unrecognized engine: ' . $engine);
        }
        
        $query = "ALTER TABLE `{$this->m_name}` ENGINE={$engine}";
        $this->m_mysqliConn->query($query);
    }
}
