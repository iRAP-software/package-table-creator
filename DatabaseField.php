<?php

/*
 * Class to represent a single field/column in a MySQL database.
 * This is mainly used in the generation of tables.
 */

namespace iRAP\TableCreator;


class DatabaseField
{
    private string $m_name; # e.g. the column name.
    private string $m_type;
    private mixed $m_default = null; # This is very different to 'NULL'
    private bool $m_autoIncrementing = false;
    private mixed $m_constraint = null;
    private bool $m_allowNull = false;
    private bool $m_isKey = false;
    private bool $m_isUnique = false; # only affects if is_key set to true
    private bool $m_isPrimary = false; # only affects if is_key set to true
    
    
    # Specify the types for easy creation (prevent typos/mistakes)
    const TYPE_CHAR      = 'CHAR';
    const TYPE_VARCHAR   = 'VARCHAR';
    const TYPE_TINYTEXT  = 'TINYTEXT';
    const TYPE_TEXT      = 'TEXT';
    const TYPE_LONGTEXT  = 'LONGTEXT';
    const TYPE_INT       = 'INT';
    const TYPE_DECIMAL   = 'DECIMAL';
    const TYPE_TINY_INT  = "tinyint";
    const TYPE_DATE      = "DATE";
    const TYPE_TIMESTAMP = "TIMESTAMP";
    
    # Spatial types
    const TYPE_POINT               = "POINT";
    const TYPE_LINESTRING          = "LINESTRING";
    const TYPE_POLYGON             = "POLYGON";
    const TYPE_MULTI_POINT         = "MULTIPOINT";
    const TYPE_MULTI_LINE_STRING   = "MULTILINESTRING";
    const TYPE_MULTI_POLYGON       = "MULTIPOLYGON";
    const TYPE_GEOMETRY_COLLECTION = "GEOMETRYCOLLECTION";
    const TYPE_GEOMETRY            = "GEOMETRY";
    

    /**
     * Private constructor because this object must be created by one of the
     *  various 'factory' static methods.
     * @param string $name - the name of the field/column.
     * @param string $type - the type of field. E.g. "VARCHAR"
     */
    private function __construct(string $name, string $type)
    {
        $this->m_name = $name;
        $this->m_type = $type;
    }


