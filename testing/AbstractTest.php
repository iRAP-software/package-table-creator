<?php

/* 
 * Abstract test that all tests should extend.
 */

namespace iRAP\TableCreator;

abstract class AbstractTest
{
    protected $m_passed = false;
    protected $m_errorMessage = "";
    
    /**
     * Clean the database by removing all tables.
     * Code taken from 
     * https://stackoverflow.com/questions/3493253/how-to-drop-all-tables-in-database-without-dropping-the-database-itself
     */
    private function cleanDatabase($mysqli)
    {
        
        $mysqli->query('SET foreign_key_checks = 0');
        
        $result = $mysqli->query("SHOW TABLES");
        
        if ($result !== FALSE)
        {
            while ($row = $result->fetch_array(MYSQLI_NUM))
            {
                $mysqli->query('DROP TABLE IF EXISTS ' . $row[0]);
            }
        }
        else 
        {
            throw new Exception("Failed to fetch tables from database for cleanup.");
        }
        
        $mysqli->query('SET foreign_key_checks = 1');
        $mysqli->close();
    }
    
    
    /**
     * Run the test.
     * If any exception is thrown then the test is considered a failure.
     */
    protected abstract function test(\mysqli $mysqli);
    
    
    
    public function run()
    {
        $mysqli = new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->cleanDatabase($mysqli);
        
        try
        {
            $this->test($mysqli);
        } 
        catch (Exception $ex) 
        {
            $this->m_passed = false;
            $this->m_errorMessage = $ex->getMessage();
        }
        
        $mysqli->close();
    }
    
    # Accessors
    public final function getPassed() { return $this->m_passed; }
    public final function getErrorMessage() { return $this->m_errorMessage; }
}