    /**
     * Creates a char field.
     * @param string $name - the name for the field.
     * @param int $size - the number of chars the field should contain.
     * @return DatabaseField
     */
    public static function createChar(string $name, int $size) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_CHAR);
        $field->m_constraint = $size;
        return $field;
    }


    /**
     * Create a VARCHAR field
     * @param string $name - the name to give the field
     * @param int $size - how many characters the field can hold
     * @return DatabaseField
     */
    public static function createVarchar(string $name, int $size) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_VARCHAR);
        $field->m_constraint = $size;
        return $field;
    }


    /**
     * Factory method for creating a boolean (tiny int) type
     * @param string $name - the name of the field/column
     * @return DatabaseField
     */
    public static function createBool(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_TINY_INT);
        $field->m_constraint = 1;
        return $field;
    }


    /**
     * Creates a date field in the database (yyyy-mm-dd).
     * @param string $name
     * @return DatabaseField
     */
    public static function createDate(string $name) : DatabaseField
    {
        return new DatabaseField($name, self::TYPE_DATE);
    }


    /**
     * Factory method for creating a TEXT type database field
     * @param string $name - the name of the field for when it is in the database;
     * @return DatabaseField
     */
    public static function createText(string $name) : DatabaseField
    {
        return new DatabaseField($name, self::TYPE_TEXT);
    }


    /**
     * Factory method for creating a TINYTEXT type database field
     * @param string $name - the name of the field for when it is in the database;
     * @return DatabaseField
     */
    public static function createTinyText(string $name) : DatabaseField
    {
        return new DatabaseField($name, self::TYPE_TINYTEXT);
    }


    /**
     * Factory method for creating a LONG_TEXT type database field
     * @param string $name - the name of the field for when it is in the database;
     * @return DatabaseField
     */
    public static function createLongText(string $name) : DatabaseField
    {
        return new DatabaseField($name, self::TYPE_LONGTEXT);
    }
    

    /**
     * Creates an integer type field.
     * @param string $name
     * @param int $size - the size the int can reach, e.g. 2 means you can reach the number 99
     * @param bool $autoInc - auto increment the field (will mark it as primary key!)
     * @return DatabaseField
     */
    public static function createInt(string $name, int $size, bool $autoInc=false) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_INT);
        $field->m_constraint = $size;
        $field->m_autoIncrementing = $autoInc;
        return $field;
    }


    /**
     * @param string $name - the name of the database field
     * @param string|null $defaultValue - specify a default value for when creating rows. Defining this will prevent
     * the field automatically updating whenever another field in the row changes.
     * @return DatabaseField
     */
    public static function createTimestamp(string $name, ?string $defaultValue = null) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_TIMESTAMP);
        
        if ($defaultValue !== NULL)
        {
            $field->setDefault('CURRENT_TIMESTAMP');
        }
        
        return $field;
    }
    
    
    /**
     * Creates the DECIMAL field type
     * @param string $name - the name of the field/column in the database.
     * @param int $precisionBefore - the precision before the decimal place. 
     *                                e.g. 2 means you can reach the number 99
     * @param int $precisionAfter - precision after the decimal place. 
     *                               e.g. 2 means you can be accurate to 0.01
     * @return DatabaseField
     */
    public static function createDecimal(
        string $name,
        int $precisionBefore,
        int $precisionAfter
    ) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_DECIMAL);
        $field->m_constraint = "{$precisionBefore},{$precisionAfter}";
        return $field;
    }
    
    
    /**
     * Create a POINT field
     * https://mariadb.com/kb/en/mariadb/point/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createPoint(string $name) : DatabaseField
    {
        return new DatabaseField($name, self::TYPE_POINT);
    }
    
    
    /**
     * Create a LineString field
     * https://mariadb.com/kb/en/mariadb/linestring/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createLineString(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_LINESTRING);
        return $field;
    }
    
    /**
     * Create a Polygon field
     * https://mariadb.com/kb/en/mariadb/polygon/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createPolygon(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_POLYGON);
        return $field;
    }
    
    
    /**
     * Create a MultiPoint field
     * https://mariadb.com/kb/en/mariadb/multipoint/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createMultiPoint(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_MULTI_POINT);
        return $field;
    }
    
    
    /**
     * Create a MultiPoint field
     * https://mariadb.com/kb/en/mariadb/multilinestring/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createMultiLineString(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_MULTI_LINE_STRING);
        return $field;
    }
    
    
    /**
     * Create a MultiPoint field
     * https://mariadb.com/kb/en/mariadb/multipolygon/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createMultiPolygon(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_MULTI_POLYGON);
        return $field;
    }
    
    
    /**
     * Create a GeometryCollection field
     * https://mariadb.com/kb/en/mariadb/geometrycollection/
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createGeometryCollection(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_GEOMETRY_COLLECTION);
        return $field;
    }
    
    
    /**
     * Create a geometry field
     * https://mariadb.com/kb/en/mariadb/geometry-types/#geometrygeometry
     * @param string $name - the name to give the field/column
     * @return DatabaseField
     */
    public static function createGeometry(string $name) : DatabaseField
    {
        $field = new DatabaseField($name, self::TYPE_GEOMETRY);
        return $field;
    }
    
    
    
    
    /**
     * Specify that this field is allowed to be null.
     * @return void
     */
    public function setAllowNull() : void
    {
        $this->m_allowNull = true;
    }
    
    
    /**
     * Disables the ability to set null in the database for this field
     * Just in case someone uses prototypes for rapid creation on one that has 
     * null enabled and then wants to disable on certain fields.
     */
    public function disableNull() : void
    {
        $this->m_allowNull = true;
        
        if ($this->m_default == 'NULL')
        {
            $this->m_default = null; # unset it
        }
    }
    
    
    /**
     * Specify that this field acts as a key (but is not a primary key)
     * @param bool $unique - optionally set to true to make this a UNIQUE key.
     */
    public function setKey(bool $unique=false) : void
    {
        $this->m_isKey = true;
        $this->m_isUnique = $unique;
    }
    
    
    /**
     * Sets the default for this field.
     * @param mixed $default - the default value for this field in the database
     */
    public function setDefault(mixed $default) : void
    {
        $this->m_default = $default;
        
        if (strtoupper($default) === 'NULL')
        {
            $this->m_allowNull = true;
            $this->m_default = 'NULL';
        }
    }
    
    
    /**
     * Returns the text representing this field's definition inside a "CREATE TABLE" statement.
     * @return string - the string for defining this field in a mysql table.
     */
    public function getFieldString() : string
    {
        $fieldString = "`" . $this->m_name . "` " . $this->m_type;
        
        if ($this->m_constraint != null)
        {
            $fieldString .= " (" . $this->m_constraint . ")";
        }
                
        if ($this->isAutoIncrementing())
        {
            $fieldString .= " AUTO_INCREMENT";
        }
        
        if ($this->m_default !== null)
        {
            $fieldString .= " DEFAULT " . $this->m_default;
        }
        
        if (!$this->m_allowNull)
        {
            $fieldString .= " NOT NULL";
        }
        
        return $fieldString;
    }
    

    # Accessors
    public function getName() : string { return $this->m_name; }
    public function getType() : string { return $this->m_type; }
    public function getDefault() : mixed { return $this->m_default; }
    public function getConstraint() : mixed { return $this->m_constraint; }
    public function getAllowNull() : bool { return $this->m_allowNull; }
    public function isKey() : bool { return $this->m_isKey; }
    public function isPrimaryKey() : bool { return $this->m_isPrimary; }
    public function isUnique() : bool { return $this->m_isUnique; }
    public function isAutoIncrementing() : bool { return $this->m_autoIncrementing; }
}